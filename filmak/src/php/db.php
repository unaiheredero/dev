<?php
class Database {
    private $pdo;

    public function __construct($host, $db, $user, $pass) {
        $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    }

    public function getConnection() {
        return $this->pdo;
    }
}
