<?php

namespace App;

class Controller
{
    // Método para renderizar vistas
    protected function render($view, $data = [])
    {
        // Extraer datos para utilizar en vista
        extract($data);

        // Insertar el archivo de vista (view)
        include "Views/$view.php";
    }
}