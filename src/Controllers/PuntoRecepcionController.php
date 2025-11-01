<?php

namespace App\Controllers;

use App\Helpers\CSRF;
use App\Models\PuntoRecepcion;
use App\Router;

class PuntoRecepcionController
{
    public static function index(Router $router)
    {
        // Obtener parámetro de búsqueda
        $busqueda = trim($_GET['busqueda'] ?? '');

        // Paginación
        $paginaActual = (int)($_GET['page'] ?? 1);
        $registrosPorPagina = 10;

        // Preparar filtros
        $filtros = [];
        if (!empty($busqueda)) {
            $filtros['busqueda'] = $busqueda;
        }

        // Obtener puntos de recepción con filtros
        $puntosrecepcion = PuntoRecepcion::filtrar($filtros, $registrosPorPagina, $paginaActual);

        // Obtener total de puntos de recepcion
        $totalPuntosRecepcion = PuntoRecepcion::contarFiltrados($filtros);
        $totalPaginas = ceil($totalPuntosRecepcion / $registrosPorPagina);

        // Calcular rangos para mostrar
        $inicio = ($totalPuntosRecepcion > 0) ? (($paginaActual - 1) * $registrosPorPagina) + 1 : 0;
        $fin = min($paginaActual * $registrosPorPagina, $totalPuntosRecepcion);

        $router->render('/puntosrecepcion/index', [
            'puntorecepcion' => $puntosrecepcion, // Cambié el nombre de la variable aquí
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'inicio' => $inicio,
            'fin' => $fin,
            'busqueda' => $busqueda,
            'totalPuntos' => $totalPuntosRecepcion // Añadí esta variable
        ]);
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        $puntorecepcion = new PuntoRecepcion();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if(!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf']))
            {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/puntosrecepcion/agregar_punto_recepcion', [
                    'alertas' => $alertas,
                    'puntorecepcion' => $puntorecepcion
                ]);
                return;
            }
            // Procesar los datos del formulario
            $datos = $_POST;

            // Crear y guardar la categoria
            $puntorecepcion = new PuntoRecepcion($datos);
            $alertas = $puntorecepcion->validar();

            if (empty($alertas['error'])) {
                $resultado = $puntorecepcion->guardar();
                if ($resultado) {
                    header('Location: /puntosrecepcion');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al guardar el punto de recepción';
                }
            }
        }

        $router->render('/puntosrecepcion/agregar_punto_recepcion', [
            'alertas' => $alertas,
            'puntorecepcion' => $puntorecepcion
        ]);
    }

    public static function editar(Router $router, $id)
    {
        // Obtener el punto de recepción existente
        $puntorecepcion = PuntoRecepcion::find($id);

        if (!$puntorecepcion) {
            header('Location: /puntosrecepcion');
            exit;
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if(!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf']))
            {
                $alertas['error'][] = 'Token CSRF no válido';
                $router->render('/puntosrecepcion/editar', [
                    'alertas' => $alertas,
                    'puntorecepcion' => $puntorecepcion
                ]);
                return;
            }
            // Sincronizar con datos del formulario
            $puntorecepcion->sincronizar($_POST);

            // Validar campos
            $alertas = $puntorecepcion->validar();

            if (empty($alertas['error'])) {
                $resultado = $puntorecepcion->actualizar();
                if ($resultado) {
                    header('Location: /puntosrecepcion');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al actualizar el punto de recepción';
                }
            }
        }

        $router->render('/puntosrecepcion/editar', [
            'alertas' => $alertas,
            'puntorecepcion' => $puntorecepcion
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!isset($_POST['_csrf']) || !CSRF::validateToken($_POST['_csrf']))
            {
                $_SESSION['error'] = 'Token CSRF no válido';
                header('Location: /puntosrecepcion');
                exit;
            }
            $id = $_POST['id'];
            $puntorecepcion = PuntoRecepcion::find($id);
            if($puntorecepcion)
            {
                $puntorecepcion->eliminar();
            }
            header('Location: /puntosrecepcion');
            exit;
        }
    }
}
