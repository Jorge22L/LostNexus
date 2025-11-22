<?php

namespace App\Models;

class Reclamante extends ActiveRecord
{
    // Atributos
    protected static $tabla = "tbl_reclamante";
    protected static $columnasDB = [
        "id",
        "nombre",
        "apellido",
        "cedula",
        "carnet_estudiante",
        "email",
        "fecha_registro",
    ];

    public $id;
    public $nombre;
    public $apellido;
    public $cedula;
    public $carnet_estudiante;
    public $email;
    public $fecha_registro;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? '';
        $this->apellido = $args["apellido"] ?? '';
        $this->cedula = $args["cedula"] ?? '';
        $this->carnet_estudiante = $args["carnet_estudiante"] ?? '';
        $this->email = $args["email"] ?? '';
        $this->fecha_registro = $args["fecha_registro"] ?? '';
    }

    public function validarReclamante()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if (!$this->apellido) {
            self::$alertas['error'][] = 'El apellido es obligatorio';
        }

        if (!$this->cedula) {
            self::$alertas['error'][] = 'La cedula es obligatoria';
        }

        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        
        return self::$alertas;
    }
}
