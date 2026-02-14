<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('debug_post.txt', print_r($_POST, true)); // DEBUG
    $settings = [
        'store_name' => $_POST['store_name'],
        'store_address' => $_POST['store_address'],
        'store_phone' => $_POST['store_phone'],
        'whatsapp_number' => $_POST['whatsapp_number'],
        'bank_account' => $_POST['bank_account']
    ];
    
    if (isset($_FILES['qris_image']) && $_FILES['qris_image']['error'] === 0) {
        require_once '../includes/cloudinary.php';
        $uploaded_url = uploadImage($_FILES['qris_image']);
        if ($uploaded_url) {
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('qris_image', ?)");
            $stmt->execute([$uploaded_url]);
        }
    }

    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
        $stmt->execute([$key, $value]);
    }
    $success = "Pengaturan berhasil disimpan.";
}

// Fetch Settings
$current_settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
while ($row = $stmt->fetch()) {
    $current_settings[$row['key']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin Jaya Bakry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#FDF6E3',
                        brown: {
                            50: '#EFEBE9',
                            100: '#D7CCC8',
                            200: '#BCAAA4',
                            300: '#A1887F',
                            400: '#8D6E63',
                            500: '#795548',
                            600: '#6D4C41',
                            700: '#5D4037', // Main Brown
                            800: '#4E342E',
                            900: '#3E2723',
                        },
                        amber: { 500: '#FFC107' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-brown-900">
                <div class="flex flex-col h-0 flex-1">
                    <div class="flex items-center h-16 flex-shrink-0 px-4 bg-brown-900">
                        <h1 class="text-xl font-bold text-white">Admin Jaya Bakry</h1>
                    </div>
                    <div class="flex-1 flex flex-col overflow-y-auto">
                        <nav class="flex-1 px-2 py-4 space-y-1">
                            <a href="dashboard.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Dashboard
                            </a>
                            <a href="products.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Produk
                            </a>
                            <a href="orders.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pesanan
                            </a>
                             <a href="settings.php" class="bg-brown-800 text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pengaturan
                            </a>
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex border-t border-brown-800 p-4">
                        <a href="../logout.php" class="text-brown-100 hover:text-white text-sm font-medium">Keluar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <h1 class="text-2xl font-semibold text-gray-900">Pengaturan Toko</h1>
                    </div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
                        <?php if ($success): ?>
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-4">
                                            <label for="store_name" class="block text-sm font-medium text-gray-700">Nama Toko</label>
                                            <div class="mt-1">
                                                <input type="text" name="store_name" id="store_name" value="<?php echo htmlspecialchars($current_settings['store_name'] ?? 'Jaya Bakry'); ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-6">
                                            <label for="store_address" class="block text-sm font-medium text-gray-700">Alamat Toko</label>
                                            <div class="mt-1">
                                                <textarea id="store_address" name="store_address" rows="3" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"><?php echo htmlspecialchars($current_settings['store_address'] ?? ''); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="store_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                            <div class="mt-1">
                                                <input type="text" name="store_phone" id="store_phone" value="<?php echo htmlspecialchars($current_settings['store_phone'] ?? ''); ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">Nomor WhatsApp (untuk Konfirmasi)</label>
                                            <div class="mt-1">
                                                <input type="text" name="whatsapp_number" id="whatsapp_number" placeholder="Contoh: 08123456789" value="<?php echo htmlspecialchars($current_settings['whatsapp_number'] ?? ''); ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-6">
                                            <label for="qris_image" class="block text-sm font-medium text-gray-700">Upload QRIS Code</label>
                                            <div class="mt-1 flex items-center">
                                                 <?php if (isset($current_settings['qris_image']) && $current_settings['qris_image']): ?>
                                                    <span class="h-24 w-24 overflow-hidden bg-gray-100 mr-4 border rounded-md">
                                                        <img class="h-full w-full object-contain" src="<?php echo htmlspecialchars($current_settings['qris_image']); ?>" alt="QRIS">
                                                    </span>
                                                <?php endif; ?>
                                                <input type="file" name="qris_image" id="qris_image" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-6">
                                            <label for="bank_account" class="block text-sm font-medium text-gray-700">Info Rekening Bank (untuk Pembayaran)</label>
                                            <div class="mt-1">
                                                <textarea id="bank_account" name="bank_account" rows="2" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"><?php echo htmlspecialchars($current_settings['bank_account'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-8">
                                        <button type="submit" class="bg-brown-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                                            Simpan Pengaturan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
