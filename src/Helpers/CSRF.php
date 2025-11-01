<?php

namespace App\Helpers;

class CSRF
{
    /** Genera un token CSRF y lo almacena en la sesión
     * si no existe
     * @return string
     */

    public static function generateToken(){
        if(empty($_SESSION['csrf_token']))
        {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Valida un token CSRF
     * @param string $token
     * @return bool
     */

     public static function validateToken($token){
        if(!is_string($token))
        {
            return false;
        }

        // Verificar que el token de sesión existe
        if(!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']))
        {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
     }

     /**
      * Genera el campo hidden para el formulario
      @return string
      */

      public static function getHiddenInput()
      {
        return '<input type="hidden" name="_csrf" value="' . self::generateToken() . '">';
      }
}