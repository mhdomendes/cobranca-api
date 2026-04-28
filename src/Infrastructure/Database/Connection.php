<?php

namespace Infrastructure\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = "pgsql:host=localhost;port=5432;dbname=cobranca";

                self::$instance = new PDO(
                    $dsn,
                    "postgres",     // usuário
                    "root",     // senha
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                throw new \Exception("Erro ao conectar no banco: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}