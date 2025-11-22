<?php

namespace App\Models;

class Reclamacion extends ActiveRecord
{
    // Atributos
    protected static $tabla = "tbl_reclamacion";
    protected static $columnasDB = [
        "id",
        "idobjeto",
        "idreclamante",
        "fecha_reclamacion",
        "observaciones",
        "usuario_atiende",
        "evidencia"
    ];

    public $id;
    public $idobjeto;
    public $idreclamante;
    public $fecha_reclamacion;
    public $observaciones;
    public $usuario_atiende;
    public $evidencia;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->idobjeto = $args["idobjeto"] ?? null;
        $this->idreclamante = $args["idreclamante"] ?? null;
        $this->fecha_reclamacion = $args["fecha_reclamacion"] ?? null;
        $this->observaciones = $args["observaciones"] ?? null;
        $this->usuario_atiende = $args["usuario_atiende"] ?? null;
        $this->evidencia = $args["evidencia"] ?? null;
    }

    public function validarReclamacion()
    {
        if (!$this->idobjeto) {
            self::$alertas['error'][] = 'El objeto es obligatorio';
        }

        if (!$this->idreclamante) {
            self::$alertas['error'][] = 'El reclamante es obligatorio';
        }

        if (!$this->fecha_reclamacion) {
            self::$alertas['error'][] = 'La fecha de reclamacion es obligatoria';
        }

        if (!$this->observaciones) {
            self::$alertas['error'][] = 'Las observaciones son obligatorias';
        }

        if (!$this->usuario_atiende) {
            self::$alertas['error'][] = 'El usuario que atiende es obligatorio';
        }

        if (!$this->evidencia) {
            self::$alertas['error'][] = 'La evidencia es obligatoria';
        }

        return self::$alertas;
        
    }
}