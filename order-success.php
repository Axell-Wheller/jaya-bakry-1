<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch Order
$stmt = $pdo->prepare("SELECT o.*, u.username, u.full_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='container mx-auto py-12 text-center'>Pesanan tidak ditemukan.</div>";
    include 'includes/footer.php';
    exit;
}

// Fetch Settings for WhatsApp Number
$wa_number = '628123456789'; // Default
$stmt_settings = $pdo->query("SELECT value FROM settings WHERE key = 'whatsapp_number'");
$setting = $stmt_settings->fetch();
if ($setting && !empty($setting['value'])) {
    $wa_number = $setting['value'];
}

// Format WhatsApp Message
$message = "Halo Admin Jaya Bakry, saya ingin konfirmasi pembayaran untuk Pesanan #$id.\n";
$message .= "Total: Rp " . number_format($order['total_amount'], 0, ',', '.') . "\n";
$message .= "Mohon diproses. Terima kasih.";
$wa_url = "https://wa.me/" . preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $wa_number)) . "?text=" . urlencode($message);

// Handle Payment Proof Upload
$upload_success = '';
$upload_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof'])) {
    if ($_FILES['payment_proof']['error'] === 0) {
        require_once 'includes/cloudinary.php';
        $uploaded_url = uploadImage($_FILES['payment_proof']);
        
        if ($uploaded_url) {
            $stmt_update = $pdo->prepare("UPDATE orders SET proof_of_payment = ?, status = 'processing' WHERE id = ?");
            $stmt_update->execute([$uploaded_url, $id]);
            $upload_success = "Bukti pembayaran berhasil diupload!";
            // Refresh order data
            $stmt->execute([$id]);
            $order = $stmt->fetch();
        } else {
            $upload_error = "Gagal mengupload gambar.";
        }
    } else {
        $upload_error = "Terjadi kesalahan saat upload.";
    }
}
?>

<div class="bg-gray-50 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 text-center">
            <?php 
            $is_pending_payment = in_array($order['payment_method'], ['transfer', 'qris', 'ewallet']) && empty($order['proof_of_payment']);
            ?>

            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full <?php echo $is_pending_payment ? 'bg-yellow-100' : 'bg-green-100'; ?>">
                <?php if ($is_pending_payment): ?>
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                <?php else: ?>
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                <?php endif; ?>
            </div>
            
            <h2 class="mt-6 text-3xl font-extrabold text-brown-900">
                <?php echo $is_pending_payment ? 'Selesaikan Pembayaran' : 'Pesanan Berhasil!'; ?>
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Nomor Pesanan: <span class="font-bold">#<?php echo $id; ?></span>
            </p>
            
            <div class="mt-6 border-t border-gray-200 pt-6">
                <p class="text-lg font-medium text-brown-900">Total Pembayaran</p>
                <p class="text-3xl font-bold text-amber-600 mt-2">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
            </div>

            <!-- Pickup Code Display -->
            <?php if ($order['delivery_method'] === 'pickup'): ?>
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <p class="text-sm text-blue-800 font-medium mb-2">Kode Pengambilan (Tunjukkan di Toko)</p>
                    <p class="text-4xl font-mono font-bold text-blue-900 tracking-wider">#<?php echo $id; ?></p>
                </div>
            <?php endif; ?>

            <!-- Payment Proof Section -->
            <?php if (in_array($order['payment_method'], ['transfer', 'qris', 'ewallet'])): ?>
                
                <?php if ($order['payment_method'] === 'transfer'): ?>
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                        <p class="text-sm text-yellow-800 font-medium mb-2">Silakan transfer ke:</p>
                        <p class="font-bold text-gray-900"><?php echo nl2br(htmlspecialchars($setting_bank['value'] ?? 'BCA 1234567890 a.n Jaya Bakry')); ?></p>
                    </div>
                <?php elseif ($order['payment_method'] === 'qris'): ?>
                     <?php
                        $stmt_qris = $pdo->query("SELECT value FROM settings WHERE key = 'qris_image'");
                        $qris = $stmt_qris->fetch();
                        $qris_image = $qris['value'] ?? null;
                     ?>
                     <div class="mt-6 bg-white border border-gray-200 rounded-md p-4 text-center mb-4">
                        <p class="text-sm text-gray-800 font-medium mb-4">Scan QRIS untuk membayar</p>
                        <?php if($qris_image): ?>
                            <img src="<?php echo htmlspecialchars($qris_image); ?>" alt="QRIS Code" class="mx-auto h-64 w-64 object-contain border">
                        <?php else: ?>
                            <p class="text-red-500">QRIS belum tersedia.</p>
                        <?php endif; ?>
                    </div>
                <?php elseif ($order['payment_method'] === 'ewallet'): ?>
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                        <p class="text-sm text-blue-800 font-medium mb-2">Silakan transfer E-Wallet ke:</p>
                        <p class="font-bold text-gray-900">GoPay/OVO/Dana: <?php echo htmlspecialchars($wa_number); ?></p>
                        <p class="text-xs text-blue-600 mt-1">Gunakan nomor WhatsApp admin.</p>
                    </div>
                <?php endif; ?>

                <div class="mt-6 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bukti Pembayaran</h3>
                    
                    <?php if ($upload_success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $upload_success; ?></div>
                    <?php endif; ?>
                    <?php if ($upload_error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"><?php echo $upload_error; ?></div>
                    <?php endif; ?>

                    <?php if ($order['proof_of_payment']): ?>
                        <div class="text-center">
                            <img src="<?php echo htmlspecialchars($order['proof_of_payment']); ?>" alt="Bukti Pembayaran" class="mx-auto max-h-64 rounded border p-1">
                            <p class="text-sm text-green-600 font-medium mt-2">Bukti pembayaran telah diterima.</p>
                        </div>
                    <?php else: ?>
                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Upload Foto Bukti Transfer/QRIS</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-brown-600 hover:text-brown-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brown-500">
                                                <span>Upload a file</span>
                                                <input id="file-upload" name="payment_proof" type="file" class="sr-only" required>
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brown-600 hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                                Kirim Bukti Pembayaran
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <div class="mt-6 text-center">
                         <a href="<?php echo $wa_url; ?>" target="_blank" class="text-green-600 hover:text-green-700 font-medium">
                            Perlu bantuan? Chat WhatsApp
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- COD or E-Wallet (Auto confirmed usually, but simplest to generic msg) -->
                 <div class="mt-6 text-center">
                    <p class="text-gray-600 mb-4">Pesanan Anda sedang diproses.</p>
                    <a href="index.php" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-brown-700 bg-brown-100 hover:bg-brown-200">
                        Kembali ke Beranda
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
