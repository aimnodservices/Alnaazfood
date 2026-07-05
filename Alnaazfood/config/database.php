<?php
// ============================================
// AL-NAAZ FOOD - Database Configuration
// ============================================

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'al_naaz_food');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create connection
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to UTF-8
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        die("Database Connection Error: " . $e->getMessage());
    }
}

// Get single connection instance
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = getConnection();
    }
    return $conn;
}

// Execute query and return results
function executeQuery($sql, $params = [], $types = "") {
    $conn = getDB();
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Query Error: " . $e->getMessage() . " - SQL: " . $sql);
        return false;
    }
}

// Get single row
function getRow($sql, $params = [], $types = "") {
    $result = executeQuery($sql, $params, $types);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get all rows
function getRows($sql, $params = [], $types = "") {
    $result = executeQuery($sql, $params, $types);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

// Insert data and return last insert ID
function insertData($sql, $params = [], $types = "") {
    $conn = getDB();
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $lastId = $stmt->insert_id;
        $stmt->close();
        
        return $lastId;
    } catch (Exception $e) {
        error_log("Insert Error: " . $e->getMessage() . " - SQL: " . $sql);
        return false;
    }
}

// Update data
function updateData($sql, $params = [], $types = "") {
    return executeQuery($sql, $params, $types);
}

// Delete data
function deleteData($sql, $params = [], $types = "") {
    return executeQuery($sql, $params, $types);
}

// Escape string for security
function escapeString($str) {
    $conn = getDB();
    return $conn->real_escape_string($str);
}

// Get total count
function getCount($table, $where = "", $params = []) {
    $sql = "SELECT COUNT(*) as total FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    $result = executeQuery($sql, $params);
    if ($result) {
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
    return 0;
}
?>