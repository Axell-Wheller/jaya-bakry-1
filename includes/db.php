<?php
try {
    // Database configuration
    $db_path = __DIR__ . '/../database/store.db';
    
    // Create (connect to) SQLite database in file
    $pdo = new PDO('sqlite:' . $db_path);
    
    // Set errormode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create database directory if it doesn't exist (just in case)
    if (!file_exists(dirname($db_path))) {
        mkdir(dirname($db_path), 0777, true);
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
