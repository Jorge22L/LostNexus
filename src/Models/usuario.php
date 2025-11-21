<?php

namespace App\Models;

class Usuario extends ActiveRecord
{
    // Atributos
    protected static $tabla = "tbl_usuario";
    protected static $columnasDB = [
        "id",
        "id_rol",
        "nombre",
        "apellido",
        "nombre_usuario",
        "pwd",
        "token"
    ];

    public $id;
    public $id_rol;
    public $nombre;
    public $apellido;
    public $nombre_usuario;
    public $pwd;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args["id"] ?? null;
        $this->id_rol = $args["id_rol"] ?? null;
        $this->nombre = $args["nombre"] ?? '';
        $this->apellido = $args["apellido"] ?? '';
        $this->nombre_usuario = $args["nombre_usuario"] ?? '';
        $this->pwd = $args["pwd"] ?? '';
    }

    // Validaciones
    public function validarNuevaCuenta()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if (!$this->apellido) {
            self::$alertas['error'][] = 'El apellido es obligatorio';
        }

        if (!$this->nombre_usuario) {
            self::$alertas['error'][] = 'El nombre de usuario es obligatorio';
        }

        if (!$this->pwd || strlen($this->pwd) < 6) {
            self::$alertas['error'][] = 'El password es obligatorio y debe tener al menos 6 caracteres';
        }

        if (!$this->id_rol) {
            self::$alertas['error'][] = 'El rol es obligatorio';
        }

        return self::$alertas;
    }

    public function validarLogin()
    {
        if (!$this->nombre_usuario) {
            self::$alertas['error'][] = "El nombre de usuario es obligatorio";
        }

        if (!$this->pwd) {
            self::$alertas['error'][] = "El password es obligatorio";
        }

        return self::$alertas;
    }

    public function validarNombreUsuario()
    {
        if (!$this->nombre_usuario) {
            self::$alertas['error'][] = "El nombre de usuario es obligatorio";
        }

        return self::$alertas;
    }

    public function validarPassword()
    {
        if (!$this->pwd) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }

        return self::$alertas;
    }

    // Revisa si el usuario existe
    public function existeUsuario()
    {
        $query = " SELECT * FROM " . self::$tabla . " WHERE nombre_usuario = '" . $this->nombre_usuario . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if ($resultado->num_rows) {
            self::$alertas['error'][] = 'El nombre de usuario ya existe';
        }

        return $resultado;
    }

    public function hashPassword()
    {
        $this->pwd = password_hash($this->pwd, PASSWORD_BCRYPT);
    }

    public function crearToken()
    {
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password)
    {
        if (empty($this->pwd)) {
            self::$alertas['error'][] = 'La contraseña no está configurada';
            return false;
        }

        if (!password_verify($password, $this->pwd)) {
            self::$alertas['error'][] = 'Usuario o contraseña incorrectos';
            return false;
        }

        return true;
    }

    public static function filtrar($filtros = [], $porPagina = 10, $pagina = 1)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE 1 = 1";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (nombre LIKE ? OR apellido LIKE ? OR nombre_usuario LIKE ?)";
            $searchTerm = "%" . $filtros['busqueda'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

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
            $query .= " AND (nombre LIKE ? OR apellido LIKE ? OR nombre_usuario LIKE ?)";
            $searchTerm = "%" . $filtros['busqueda'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            $types .= 'sss'; // Tipo string para cada parámetro
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
    public function getRol()
    {
        if ($this->id_rol) {
            return Rol::find($this->id_rol);
        }
        return null;
    }
}
