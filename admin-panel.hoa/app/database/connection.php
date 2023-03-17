<?php

$databaseType = "mysql";
$hostname = "localhost";
$username = "root";
$password = "";
$database = "hoa_db_v2";

try {
    switch ($databaseType) {
        case "mysql":
            // Establish database connection
            $db_connection = new mysqli($hostname, $username, $password, $database);
            
            // Check for connection errors
            if ($db_connection->connect_errno) {
                throw new Exception("Failed to connect to MySQL: " . $db_connection->connect_error);
            }
            
            // Function to create a prepared statement
            function prepare_statement($query) {
                global $db_connection;
                if (!$stmt = $db_connection->prepare($query)) {
                    echo "Failed to prepare statement: (" . $db_connection->errno . ") " . $db_connection->error;
                    exit();
                }
                return $stmt;
            }
            
            break;
        default:
            throw new Exception("Unsupported database type: " . $databaseType);
            break;
    }
} catch (Exception $e) {
    // Handle any errors that occur during database connection
    echo "SERVER ERROR: " . $e->getMessage();
    exit();
}

?>
