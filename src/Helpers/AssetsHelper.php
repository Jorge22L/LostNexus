<?php
namespace App\Helpers;

class AssetsHelper
{
    public static function js($file)
    {
        return '/js/' . $file . '?v=' . filemtime(__DIR__ . '/../../public/js/' . $file);
    }
}