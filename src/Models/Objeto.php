<?php

namespace App\Models;

use DateTime;
use Exception;

class Objeto extends ActiveRecord
{
    // Atributos
    protected static $tabla = "tblobjeto";
    protected static $columnasDB = [
        "id",
        "nombre",
        "descripcion",
        "fecha_reporte",
        "idpunto_recepcion",
        "foto",
        "idcategoria",
        "estado",
        "observaciones",
        "usuario_guarda",
        "usuario_devuelve",
        "fecha_devolucion"
    ];

    public $id;
    public $nombre;
    public $descripcion;
    public $fecha_reporte;
    public $idpunto_recepcion;
    public $foto;
    public $idcategoria;
    public $estado;
    public $observaciones;
    public $usuario_guarda;
    public $usuario_devuelve;
    public $fecha_devolucion;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? '';
        $this->descripcion = $args["descripcion"] ?? '';
        $this->fecha_reporte = $args["fecha_reporte"] ?? 'Y-m-d H:i:s';
        $this->idpunto_recepcion = $args["idpunto_recepcion"] ?? null;
        $this->foto = $args["foto"] ?? '';
        $this->idcategoria = $args["idcategoria"] ?? null;
        $this->estado = $args["estado"] ?? 'Perdido';
        $this->observaciones = $args["observaciones"] ?? '';
        $this->usuario_guarda = $args["usuario_guarda"] ?? null;
        $this->usuario_devuelve = $args["usuario_devuelve"] ?? null;
        $this->fecha_devolucion = $args["fecha_devolucion"] ?? null;
    }

    public function validar()
    {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = "El nombre del objeto es obligatorio";
        }

        if (!$this->descripcion) {
            self::$alertas['error'][] = "La descripción del objeto es obligatoria";
        }

        if (!$this->idcategoria) {
            self::$alertas['error'][] = "La categoría del objeto es obligatoria";
        }

        if (!$this->idpunto_recepcion) {
            self::$alertas['error'][] = "El punto de recepción es obligatorio";
        }

        if ($this->foto && !preg_match('/\.(jpg|jpeg|png|webp)$/i', $this->foto)) {
            self::$alertas['error'][] = "Formato de imagen no válido";
        }

        return self::$alertas;
    }

    public static function listarObjetosPerdidos()
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE estado = 'Perdido'";
        return self::consultarSQL($query);
    }

    public static function findWithDetails($id)
    {
        $query = "select tblo.*, tblc.nombre as categoria, tblpr.nombre as punto_recepcion, tblpr.ubicacion as ubicacion from " . static::$tabla . " tblo
            inner join tbl_categoria tblc on tblc.id = tblo.idcategoria
            inner join tbl_punto_recepcion tblpr on tblo.idpunto_recepcion = tblpr.id WHERE tblo.id = " . self::$db->real_escape_string($id);

        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public static function findWithDetailsDevueltos($id)
    {
        $query = "SELECT tblo.*, 
              tblc.nombre AS categoria_nombre, 
              tblpr.nombre AS punto_recepcion_nombre, 
              tblpr.ubicacion AS punto_recepcion_ubicacion,
              tblrec.evidencia AS reclamacion_evidencia,
              tblrec.fecha_reclamacion AS reclamacion_fecha,
              tblrec.observaciones AS reclamacion_observaciones,
              tblrecl.nombre AS reclamante_nombre, 
              tblrecl.apellido AS reclamante_apellido,
              tblrecl.cedula AS reclamante_cedula, 
              tblrecl.carnet_estudiante AS reclamante_carnet
              FROM " . static::$tabla . " tblo
              INNER JOIN tbl_categoria tblc ON tblc.id = tblo.idcategoria
              INNER JOIN tbl_punto_recepcion tblpr ON tblo.idpunto_recepcion = tblpr.id
              INNER JOIN tbl_reclamacion tblrec ON tblo.id = tblrec.idobjeto
              INNER JOIN tbl_reclamante tblrecl ON tblrec.idreclamante = tblrecl.id 
              WHERE tblo.estado = 'Devuelto' AND tblo.id = " . self::$db->real_escape_string($id);

        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public function categoria()
    {
        return Categoria::find($this->idcategoria);
    }

    public function puntoRecepcion()
    {
        return PuntoRecepcion::find($this->idpunto_recepcion);
    }

    public static function filtrar($filtros = [], $porPagina = 10, $pagina = 1, $recientes = false)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE 1=1 AND estado = 'Perdido'";

        if ($recientes) {
            $query .= " AND fecha_reporte >= DATE_SUB(NOW(), INTERVAL 2 MONTH)";
        }
        $params = [];

        // Filtro por nombre
        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        // Filtro por categoría
        if (!empty($filtros['categoria']) && is_numeric($filtros['categoria'])) {
            $query .= " AND idcategoria = ?";
            $params[] = (int)$filtros['categoria'];
        }

        // Filtro por fecha
        if (!empty($filtros['fecha'])) {
            if (\DateTime::createFromFormat('Y-m-d', $filtros['fecha']) !== false) {
                $query .= " AND DATE(fecha_reporte) = ?";
                $params[] = $filtros['fecha'];
            }
        }

        // Paginación: inyectar directamente los valores
        $offset = ($pagina - 1) * $porPagina;
        $porPagina = (int)$porPagina;
        $offset = (int)$offset;

        $query .= " LIMIT $porPagina OFFSET $offset";

        error_log($query);

        return self::consultarSQL($query, $params);
    }


    public static function filtrarDevueltos($filtros = [], $porPagina = 10, $pagina = 1)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE 1=1 AND estado = 'Devuelto'";
        $params = [];

        // Filtro por nombre
        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        // Filtro por categoría
        if (!empty($filtros['categoria']) && is_numeric($filtros['categoria'])) {
            $query .= " AND idcategoria = ?";
            $params[] = (int)$filtros['categoria'];
        }

        // Filtro por fecha
        if (!empty($filtros['fecha'])) {
            // Validar formato de fecha
            if (\DateTime::createFromFormat('Y-m-d', $filtros['fecha']) !== false) {
                $query .= " AND DATE(fecha_reporte) = ?";
                $params[] = $filtros['fecha'];
            }
        }

        // Paginación
        $offset = ($pagina - 1) * $porPagina;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = (int)$porPagina;
        $params[] = (int)$offset;

        return self::consultarSQL($query, $params);
    }

    public static function contarFiltrados($filtros = [], $recientes = false)
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE 1=1 AND estado = 'Perdido'";

        if ($recientes) {
            $query .= " AND fecha_reporte >= DATE_SUB(NOW(), INTERVAL 2 MONTH)";
        }
        $params = [];

        // Mismos filtros que en filtrar()
        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        if (!empty($filtros['categoria']) && is_numeric($filtros['categoria'])) {
            $query .= " AND idcategoria = ?";
            $params[] = (int)$filtros['categoria'];
        }

        if (!empty($filtros['fecha'])) {
            if (DateTime::createFromFormat('Y-m-d', $filtros['fecha']) !== false) {
                $query .= " AND DATE(fecha_reporte) = ?";
                $params[] = $filtros['fecha'];
            }
        }

        try {
            $resultado = self::consultarSQL($query, $params);

            // Manejo más robusto del resultado
            if (is_array($resultado) && count($resultado) > 0 && is_object($resultado[0])) {
                return (int)($resultado[0]->total ?? 0);
            }

            // Log para depuración
            error_log("Resultado inesperado en contarFiltrados: " . print_r($resultado, true));
            return 0;
        } catch (Exception $e) {
            error_log("Error en contarFiltrados: " . $e->getMessage());
            return 0;
        }
    }

    public static function contarDevueltos($filtros = [])
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE 1=1 AND estado = 'Devuelto'";
        $params = [];

        // Mismos filtros que en filtrar()
        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        if (!empty($filtros['categoria']) && is_numeric($filtros['categoria'])) {
            $query .= " AND idcategoria = ?";
            $params[] = (int)$filtros['categoria'];
        }

        if (!empty($filtros['fecha'])) {
            if (\DateTime::createFromFormat('Y-m-d', $filtros['fecha']) !== false) {
                $query .= " AND DATE(fecha_reporte) = ?";
                $params[] = $filtros['fecha'];
            }
        }

        $resultado = self::consultarSQL($query, $params);
        return $resultado[0]->total ?? 0;
    }

    public static function contarPorEstado($estado)
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE estado = ?";
        // Usar consulta preparada
        $stmt = self::$db->prepare($query);
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . self::$db->error);
            return 0;
        }
        $stmt->bind_param("s", $estado);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
        return (int)($fila['total'] ?? 0);
    }

    public static function objetosDevueltosPorUsuario($usuarioId)
    {
        $query = " SELECT * FROM " . static::$tabla . " WHERE estado = 'Devuelto' AND usuario_devuelve = ? ORDER BY fecha_devolucion DESC LIMIT 5";
        return self::consultarSQL($query, [$usuarioId]);
    }

    public static function devolucionesPorMes($limiteMeses = 6)
    {
        // Primero, obtener los últimos X meses
        $mesesRecientes = [];
        for ($i = $limiteMeses - 1; $i >= 0; $i--) {
            $mesesRecientes[] = date('Y-m', strtotime("-$i months"));
        }

        // Consulta para obtener los datos agrupados por mes
        $query = "SELECT 
                DATE_FORMAT(fecha_devolucion, '%Y-%m') as mes, 
                COUNT(*) as total
              FROM " . static::$tabla . "
              WHERE estado = 'Devuelto'
              AND fecha_devolucion IS NOT NULL
              GROUP BY DATE_FORMAT(fecha_devolucion, '%Y-%m')
              ORDER BY mes";

        // Usar consulta directa en lugar de consultarSQL
        $result = self::$db->query($query);
        if (!$result) {
            error_log("Error en consulta: " . self::$db->error);
            return ['meses' => [], 'totales' => []];
        }

        // Procesar resultados como array asociativo
        $datosPorMes = [];
        while ($fila = $result->fetch_assoc()) {
            $datosPorMes[$fila['mes']] = (int)$fila['total'];
        }

        // Preparar datos para el gráfico
        $meses = [];
        $totales = [];

        foreach ($mesesRecientes as $mes) {
            $mesFormateado = DateTime::createFromFormat('Y-m', $mes)->format('M Y');
            $meses[] = $mesFormateado;
            $totales[] = $datosPorMes[$mes] ?? 0; // 0 si no hay datos para ese mes
        }

        return [
            'meses' => $meses,
            'totales' => $totales
        ];
    }

    public static function objetosRecientes()
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE estado = 'Perdido' ORDER BY fecha_reporte DESC LIMIT 5";
        return self::consultarSQL($query);
    }

    public static function filtrarArchivados($filtros = [], $porPagina = 10, $pagina = 1)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE 1=1 AND estado = 'Perdido' AND fecha_reporte < DATE_SUB(NOW(), INTERVAL 2 MONTH)";
        $params = [];

        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        if (!empty($filtros['categoria']) && is_numeric($filtros['categoria'])) {
            $query .= " AND idcategoria = ?";
            $params[] = (int)$filtros['categoria'];
        }

        if (!empty($filtros['fecha'])) {
            if (\DateTime::createFromFormat('Y-m-d', $filtros['fecha']) !== false) {
                $query .= " AND DATE(fecha_reporte) = ?";
                $params[] = $filtros['fecha'];
            }
        }

        $offset = ($pagina - 1) * $porPagina;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = (int)$porPagina;
        $params[] = (int)$offset;

        return self::consultarSQL($query, $params);
    }

    public static function contarArchivados($filtros = [])
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE 1=1 AND estado = 'Perdido' AND fecha_reporte < DATE_SUB(NOW(), INTERVAL 2 MONTH)";
        $params = [];

        if (!empty($filtros['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = "%" . trim($filtros['nombre']) . "%";
        }

        if (!empty($filtros['categoria']) && is_numeric($filtros['categoria'])) {
            $query .= " AND idcategoria = ?";
            $params[] = (int)$filtros['categoria'];
        }

        if (!empty($filtros['fecha'])) {
            if (\DateTime::createFromFormat('Y-m-d', $filtros['fecha']) !== false) {
                $query .= " AND DATE(fecha_reporte) = ?";
                $params[] = $filtros['fecha'];
            }
        }

        $resultado = self::consultarSQL($query, $params);
        return $resultado[0]->total ?? 0;
        
    }
}
