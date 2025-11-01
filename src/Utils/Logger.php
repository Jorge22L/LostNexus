<?php

namespace App\Utils;

class Logger
{
    private static $logFile;
    private static $instance = null;
    private static $debug = false;

    private function __construct()
    {
        if(!getenv('DEBUG') && file_exists(dirname(__DIR__, 2) . '/.env')){
            $lines = file(dirname(__DIR__, 2) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach($lines as $line)
            {
                if(strpos(trim($line), '#') === 0)
                {
                    continue;
                }
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value, "\"'");
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }

        self::$debug = filter_var(getenv('DEBUG'), FILTER_VALIDATE_BOOLEAN);

        // Configurar el archivo de log en el directorio raíz/logs
        $logDir = dirname(__DIR__, 2) . '/logs';
        
        // Crear el directorio si no existe
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        self::$logFile = $logDir . '/app_' . date('Y-m-d') . '.log';
        
        // Crear el archivo si no existe
        if (!file_exists(self::$logFile)) {
            touch(self::$logFile);
            chmod(self::$logFile, 0644);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Escribe un mensaje en el archivo de log
     * 
     * @param string $message Mensaje a registrar
     * @param string $level Nivel de error (INFO, WARNING, ERROR, DEBUG, etc.)
     * @param array $context Datos adicionales para incluir en el log
     * @return bool true si se escribió correctamente, false en caso contrario
     */
    public static function log($message, $level = 'INFO', array $context = [])
    {
        // Si no está en modo debug, solo registrar error
        if(!self::$debug && !in_array(strtoupper($level), ['ERROR', 'WARNING', 'CRITICAL', 'EMERGENCY', 'ALERT'])){
            return false;
        }
        $instance = self::getInstance();
        
        $timestamp = date('[Y-m-d H:i:s]');
        $level = strtoupper($level);
        
        // Formato: [fecha] [nivel] mensaje {contexto}
        $logMessage = sprintf(
            "%s [%s] %s",
            $timestamp,
            str_pad($level, 8),  // Asegura que el nivel ocupe 8 caracteres
            $message
        );
        
        // Agregar contexto si existe
        if (!empty($context)) {
            $logMessage .= ' ' . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        // Agregar salto de línea
        $logMessage .= PHP_EOL;
        
        // Escribir en el archivo
        return file_put_contents(self::$logFile, $logMessage, FILE_APPEND) !== false;
    }

    /**
     * Registra un error
     */
    public static function error($message, array $context = [])
    {
        return self::log($message, 'ERROR', $context);
    }

    /**
     * Registra una advertencia
     */
    public static function warning($message, array $context = [])
    {
        return self::log($message, 'WARNING', $context);
    }

    /**
     * Registra información
     */
    public static function info($message, array $context = [])
    {
        return self::log($message, 'INFO', $context);
    }

    /**
     * Registra información de depuración
     */
    public static function debug($message, array $context = [])
    {
        return self::log($message, 'DEBUG', $context);
    }

    /**
     * Maneja los errores de PHP
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        $errorType = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSING ERROR',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE ERROR',
            E_CORE_WARNING => 'CORE WARNING',
            E_COMPILE_ERROR => 'COMPILE ERROR',
            E_COMPILE_WARNING => 'COMPILE WARNING',
            E_USER_ERROR => 'USER ERROR',
            E_USER_WARNING => 'USER WARNING',
            E_USER_NOTICE => 'USER NOTICE',
            E_STRICT => 'STRICT NOTICE',
            E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER DEPRECATED',
        ];

        $level = $errorType[$errno] ?? 'UNKNOWN';
        $message = "$errstr in $errfile on line $errline";
        
        self::log($message, $level);
        
        // No ejecutar el gestor de errores interno de PHP
        return true;
    }

    /**
     * Maneja las excepciones no capturadas
     */
    public static function handleException(\Throwable $exception)
    {
        $message = sprintf(
            'Uncaught Exception: %s in %s on line %d',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        
        self::error($message, [
            'trace' => $exception->getTraceAsString(),
            'code' => $exception->getCode(),
            'previous' => $exception->getPrevious() ? $exception->getPrevious()->getMessage() : null
        ]);
        
        // No ejecutar el gestor de excepciones interno de PHP
        return true;
    }

    /**
     * Configura el manejo de errores y excepciones
     */
    public static function register()
    {
        // Configurar el manejador de errores
        set_error_handler([self::class, 'handleError']);
        
        // Configurar el manejador de excepciones
        set_exception_handler([self::class, 'handleException']);
        
        // Configurar para mostrar todos los errores
        error_reporting(E_ALL);
        ini_set('display_errors', self::$debug ? '1' : '0');
        ini_set('log_errors', '1');
        
        // Configurar el archivo de log de errores de PHP
        $logDir = dirname(__DIR__, 2) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        ini_set('error_log', $logDir . '/php_errors.log');
    }
}
