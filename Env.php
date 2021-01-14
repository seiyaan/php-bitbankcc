<?php

namespace seiyaan\trade;
use Dotenv\Dotenv;

class Env {
    private static $dotenv;

    public static function get($key){
        if((self::$dotenv instanceof Dotenv) === false){
            self::$dotenv = Dotenv::createImmutable(__DIR__);
            self::$dotenv->load();
        }
        return array_key_exists($key, $_ENV) ? $_ENV[$key] : null;
    }
}
