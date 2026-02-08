<?php
// Database connection class using PDO
require_once __DIR__ . '/../config/config.php';

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

// Helper functions for common database operations
function executeQuery($sql, $params = []) {
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() : null;
}

function insertData($table, $data) {
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $values = array_values($data);
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = executeQuery($sql, $values);
    
    return $stmt ? $stmt->rowCount() : false;
}

function updateData($table, $data, $where, $whereParams = []) {
    $setClause = [];
    $values = [];
    
    foreach ($data as $column => $value) {
        $setClause[] = "$column = ?";
        $values[] = $value;
    }
    
    $setClause = implode(', ', $setClause);
    $values = array_merge($values, $whereParams);
    
    $sql = "UPDATE $table SET $setClause WHERE $where";
    $stmt = executeQuery($sql, $values);
    
    return $stmt ? $stmt->rowCount() : false;
}

function deleteData($table, $where, $params = []) {
    $sql = "DELETE FROM $table WHERE $where";
    $stmt = executeQuery($sql, $params);
    
    return $stmt ? $stmt->rowCount() : false;
}
?>
