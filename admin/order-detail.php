<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Include WhatsApp helper
require_once '../includes/whatsapp.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: orders.php');
    exit;
}

// Fetch Order Details FIRST to get phone number for notification
$stmt = $pdo->prepare("SELECT o.*, u.username, u.full_name, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Update Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    
    // Refresh order status in memory
    $order['status'] = $new_status;
    
    $success = "Status pesanan berhasil diperbarui.";

    // Send WhatsApp Notification
    $wa_status = "";
    if ($order['phone']) {
        $message = "Halo *" . $order['username'] . "*,\n\n";
        $message .= "Status pesanan Anda *" . ($order['order_code'] ?? "#" . $order['id']) . "* telah diperbarui menjadi: *" . strtoupper($new_status) . "*.\n\n";
        
        if ($new_status == 'processing') {
            $message .= "Pesanan Anda sedang kami proses/buat. Mohon ditunggu ya! 👨‍🍳";
        } elseif ($new_status == 'completed') {
            if ($order['delivery_method'] === 'delivery') {
                $message .= "Pesanan Anda sudah selesai dan sedang dalam proses pengiriman 🛵. Mohon pastikan nomor ini aktif saat kurir menghubungi.";
            } else {
                $message .= "Pesanan Anda sudah SIAP! 🛍️\nSilakan datang ke toko (Jaya Bakery) untuk mengambil pesanan Anda.";
            }
        } elseif ($new_status == 'cancelled') {
            $message .= "Mohon maaf, pesanan Anda telah dibatalkan. Silakan hubungi admin jika ada pertanyaan.";
        }
        
        $message .= "\n\nCek detail pesanan di website kami.";

        $response = sendWhatsapp($order['phone'], $message);
        
        // Check if response seems valid (Fonnte usually returns JSON)
        $res_data = json_decode($response, true);
        if ($res_data && isset($res_data['status']) && $res_data['status']) {
             $wa_status = " (Notifikasi WA terkirim)";
        } else {
             $wa_status = " (Gagal kirim WA. Cek Token/Koneksi)";
        }
    } else {
        $wa_status = " (Tidak ada nomor WA)";
    }
    
    $success .= $wa_status;
}

// Fetch Items (Moved after status logic so it doesn't affect flow)
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo $id; ?> - Admin Jaya Bakery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="md:pl-64 flex flex-col flex-1">
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 flex justify-between items-center">
                        <h1 class="text-2xl font-semibold text-gray-900">Detail Pesanan <?php echo htmlspecialchars($order['order_code'] ?? "#" . $id); ?></h1>
                        <a href="orders.php" class="text-brown-600 hover:text-brown-900 font-medium">Kembali ke Daftar</a>
                    </div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
                        <?php if (isset($success)): ?>
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Pelanggan</h3>
                            </div>
                            <div class="border-t border-gray-200">
                                <dl>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo htmlspecialchars($order['full_name']); ?> (<?php echo htmlspecialchars($order['username']); ?>)</dd>
                                    </div>
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo htmlspecialchars($order['phone']); ?></dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Alamat Pengiriman</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></dd>
                                    </div>
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Metode Pembayaran</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 uppercase"><?php echo htmlspecialchars($order['payment_method']); ?></dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Metode Pengiriman</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 capitalize">
                                            <?php 
                                            echo $order['delivery_method'] ?? 'Pickup'; 
                                            if ($order['delivery_method'] === 'delivery' && !empty($order['location_data'])) {
                                                $loc = json_decode($order['location_data'], true);
                                                if ($loc && isset($loc['lat'], $loc['lng'])) {
                                                    echo ' <a href="https://www.google.com/maps/dir/?api=1&destination=' . $loc['lat'] . ',' . $loc['lng'] . '" target="_blank" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 ml-2">🗺️ Rute ke Lokasi</a>';
                                                }
                                            }
                                            ?>
                                        </dd>
                                    </div>
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Bukti Pembayaran</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <?php if (!empty($order['proof_of_payment'])): ?>
                                                <a href="<?php echo htmlspecialchars($order['proof_of_payment']); ?>" target="_blank">
                                                    <img src="<?php echo htmlspecialchars($order['proof_of_payment']); ?>" alt="Bukti Pembayaran" class="h-32 object-contain border rounded">
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-500">Belum ada bukti pembayaran.</span>
                                            <?php endif; ?>
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Status Pesanan</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <form action="" method="POST" class="flex items-center space-x-2">
                                                <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm rounded-md">
                                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" class="bg-brown-600 hover:bg-brown-700 text-white px-3 py-2 rounded-md text-sm font-medium">Update</button>
                                            </form>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <h3 class="text-lg leading-6 font-medium text-gray-900 mt-8 mb-4">Item Pesanan</h3>
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <?php foreach ($items as $item): ?>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <div class="flex-shrink-0 h-10 w-10">
                                                                    <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($item['image'] ?: 'https://via.placeholder.com/100'); ?>" alt="">
                                                                </div>
                                                                <div class="ml-4">
                                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['name']); ?></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <?php echo $item['quantity']; ?>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                                            Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <!-- Total Row -->
                                                <tr class="bg-gray-50">
                                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total Pembayaran</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-lg">
                                                        Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
