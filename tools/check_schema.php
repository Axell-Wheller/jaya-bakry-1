<?php
require __DIR__ . '/../includes/db.php';

try {
    $result = $pdo->query("PRAGMA table_info(orders)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
