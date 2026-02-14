<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT value FROM settings WHERE key = 'whatsapp_number'");
echo "Current Admin WA: " . $stmt->fetchColumn();
?>
