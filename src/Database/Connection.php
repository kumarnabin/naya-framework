<?php

namespace Konnect\NayaFramework\Database;

use PDO;
use PDOException;

class Connection
{
    private $pdo;

    public function __construct()
    {
        $this->connect();
    }

    // Establish a PDO connection
    private function connect(): void
    {
        try {
            $dbDriver = env('DB_DRIVER', 'sqlite'); // Default to SQLite
            if ($dbDriver === 'sqlite') {
                $dbPath = "../" . env('DB_DATABASE', 'database.sqlite');
                if (!file_exists($dbPath)) {
                    touch($dbPath);
                }
                $this->pdo = new PDO("sqlite:$dbPath");
            } elseif ($dbDriver === 'mysql') {
                $host = env('DB_HOST', '127.0.0.1');
                $dbname = env('DB_DATABASE', 'test');
                $username = env('DB_USERNAME', 'root');
                $password = env('DB_PASSWORD', '');
                $port = env('DB_PORT', 3306);

                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
                $this->pdo = new PDO($dsn, $username, $password);
            } else {
                throw new PDOException("Unsupported database driver: $dbDriver");
            }

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Expose the PDO instance
    public function getConnection()
    {
        return $this->pdo;
    }
}
