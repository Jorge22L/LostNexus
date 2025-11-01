<?php

namespace App\Models;

class Categoria extends ActiveRecord
{
    // Atributos
    protected static $tabla = "tbl_categoria";
    protected static $columnasDB = [
        "id",
        "nombre"
    ];

    public $id;
    public $nombre;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? '';
    }

    public function validar()
    {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = "El nombre de la categoría es obligatorio";
        }

        return self::$alertas;
    }

    public static function filtrar($filtros = [], $porPagina = 10, $pagina = 1)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE 1=1";
        $params = [];

        // Validar filtros
        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        $offset = ($pagina - 1) * $porPagina;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = (int)$porPagina;
        $params[] = (int)$offset;

        return self::consultarSQL($query, $params);
    }

    public static function contarFiltrados($filtros = [])
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE 1=1";
        $params = [];

        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        // Versión corregida para COUNT
        $stmt = self::$db->prepare($query);

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        return (int)$fila['total'];
    }
}
