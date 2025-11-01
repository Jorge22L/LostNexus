<?php

namespace App\Controllers;

use App\Models\Categoria;
use App\Models\Objeto;
use App\Models\PuntoRecepcion;
use App\Models\Reclamacion;
use App\Models\Reclamante;
use App\Router;
use DateTime;

class ObjetosDevueltosController
{
    public static function index(Router $router)
    {
        $filtros = [
            'nombre' => trim($_GET['nombre'] ?? ''),
            'categoria' => $_GET['categoria'] ?? null,
            'fecha' => trim($_GET['fecha'] ?? '')
        ];

        // Validar categoría
        if ($filtros['categoria'] === '' || !is_numeric($filtros['categoria'])) {
            $filtros['categoria'] = null;
        }

        // Validar fecha
        if ($filtros['fecha'] !== '' && DateTime::createFromFormat('Y-m-d', $filtros['fecha']) === false) {
            $filtros['fecha'];
        }

        // Paginación
        // Obtener el número de página actual (si no existe, será 1)
        $paginaActual = max(1, (int)($_GET['page'] ?? 1));
        $registrosPorPagina = 10;

        // Obtener los objetos
        $objetos = Objeto::filtrarDevueltos($filtros, $registrosPorPagina, $paginaActual);
        // Obtener el total de registros para calcular el total de páginas
        $totalRegistros = Objeto::contarDevueltos($filtros);
        $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

        //Recuperar categorias
        $categorias = Categoria::all();

        $router->render('/objetosdevueltos/index', [
            'objetos' => $objetos,
            'categorias' => $categorias,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'nombre_filtro' => $filtros['nombre'],
            'categoria_filtro' => $filtros['categoria'],
            'fecha_filtro' => $filtros['fecha']
        ]);
    }

    public static function ver(Router $router, $id = null)
    {
        if (!$id) {
            header('Location: /objetosdevueltos');
            exit;
        }

        // Obtener el objeto básico
        $objeto = Objeto::find($id);

        if (!$objeto || $objeto->estado !== 'Devuelto') {
            header('Location: /objetosdevueltos');
            exit;
        }

        // Obtener datos adicionales
        $objeto->categoria = Categoria::find($objeto->idcategoria);
        $objeto->puntoRecepcion = PuntoRecepcion::find($objeto->idpunto_recepcion);

        // Obtener información de la reclamación (versión corregida)
        $reclamaciones = Reclamacion::where('idobjeto', $objeto->id);
        $reclamacion = is_array($reclamaciones) ? reset($reclamaciones) : $reclamaciones->first();

        if ($reclamacion) {
            $objeto->reclamacion = $reclamacion;
            $objeto->reclamante = Reclamante::find($reclamacion->idreclamante);
        }

        $router->render('/objetosdevueltos/ver_objeto_devuelto', [
            'objeto' => $objeto
        ]);
    }
}
