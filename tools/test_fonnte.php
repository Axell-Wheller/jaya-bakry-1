<?php
require 'includes/db.php';
require 'includes/whatsapp.php';

// Fetch Settings
$stmt = $pdo->query("SELECT key, value FROM settings WHERE key IN ('fonnte_token', 'whatsapp_number')");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$token = $settings['fonnte_token'] ?? 'TIDAK ADA';
$target = $settings['whatsapp_number'] ?? 'TIDAK ADA';

echo "Token: " . substr($token, 0, 5) . "...\n";
echo "Target: " . $target . "\n";

if ($token === 'TIDAK ADA' || $target === 'TIDAK ADA') {
    die("Data tidak lengkap.\n");
}

echo "Mengirim pesan test...\n";
$response = sendWhatsapp($target, "Tes Notifikasi dari Jaya Bakry Website. Waktu: " . date('Y-m-d H:i:s'));

echo "Response Fonnte:\n";
print_r($response);
?>
