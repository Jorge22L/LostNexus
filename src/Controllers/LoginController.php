<?php

namespace App\Controllers;

use App\Helpers\CSRF;
use App\Models\Usuario;
use App\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas['error'])) {
                $usuario = Usuario::whereFirst('nombre_usuario', $auth->nombre_usuario);

                if ($usuario !== null) {
                    if ($usuario->comprobarPasswordAndVerificado($auth->pwd)) {
                        // Regenerar el ID de sesión despúes del login exitoso
                        session_regenerate_id();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['nombre_usuario'] = $usuario->nombre_usuario;
                        $_SESSION['login'] = true;
                        $_SESSION['admin'] = $usuario->nombre_usuario;

                        // Regenerar token CSRF después del login
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                        header('Location: /objetosperdidos');
                        exit;
                    }
                } else {
                    $alertas['error'][] = 'Usuario no encontrado';
                }
            }

            $alertas = array_merge($alertas, Usuario::getAlertas());
        }

        $router->render('/login/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        $_SESSION = [];

        // Si se desea destruir la cookie de sesión también
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finalmente, destruir la sesión
        session_destroy();

        header('Location: /login');
        exit;
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario();

        // Alertas vacías
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/login/crear', [
                    'alertas' => $alertas,
                    'usuario' => $usuario
                ]);
                return;
            }

            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas['error'])) {
                // Verificar que el usuario no esté registrado
                $resultado = $usuario->existeUsuario();
                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear la contraseña
                    $usuario->hashPassword();

                    // Generar token unico
                    $usuario->crearToken();

                    // Crear el usuario
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        header('Location: /admin/usuarios');
                        exit;
                    }
                }
            }
        }

        $router->render('/login/crear', [
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function listar(Router $router)
    {
        // Obtener parámetros de búsqueda
        $busqueda = trim($_GET['busqueda'] ?? '');

        // Paginación
        $paginaActual = (int)($_GET['page'] ?? 1);
        $registrosPorPagina = 10;

        // Preparar filtros
        $filtros = [];
        if (!empty($busqueda)) {
            $filtros['busqueda'] = $busqueda;
        }

        // Obtener usuarios paginados con filtros
        $usuarios = Usuario::filtrar($filtros, $registrosPorPagina, $paginaActual);

        // Obtener total de usuarios
        $totalUsuarios = Usuario::contarFiltrados($filtros);
        $totalPaginas = ceil($totalUsuarios / $registrosPorPagina);

        // Calcular rangos para mostrar
        $inicio = ($totalUsuarios > 0) ? (($paginaActual - 1) * $registrosPorPagina) + 1 : 0;
        $fin = min($paginaActual * $registrosPorPagina, $totalUsuarios);

        $router->render('/login/listar', [
            'usuarios' => $usuarios,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'inicio' => $inicio,
            'fin' => $fin,
            'busqueda' => $busqueda
        ]);
    }

    public static function editar(Router $router, $id)
    {
        // Obtener usuario existente
        $usuario = Usuario::find($id);

        if (!$usuario) {
            header('Location: /admin/usuarios');
            exit;
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/login/editar', [
                    'alertas' => $alertas,
                    'usuario' => $usuario
                ]);
                return;
            }

            // Sincronizar con datos del formulario
            $usuario->sincronizar($_POST);

            // Validar campos
            $alertas = $usuario->validar();

            if (empty($alertas['error'])) {
                $resultado = $usuario->actualizar();
                if ($resultado) {
                    header('Location: /admin/usuarios');
                    exit;
                }
            } else {
                $alertas['error'][] = 'Error al actualizar el usuario';
            }
        }

        $router->render('/login/editar', [
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            $csrfToken = $_POST['_csrf'] ?? '';

            // Validar que es string
            if (is_array($csrfToken)) {
                $csrfToken = $csrfToken[0] ?? '';
            }

            if (!CSRF::validateToken($csrfToken)) {
                $_SESSION['error'] = 'Token CSRF no válido';
                header('Location: /admin/usuarios');
                exit;
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                $_SESSION['error'] = 'ID de usuario no proporcionado';
                header('Location: /admin/usuarios');
                exit;
            }

            $usuario = Usuario::find($id);
            if (!$usuario) {
                $_SESSION['error'] = 'Usuario no encontrado';
            } else {
                try {
                    if ($usuario->eliminar()) {
                        $_SESSION['success'] = 'Usuario eliminado correctamente';
                    } else {
                        $_SESSION['error'] = 'Error al eliminar el usuario';
                    }
                } catch (\Exception $e) {
                    $_SESSION['error'] = 'Error al eliminar el usuario: ' . $e->getMessage();
                }
            }

            header('Location: /admin/usuarios');
            exit;
        }
    }
}
