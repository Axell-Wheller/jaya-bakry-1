<?php
require 'includes/db.php';

$token = 'Fcbqd4ggmue63NqjEHgC';

try {
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('fonnte_token', ?)");
    $stmt->execute([$token]);
    echo "Fonnte Token berhasil disimpan: " . $token;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
