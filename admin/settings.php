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
        'store_email' => $_POST['store_email'] ?? '',
        'instagram_username' => $_POST['instagram_username'] ?? '',
        'facebook_username' => $_POST['facebook_username'] ?? '',
        'fonnte_token' => $_POST['fonnte_token'] ?? ''
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
        <?php include 'includes/sidebar.php'; ?>
        <div class="md:pl-64 flex flex-col flex-1">
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
                                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">Nomor WhatsApp (untuk Konfirmasi & Kontak)</label>
                                            <div class="mt-1">
                                                <input type="text" name="whatsapp_number" id="whatsapp_number" placeholder="Contoh: 628123456789" value="<?php echo htmlspecialchars($current_settings['whatsapp_number'] ?? ''); ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                                <p class="mt-1 text-xs text-gray-500">Gunakan format 628xxx tanpa + atau 0 di depan.</p>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="store_email" class="block text-sm font-medium text-gray-700">Email Toko</label>
                                            <div class="mt-1">
                                                <input type="email" name="store_email" id="store_email" value="<?php echo htmlspecialchars($current_settings['store_email'] ?? ''); ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="instagram_username" class="block text-sm font-medium text-gray-700">Username Instagram (Tanpa @)</label>
                                            <div class="mt-1">
                                                <input type="text" name="instagram_username" id="instagram_username" placeholder="jayabakry" value="<?php echo htmlspecialchars($current_settings['instagram_username'] ?? ''); ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label for="facebook_username" class="block text-sm font-medium text-gray-700">Username Facebook</label>
                                            <div class="mt-1">
                                                <input type="text" name="facebook_username" id="facebook_username" placeholder="jayabakry" value="<?php echo htmlspecialchars($current_settings['facebook_username'] ?? ''); ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
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
                                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <!-- Heroicon name: solid/exclamation -->
                                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm text-yellow-700">
                                                            Ingin mengatur <b>Metode Pembayaran</b> atau <b>Rekening Bank</b>?
                                                            <a href="payment_methods.php" class="font-medium underline text-yellow-700 hover:text-yellow-600">
                                                                Klik di sini untuk kelola metode pembayaran.
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
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
