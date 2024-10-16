<?php
class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verificar si el usuario existe y si la contraseña es válida
        return ($user && password_verify($password, $user['password'])) ? $user : false;
    }
    
    public function register($username, $password) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña
        $stmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");

        // Retorna true si el registro es exitoso, false si falla
        return $stmt->execute(['username' => $username, 'password' => $password_hashed]);
    }
}
