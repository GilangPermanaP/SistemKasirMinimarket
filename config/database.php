<?php
class Database {
    private $host = '127.0.0.1';
    private $port = '3306';
    private $db = 'db_minimarket';
    private $user = 'root';
    private $pass = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db);
            $this->conn->exec("USE " . $this->db);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $stmt = $this->conn->query("SHOW TABLES LIKE 'user'");
            if ($stmt->rowCount() === 0) {
                $sql = file_get_contents(__DIR__ . '/../database.sql');
                $this->conn->exec($sql);
            }
        } catch(PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
