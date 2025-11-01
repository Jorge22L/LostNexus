<?php

namespace App\Models;

class ActiveRecord
{
    // Base de Datos
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];

    // Definir la conexión a la DB - includes/database.php
    public static function setDB($database)
    {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje)
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas()
    {
        return static::$alertas;
    }

    public function validar()
    {
        static::$alertas = [];
        return static::$alertas;
    }

    //Consulta SQL para crear objeto en Memoria
    public static function consultarSQL($query, $params = [])
    {
        // Si no hay parámetros, no se ejecuta la consulta.
        if (empty($params)) {
            $resultado = self::$db->query($query);
        } else {
            // Usar consultas preparadas para seguridad
            $stmt = self::$db->prepare($query);

            if ($stmt === false) {
                throw new \RuntimeException("Error en la preparación de la consulta: " . self::$db->error);
            }

            // Bind de parámetros si existen
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Asumimos strings por seguridad
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $resultado = $stmt->get_result();
        }

        // Iterar los resultados.
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados.
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la DB
    protected static function crearObjeto($registro)
    {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la DB
    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }

        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la DB.
    public function sanitizarAtributos()
    {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            if (is_null($value)) {
                $sanitizado[$key] = 'NULL';
            } else {
                // Escapar comillas simples
                $sanitizado[$key] = "'" . self::$db->real_escape_string($value) . "'";
            }
        }

        return $sanitizado;
    }

    // Sincroniza DB con Objetos en memoria
    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    // Registros - CRUD
    public function guardar()
    {
        $resultado = '';
        if (!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();;
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }

        return $resultado;
    }

    // Obtener todos los registros
    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Buscar un registro por su ID
    public static function find($id)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = " . self::$db->real_escape_string($id);
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Obtener registros con un límite
    public static function get($limite)
    {
        if (!is_numeric($limite) || ($limite <= 0)) {
            throw new \InvalidArgumentException("El límite debe ser un número positivo");
        }
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . self::$db->real_escape_string($limite);
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Buscar registros por columna y valor
    public static function where($columna, $valor)
    {
        if (!in_array($columna, static::$columnasDB)) {
            throw new \InvalidArgumentException("La columna $columna no existe en la tabla");
        }

        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '" . self::$db->real_escape_string($valor) . "'";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Buscar un único registro por columna y valor
    public static function whereFirst($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '" . self::$db->real_escape_string($valor) . "' LIMIT 1";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Crear nuevo registro
    public function crear()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Construir la consulta SQL 
        $columnas = implode(',', array_keys($atributos));
        $valores = implode(', ', array_values($atributos));
        $query = "INSERT INTO " . static::$tabla . "($columnas) VALUES ($valores)";

        // Ejecutar query
        $resultado = self::$db->query($query);
        if (!$resultado) {
            throw new \Exception("Error al crear: " . self::$db->error);
        }
        return [
            'resultado' => $resultado,
            'id' => self::$db->insert_id
        ];
    }

    public function actualizar()
    {
        if (is_null($this->id)) {
            throw new \Exception("No se puede actualizar un registro sin ID");
        }

        // Sanitizar atributos
        $atributos = $this->sanitizarAtributos();

        // Verificar que haya atributos para actualizar
        if (empty($atributos)) {
            throw new \Exception("No hay atributos para actualizar");
        }

        $set = [];
        foreach ($atributos as $columna => $valor) {
            $set[] = "$columna = $valor";
        }

        // Corrección: Añadir espacio después del nombre de la tabla
        $query = "UPDATE " . static::$tabla . " SET ";  // <-- Espacio añadido aquí
        $query .= implode(', ', $set);
        $query .= " WHERE id = '" . self::$db->real_escape_string($this->id) . "'";
        $query .= " LIMIT 1";

        // Depuración (opcional)
        // error_log("Consulta SQL: " . $query);

        $resultado = self::$db->query($query);
        if (!$resultado) {
            throw new \Exception("Error al actualizar: " . self::$db->error . "\nConsulta: " . $query);
        }

        return $resultado;
    }

    // Eliminar un registro por su ID
    public function eliminar()
    {
        if (is_null($this->id)) {
            throw new \Exception("No se puede eliminar un registro sin ID");
        }

        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->real_escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);

        if (!$resultado) {
            throw new \Exception("Error al eliminar: " . self::$db->error);
        }

        return $resultado;
    }

    public static function paginate($porPagina = 10, $paginaActual = 1, $orderBy = '')
    {
        // Validar Parámetros
        $porPagina = (int)$porPagina;
        $paginaActual = (int)$paginaActual;

        if ($porPagina <= 0 || $paginaActual <= 0) {
            throw new \InvalidArgumentException("Los valores deben ser números positivos");
        }

        // Calcular offset
        $offset = ($paginaActual - 1) * $porPagina;

        // Construir la consulta
        $query = " SELECT * FROM " . static::$tabla;
        if ($orderBy) {
            $query .= " " . $orderBy;
        }

        $query .= " LIMIT " . self::$db->real_escape_string($porPagina) . " OFFSET " . self::$db->real_escape_string($offset);

        return self::consultarSQL($query);
    }

    public static function count()
    {
        $query = " SELECT COUNT(*) as total FROM " . static::$tabla;
        $resultado = self::$db->query($query);
        $fila = $resultado->fetch_assoc();
        return (int)$fila['total'];
    }
}
