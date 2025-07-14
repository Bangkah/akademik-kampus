<?php
class Database {
    private $host = "127.0.0.1";
    private $dbname = "akademik";
    private $username = "root";
    private $password = "";
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8", 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }
        return $this->conn;
    }
    
    // Method untuk test koneksi
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                $stmt = $conn->query("SELECT 1");
                return $stmt ? true : false;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Method untuk mendapatkan info database
    public function getDatabaseInfo() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT VERSION() as version, DATABASE() as current_db");
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
}
?>