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
        // Solo iniciar sesión si se accede a una ruta protegida o si se requiere CSRF
        $path = $_SERVER['PATH_INFO'] ?? strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        $isProtected = isset($this->rutasProtegidas[$method][$path]);

        // Iniciar sesión solo si se requiere
        if ($isProtected || $method === 'POST' || isset($_COOKIE['lost_nexus'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_name('lost_nexus');
                session_start();

                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
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

            if ($this->rutaEstaProtegida($method, $currentUrl)) {
                if (!$this->estaAutenticado()) {
                    header('Location: /login');
                    exit;
                }
            }

            if ($method === 'POST' && $this->rutaEstaProtegida($method, $currentUrl)) {
                $this->validarCSRF();
            }

            $routes = ($method === 'GET') ? $this->getRoutes : $this->postRoutes;

            foreach ($routes as $route => $fn) {
                if (strpos($route, '{') !== false) {
                    $pattern = "@^" . preg_replace('/\{([^\}]+)\}/', '([^/]+)', $route) . "$@";
                    if (preg_match($pattern, $currentUrl, $matches)) {
                        array_shift($matches);
                        call_user_func_array($fn, array_merge([$this], $matches));
                        return;
                    }
                } elseif ($route === $currentUrl) {
                    call_user_func($fn, $this);
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

            exit; // Silenciosamente termina sin generar HTML
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
