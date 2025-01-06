<?php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "vivaporto";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        try {
            error_log("Attempting database connection to {$this->host}/{$this->db_name}");
            
            // Test if MySQL is running
            $test = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            
            error_log("Successfully connected to MySQL server");
            
            // Test if database exists
            $result = $test->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->db_name}'");
            if (!$result->fetch()) {
                error_log("Database {$this->db_name} does not exist");
                throw new Exception("Database {$this->db_name} does not exist");
            }
            
            error_log("Database {$this->db_name} exists");
            
            // Connect to the specific database
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                )
            );
            
            error_log("Successfully connected to database {$this->db_name}");
            
            // Test if users table exists
            $result = $this->conn->query("SHOW TABLES LIKE 'users'");
            if (!$result->fetch()) {
                error_log("Users table does not exist");
                throw new Exception("Users table does not exist");
            }
            
            error_log("Users table exists");
            return $this->conn;
            
        } catch(PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            error_log("Error Code: " . $e->getCode());
            error_log("Stack Trace: " . $e->getTraceAsString());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
}
