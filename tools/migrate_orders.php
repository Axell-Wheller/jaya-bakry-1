<?php
require __DIR__ . '/../includes/db.php';

try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_method TEXT DEFAULT 'pickup'"); // pickup or delivery
    $pdo->exec("ALTER TABLE orders ADD COLUMN location_data TEXT"); // JSON or text address
    $pdo->exec("ALTER TABLE orders ADD COLUMN proof_of_payment TEXT"); // URL to image
    
    echo "Columns added successfully.";
} catch (PDOException $e) {
    echo "Error (maybe columns already exist): " . $e->getMessage();
}
?>
