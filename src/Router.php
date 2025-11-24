<?php

namespace App;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];
    private array $rutasProtegidas = [];
    private bool $csrfProtection = true;

    public function __construct()
    {
        $this->iniciarSesion();
    }

    private function iniciarSesion()
    {
        // Si ya hay una sesión activa, no hacer nada
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Siempre iniciar sesión si hay una cookie de sesión existente
        if (isset($_COOKIE['lost_nexus'])) {
            session_name('lost_nexus');
            session_start();
            
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return;
        }

        $currentUrl = $_SERVER['PATH_INFO'] ?? strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Rutas que NO necesitan sesión automáticamente (GET)
        $rutasSinSesion = [
            '/login',
            '/'
        ];

        // Rutas que SIEMPRE necesitan sesión (excepto login GET)
        $rutasConSesion = [
            '/logout',
            '/admin/usuarios',
            '/objetosperdidos',
            '/login/crear',
            '/login/listar',
            '/login/editar'
        ];

        // Iniciar sesión para:
        // 1. Cualquier método POST
        // 2. Rutas específicas que necesitan sesión
        // 3. Rutas protegidas (excepto login GET)
        // 4. NO iniciar para login GET y raíz
        
        $necesitaSesion = 
            ($method === 'POST') ||
            in_array($currentUrl, $rutasConSesion) ||
            ($this->rutaEstaProtegida($method, $currentUrl) && !($currentUrl === '/login' && $method === 'GET'));

        if ($necesitaSesion) {
            session_name('lost_nexus');
            session_start();
            
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    }

    public function get($url, $fn, $protegida = false)
    {
        $this->getRoutes[$url] = $fn;
        if ($protegida) {
            $this->rutasProtegidas['GET'][$url] = true;
        }
    }

    public function post($url, $fn, $protegida = false)
    {
        $this->postRoutes[$url] = $fn;
        if ($protegida) {
            $this->rutasProtegidas['POST'][$url] = true;
        }
    }

    public function estaAutenticado()
    {
        // Verifica si hay una sesión activa con nuestro nombre
        if (session_status() === PHP_SESSION_ACTIVE && session_name() === 'lost_nexus') {
            return isset($_SESSION['login']) && $_SESSION['login'] === true;
        }
        return false;
    }

    protected function rutaEstaProtegida(string $method, string $currentUrl): bool
    {
        // Verificar rutas protegidas exactas
        if (isset($this->rutasProtegidas[$method][$currentUrl])) {
            return true;
        }

        // Verificar rutas con parámetros
        foreach ($this->rutasProtegidas[$method] ?? [] as $route => $isProtected) {
            if (strpos($route, '{') !== false) {
                $pattern = "@^" . preg_replace('/\{[^}]+\}/', '([^/]+)', $route) . "$@";
                if (preg_match($pattern, $currentUrl)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function comprobarRutas()
    {
        try {
            $currentUrl = $_SERVER['PATH_INFO'] ?? strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
            $method = $_SERVER['REQUEST_METHOD'];

            // Verificar autenticación para rutas protegidas
            if ($this->rutaEstaProtegida($method, $currentUrl)) {
                if (!$this->estaAutenticado()) {
                    // Permitir acceso a login sin autenticación solo para GET
                    if ($currentUrl === '/login' && $method === 'GET') {
                        // Continuar normalmente al login
                    } else {
                        header('Location: /login');
                        exit;
                    }
                }
            }

            // Validar CSRF para POST en rutas protegidas
            if ($method === 'POST' && $this->rutaEstaProtegida($method, $currentUrl)) {
                $this->validarCSRF();
            }

            $routes = ($method === 'GET') ? $this->getRoutes : $this->postRoutes;

            foreach ($routes as $route => $fn) {
                if (strpos($route, '{') !== false) {
                    $pattern = "@^" . preg_replace('/\{([^\}]+)\}/', '([^/]+)', $route) . "$@";
                    if (preg_match($pattern, $currentUrl, $matches)) {
                        array_shift($matches);
                        
                        // Manejar métodos estáticos y de instancia
                        $this->ejecutarControlador($fn, $matches);
                        return;
                    }
                } elseif ($route === $currentUrl) {
                    // Manejar métodos estáticos y de instancia
                    $this->ejecutarControlador($fn);
                    return;
                }
            }

            $this->handleNotFound();
        } catch (\Throwable $e) {
            if (class_exists('App\\Utils\\Logger')) {
                \App\Utils\Logger::error('Error en el enrutador', [
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            } else {
                error_log(sprintf(
                    "Error: %s in %s on line %d\n%s",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                ));
            }

            if (!headers_sent()) {
                http_response_code(500);
            }

            exit;
        }
    }

    /**
     * Ejecutar controladores estáticos y de instancia
     */
    private function ejecutarControlador($fn, $params = [])
    {
        if (is_array($fn)) {
            // Es un array [ClassName, 'methodName']
            list($class, $method) = $fn;
            
            // Verificar si el método es estático
            $reflection = new \ReflectionMethod($class, $method);
            if ($reflection->isStatic()) {
                // Método estático: llamar directamente
                if ($params) {
                    call_user_func_array([$class, $method], array_merge([$this], $params));
                } else {
                    call_user_func([$class, $method], $this);
                }
            } else {
                // Método de instancia: crear objeto primero
                $instance = new $class();
                if ($params) {
                    call_user_func_array([$instance, $method], array_merge([$this], $params));
                } else {
                    call_user_func([$instance, $method], $this);
                }
            }
        } elseif (is_callable($fn)) {
            // Función anónima o callable
            if ($params) {
                call_user_func_array($fn, array_merge([$this], $params));
            } else {
                call_user_func($fn, $this);
            }
        } else {
            throw new \Exception("Controlador no válido: " . gettype($fn));
        }
    }

    protected function validarCSRF()
    {
        $token = $_POST['_csrf'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            // Guardar los datos del formulario para no perderlos
            $_SESSION['old_form_data'] = $_POST;

            // Redirigir con mensaje de error
            $_SESSION['csrf_error'] = 'El token de seguridad ha expirado. Por favor, intenta nuevamente.';
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/');
            exit;
        }
    }

    /**
     * Genera un campo de formulario CSRF
     */
    public function csrfField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($_SESSION['csrf_token'] ?? '') . '">';
    }

    public function old($key, $default = '')
    {
        return htmlspecialchars($_SESSION['old_form_data'][$key] ?? $default);
    }

    /**
     * Habilita o deshabilita la protección CSRF para rutas específicas
     */
    public function withoutCsrfProtection(): self
    {
        $this->csrfProtection = false;
        return $this;
    }

    public function handleNotFound()
    {
        http_response_code(404);

        if ($this->estaAutenticado()) {
            $this->render('error/404', [
                'mensaje' => 'Página no encontrada',
                'redireccion' => '/objetosperdidos'
            ]);
        } else {
            $this->render('error/404', [
                'mensaje' => 'Página no encontrada',
                'redireccion' => '/login'
            ]);
        }
        exit;
    }

    public function render($view, $datos = [])
    {
        $datos['router'] = $this;

        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        // Corrección en rutas
        $viewsPath = __DIR__ . '/Views/';
        $view = ltrim($view, '/');
        $viewFile = $viewsPath . "$view.php";

        if (!file_exists($viewFile)) {
            throw new \Exception("La vista no existe: $viewFile");
        }

        ob_start();
        include_once $viewFile;
        $contenido = ob_get_clean();
        include_once $viewsPath . 'layout.php';
    }
}