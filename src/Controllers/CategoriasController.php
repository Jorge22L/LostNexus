<?php

namespace App\Controllers;

use App\Models\Categoria;
use App\Router;
use App\Helpers\CSRF;

class CategoriasController
{
    public static function index(Router $router)
    {
        $busqueda = trim($_GET['busqueda'] ?? '');

        // Configurar filtros
        $filtros = [];
        if (!empty($busqueda)) {
            $filtros['nombre'] = $busqueda;
        }

        // Configurar paginación
        $paginaActual = max(1, (int)($_GET['page'] ?? 1));
        $registrosPorPagina = 10;

        // Obtener datos
        $categorias = Categoria::filtrar($filtros, $registrosPorPagina, $paginaActual);
        $totalCategorias = Categoria::contarFiltrados($filtros);
        $totalPaginas = max(1, ceil($totalCategorias / $registrosPorPagina));

        // Calcular valores de inicio y fin
        $inicio = ($totalCategorias > 0) ? (($paginaActual - 1) * $registrosPorPagina) + 1 : 0;
        $fin = min($paginaActual * $registrosPorPagina, $totalCategorias);

        $router->render('/categorias/index', [
            'categorias' => $categorias,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'totalCategorias' => $totalCategorias,
            'inicio' => $inicio,
            'fin' => $fin,
            'busqueda' => $busqueda
        ]);
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        $categorias = new Categoria();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/categorias/agregar_categoria', [
                    'alertas' => $alertas,
                    'categorias' => $categorias
                ]);

                return;
            }
            // Procesar los datos del formulario
            $datos = $_POST;

            // Crear y guardar la categoria
            $categorias = new Categoria($datos);
            $alertas = $categorias->validar();

            if (empty($alertas['error'])) {
                $resultado = $categorias->guardar();
                if ($resultado) {
                    header('Location: /categorias');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar la categoria';
                }
            }
        }

        $router->render('/categorias/agregar_categoria', [
            'alertas' => $alertas,
            'categorias' => $categorias
        ]);
    }

    public static function editar(Router $router, $id)
    {
        // Obtener la categoria existente
        $categorias = Categoria::find($id);

        if (!$categorias) {
            header('Location: /categorias');
            exit;
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validar CSRF primero
            if (!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf'])) {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/categorias/editar', [
                    'alertas' => $alertas,
                    'categoria' => $categorias,
                    'router' => $router
                ]);
                return;
            }

            // Sincronizar con datos del formulario
            $categorias->sincronizar($_POST);

            // Validar campos
            $alertas = $categorias->validar();

            if (empty($alertas['error'])) {
                $resultado = $categorias->actualizar();
                if ($resultado) {
                    header('Location: /categorias');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al actualizar la categoria';
                }
            }
        }

        $router->render('/categorias/editar', [
            'alertas' => $alertas,
            'categorias' => $categorias
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Validar CSRF
            if (!isset($_POST['_csrf'])) {
                // Validar CSRF
                $csrfToken = $_POST['_csrf'] ?? '';

                // Validar que token es string
                if (is_array($csrfToken)) {
                    $csrfToken = $csrfToken[0] ?? '';
                }

                if (!CSRF::validateToken($csrfToken)) {
                    $_SESSION['error'] = 'Token CSRF no válido';
                    header('Location: /categorias');
                    exit;
                }
            }
            $id = $_POST['id'];
            $categorias = Categoria::find($id);
            if ($categorias) {
                $categorias->eliminar();
            }
            header('Location: /categorias');
            exit;
        }
    }
}
