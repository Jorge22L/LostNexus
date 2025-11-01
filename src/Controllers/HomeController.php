<?php

namespace App\Controllers;

use App\Models\Objeto;
use App\Router;

class HomeController
{
    public static function index(Router $router)
    {
        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }


        // Obtener estadísticas
        $totalPendientes = Objeto::contarPorEstado('Perdido');
        $totalDevueltos = Objeto::contarPorEstado('Devuelto');
        $recientes = Objeto::objetosRecientes();
        $datosGrafico = Objeto::devolucionesPorMes();

        $router->render('/home/home', [
            'totalPendientes' => $totalPendientes,
            'totalDevueltos' => $totalDevueltos,
            'objetosRecientes' => $recientes,
            'datosGrafico' => $datosGrafico
        ]);
    }

}
