<?php
// public/index.php - VERSIÓN COMPATIBLE CON NGINX - CORREGIDA

// PATCH CRÍTICO MEJORADO: Compatibilidad entre Nginx y tu Router
$currentUrl = '/';

// PRIMERO: Intentar obtener de $_GET['url'] (configuración actual de Nginx)
if (isset($_GET['url']) && $_GET['url'] !== '') {
    $currentUrl = $_GET['url'];
} 
// SEGUNDO: Fallback a PATH_INFO si está disponible
elseif (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $currentUrl = $_SERVER['PATH_INFO'];
} 
// TERCERO: Fallback a REQUEST_URI
elseif (isset($_SERVER['REQUEST_URI'])) {
    $requestUri = $_SERVER['REQUEST_URI'];
    // Remover query string si existe
    $parsed = parse_url($requestUri);
    $currentUrl = $parsed['path'] ?? '/';
}

// Normalizar la URL
$currentUrl = rtrim($currentUrl, '/');
if ($currentUrl === '') {
    $currentUrl = '/';
}

// Configurar para el router
$_SERVER['REQUEST_URI'] = $currentUrl;
$_SERVER['PATH_INFO'] = $currentUrl;

use App\Controllers\CategoriasController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\ObjetosDevueltosController;
use App\Controllers\ObjetosPerdidosController;
use App\Controllers\PuntoRecepcionController;
use App\Router;
use App\Utils\Logger;

require_once __DIR__ . '/../includes/app.php';

$tempDir = sys_get_temp_dir() . '/php-uploads';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}
ini_set('upload_tmp_dir', $tempDir);

$router = new Router();

// Logger
Logger::register();

// raiz
// raiz
$router->get('/', function($router) {
    if ($router->estaAutenticado()) {
        header('Location: /objetosperdidos');
    } else {
        header('Location: /login');
    }
    exit;
});

// HomePage
$router->get('/home', [HomeController::class, 'index']);

// Objetos Perdidos
$router->get('/objetosperdidos', [ObjetosPerdidosController::class, 'index']);
$router->get('/objetosperdidos/archivados', [ObjetosPerdidosController::class, 'archivados'], true);
$router->post('/objetosperdidos/dar-de-baja/{id}', [ObjetosPerdidosController::class, 'darDeBaja'], true);

// Objetos Devueltos
$router->get('/objetosdevueltos', [ObjetosDevueltosController::class, 'index'], true);
$router->get('/objetosdevueltos/ver', [ObjetosDevueltosController::class, 'ver'], true);
$router->get('/objetosdevueltos/ver/{id}', [ObjetosDevueltosController::class, 'ver'], true);

// Agregar Objetos
$router->get('/agregar_objetos', [ObjetosPerdidosController::class, 'crear'], true);
$router->post('/agregar_objetos/crear', [ObjetosPerdidosController::class, 'crear'], true);

// Ver Objetos
$router->get('/objetosperdidos/ver', [ObjetosPerdidosController::class, 'ver']);
$router->get('/objetosperdidos/ver/{id}', [ObjetosPerdidosController::class, 'ver']);

// ActualizarObjetos
$router->get('/objetosperdidos/editar/{id}', [ObjetosPerdidosController::class, 'editar'], true);
$router->post('/objetosperdidos/editar/{id}', [ObjetosPerdidosController::class, 'editar'], true);

// Devolver Objetos
$router->get('/objetosperdidos/devolver/{id}', [ObjetosPerdidosController::class, 'devolver'], true);
$router->post('/objetosperdidos/devolver/{id}', [ObjetosPerdidosController::class, 'devolver'], true);

// Categorias
$router->get('/categorias', [CategoriasController::class, 'index'], true);

// Agregar Categorias
$router->get('/agregar_categoria', [CategoriasController::class, 'crear'], true);
$router->post('/agregar_categoria/crear', [CategoriasController::class, 'crear'], true);

// Editar Categorias
$router->get('/categorias/editar/{id}', [CategoriasController::class, 'editar'], true);
$router->post('/categorias/editar/{id}', [CategoriasController::class, 'editar'], true);

// Eliminar Categorias
$router->post('/categorias', [CategoriasController::class, 'eliminar'], true);

// Puntos de Recepcion
$router->get('/puntosrecepcion', [PuntoRecepcionController::class, 'index'], true);

// Agregar Puntos de Recepcion
$router->get('/agregar_punto_recepcion', [PuntoRecepcionController::class, 'crear'], true);
$router->post('/agregar_punto_recepcion/crear', [PuntoRecepcionController::class, 'crear'], true);

// Editar Puntos de Recepcion
$router->get('/puntosrecepcion/editar/{id}', [PuntoRecepcionController::class, 'editar'], true);
$router->post('/puntosrecepcion/editar/{id}', [PuntoRecepcionController::class, 'editar'], true);

// Eliminar Puntos de Recepcion
$router->post('/puntosrecepcion', [PuntoRecepcionController::class, 'eliminar'], true);

// Login
$router->get('/login', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);

// Logout
$router->get('/logout', [LoginController::class, 'logout']);

// Listar Usuarios
$router->get('/admin/usuarios', [LoginController::class, 'listar'], true);

// Crear Cuenta
$router->get('/admin/usuarios/crear', [LoginController::class, 'crear'], true);
$router->post('/admin/usuarios/crear', [LoginController::class, 'crear'], true);

// Editar Cuenta
$router->get('/admin/usuarios/editar/{id}', [LoginController::class, 'editar'], true);
$router->post('/admin/usuarios/editar/{id}', [LoginController::class, 'editar'], true);

// Eliminar Cuenta
$router->post('/admin/usuarios/eliminar', [LoginController::class, 'eliminar'], true);

// Comprueba y valida las rutas, que existen y les asigna las funciones del controlador
$router->comprobarRutas();