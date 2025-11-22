<?php

namespace App\Models;

class PuntoRecepcion extends ActiveRecord
{
    // Atributos
    protected static $tabla = "tbl_punto_recepcion";
    protected static $columnasDB = [
        "id",
        "nombre",
        "ubicacion"
    ];

    public $id;
    public $nombre;
    public $ubicacion;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? '';
        $this->ubicacion = $args["ubicacion"] ?? '';
    }

    public function validar()
    {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = "El nombre del punto de recepción es obligatorio";
        }

        if (!$this->ubicacion) {
            self::$alertas['error'][] = "La ubicación del punto de recepción es obligatoria";
        }

        return self::$alertas;
    }

    public static function filtrar($filtros = [], $porPagina = 10, $pagina = 1)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE 1 = 1";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (nombre LIKE ? OR ubicacion LIKE ?)";
            $searchTerm = "%" . $filtros['busqueda'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        // Añadir ordenación por defecto
        // $query .= " ORDER BY nombre ASC";

        // Paginación
        $offset = ($pagina - 1) * $porPagina;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = (int)$porPagina;
        $params[] = (int)$offset;

        return self::consultarSQL($query, $params);
    }
    
    public static function contarFiltrados($filtros = [])
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE 1 = 1";
        $params = [];
        $types = '';

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (nombre LIKE ? OR ubicacion LIKE ?)";
            $searchTerm = "%" . $filtros['busqueda'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
            $types .= 'ss';
        }

        // Usar sentencias preparadas
        $stmt = self::$db->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        return (int)$fila['total'];
        
    }
}
