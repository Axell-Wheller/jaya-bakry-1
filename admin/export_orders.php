<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Access Denied');
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="transaksi_jaya_bakry_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Header Row
fputcsv($output, ['Order ID', 'Tanggal', 'Pelanggan', 'Alamat', 'Metode Pembayaran', 'Status', 'Total (Rp)']);

// Fetch Orders
$sql = "SELECT o.id, o.created_at, u.username, o.shipping_address, o.payment_method, o.status, o.total_amount 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$stmt = $pdo->query($sql);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
