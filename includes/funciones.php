<?php 

function debugear($variable) : string 
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitiza el HTML
function s($html) : string 
{
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo):bool {
    if($actual !== $proximo)
    {
        return true;
    }

    return false;
}

// Funcion que revisa si el usuario está autenticado
function isAuth(): void{
    if(!isset($_SESSION['login'])){
        header('Location: /');
    }
}