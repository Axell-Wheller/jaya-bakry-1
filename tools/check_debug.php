<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT id, created_at, total_amount FROM orders ORDER BY id DESC LIMIT 1");
$order = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Latest Order: " . print_r($order, true);

if (file_exists('debug_wa.txt')) {
    echo "\nDebug Log Content:\n";
    echo file_get_contents('debug_wa.txt');
} else {
    echo "\nDebug Log File NOT Found.";
}
?>
