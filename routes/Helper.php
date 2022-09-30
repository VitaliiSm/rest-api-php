<?php

namespace app\api\routes;

class Helper
{
    public static function trimPath(string $path): string
    {
        return '/' . rtrim(ltrim(trim($path), '/'), '/');
    }
}