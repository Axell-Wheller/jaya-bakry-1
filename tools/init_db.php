<?php
require __DIR__ . '/../includes/db.php';

try {
    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    if ($schema === false) {
        die("Could not read schema file.");
    }
    
    $pdo->exec($schema);
    echo "Database initialized successfully.\n";
    
} catch(PDOException $e) {
    echo "Error initializing database: " . $e->getMessage();
}
?>
