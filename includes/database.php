<?php

use App\Models\ActiveRecord;

// Load environment variables first
require_once __DIR__ . '/env.php';

// Database connection
$db = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);

// Check connection
if ($db->connect_error) {
    error_log("Error de conexión: " . $db->connect_error);
    die("Error de conexión a la base de datos. Por favor, intente más tarde.");
}

// Set charset
$charset = $_ENV['DB_CHARSET'];
if (!$db->set_charset($charset)) {
    error_log("Error al establecer el charset: " . $db->error);
    // Fall back to utf8 if utf8mb4 fails
    $db->set_charset('utf8');
}

// Set the database connection in ActiveRecord
if (class_exists('App\\Models\\ActiveRecord')) {
    ActiveRecord::setDB($db);
} else {
    error_log("Error: No se pudo encontrar la clase ActiveRecord");
    die("Error crítico del sistema. Por favor, contacte al administrador.");
}

// No need to return anything, we're setting the static property