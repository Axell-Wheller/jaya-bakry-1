<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT id, name, image FROM products LIMIT 5");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($products);
?>
