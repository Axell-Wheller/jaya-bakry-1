<?php
require __DIR__ . '/../includes/db.php';

try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN order_code TEXT");
    echo "Column 'order_code' added successfully.\n";
    

    $stmt = $pdo->query("SELECT id FROM orders WHERE order_code IS NULL");
    $orders = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $updateStmt = $pdo->prepare("UPDATE orders SET order_code = ? WHERE id = ?");
    
    foreach ($orders as $id) {
        $code = 'JB-OLD-' . str_pad($id, 4, '0', STR_PAD_LEFT);
        $updateStmt->execute([$code, $id]);
    }
    echo "Updated " . count($orders) . " existing orders with default codes.\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'duplicate column name') !== false) {
        echo "Column 'order_code' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
