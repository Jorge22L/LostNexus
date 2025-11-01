<?php 

function loadEnv($path)
{
    if(!file_exists($path))
    {
        throw new RuntimeException('.env no encontrado');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach($lines as $line)
    {
        if(strpos(trim($line), '#') === 0 || strpos($line, '=') === false)
        {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if(!array_key_exists($name, $_ENV))
        {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Cargar Variables
loadEnv(__DIR__ . '/../.env');

// Validar variables requeridas
$required = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'];
foreach($required as $var)
{
    if(empty($_ENV[$var]))
    {
        die("Error: La variable $var es requerida en .env");
    }
}