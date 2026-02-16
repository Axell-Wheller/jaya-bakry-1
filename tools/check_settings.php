<?php
require __DIR__ . '/../includes/db.php';

$stmt = $pdo->query("SELECT * FROM settings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

echo "--- Current Settings ---\n";
echo "Fonnte Token: " . ($settings['fonnte_token'] ?? 'NOT SET') . "\n";
echo "WhatsApp Number: " . ($settings['whatsapp_number'] ?? 'NOT SET') . "\n";
echo "----------------------\n";
?>
