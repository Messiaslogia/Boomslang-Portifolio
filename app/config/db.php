<?php
// app/config/db.php

// O vendor está na raiz do projeto, então precisa subir 2 níveis
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..'); // Raiz do projeto
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

class Db
{
    public $pdo;

    public function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Retorna JSON em vez de HTML para APIs
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'error' => 'Erro na conexão com banco: ' . $e->getMessage()
            ]);
            exit;
        }
    }
}
?>