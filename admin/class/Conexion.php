<?php

declare(strict_types=1);

final class Conexion
{
    private static ?PDO $instancia = null;

    private function __construct() {}

    public static function get(): PDO
    {
        if (self::$instancia === null) {
            $cfg = require __DIR__ . '/../../config.php';
            $db  = $cfg['db'];

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $db['host'],
                $db['name'],
                $db['charset'],
            );

            self::$instancia = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$instancia;
    }
}
