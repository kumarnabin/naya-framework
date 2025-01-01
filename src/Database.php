<?php

namespace Konnect\NayaFramework;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private $pdo;

    public function __construct() {
        $this->loadEnv();
        $this->connect();
    }

    // Load environment variables
    private function loadEnv() {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
    }

    // Establish a PDO connection
    private function connect() {
        try {
            $dbPath = $_ENV['DB_DATABASE'];
            $this->pdo = new PDO("sqlite:$dbPath");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Expose the PDO instance
    public function getConnection() {
        return $this->pdo;
    }
}