<?php
session_start();
require '../includes/db.php';
require '../includes/cloudinary.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM payment_methods WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $success = "Metode pembayaran berhasil dihapus.";
    } else {
        $name = $_POST['name'];
        $code = $_POST['code'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $requires_proof = isset($_POST['requires_proof']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $image_url = $_POST['current_image'] ?? null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploaded_url = uploadImage($_FILES['image']);
            if ($uploaded_url) {
                $image_url = $uploaded_url;
            } else {
                $error = "Gagal mengupload gambar.";
            }
        }
        
        if (!$error) {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update
                $stmt = $pdo->prepare("UPDATE payment_methods SET name=?, code=?, description=?, type=?, requires_proof=?, is_active=?, image=? WHERE id=?");
                $stmt->execute([$name, $code, $description, $type, $requires_proof, $is_active, $image_url, $_POST['id']]);
                $success = "Metode pembayaran berhasil diperbarui.";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO payment_methods (name, code, description, type, requires_proof, is_active, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $code, $description, $type, $requires_proof, $is_active, $image_url]);
                $success = "Metode pembayaran berhasil ditambahkan.";
            }
            $action = 'list';
        }
    }
}

// Fetch ID for Edit
$edit_item = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_item = $stmt->fetch();
}

// Fetch All for List
$payment_methods = $pdo->query("SELECT * FROM payment_methods ORDER BY is_active DESC, name ASC")->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Metode Pembayaran - Admin Jaya Bakery</title>
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
        <?php include 'includes/sidebar.php'; ?>
        <div class="md:pl-64 flex flex-col flex-1">
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 flex justify-between items-center">
                        <h1 class="text-2xl font-semibold text-gray-900">Kelola Metode Pembayaran</h1>
                        <?php if($action === 'list'): ?>
                            <a href="payment_methods.php?action=add" class="bg-brown-600 hover:bg-brown-700 text-white px-4 py-2 rounded-md font-medium text-sm">Tambah Metode</a>
                        <?php endif; ?>
                    </div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
                        <?php if ($success): ?>
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if ($action === 'list'): ?>
                            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                <ul class="divide-y divide-gray-200">
                                    <?php foreach ($payment_methods as $item): ?>
                                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium text-brown-600"><?php echo htmlspecialchars($item['name']); ?></p>
                                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($item['code']); ?></p>
                                                </div>
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $item['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                    <?php echo $item['is_active'] ? 'Aktif' : 'Non-Aktif'; ?>
                                                </span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="payment_methods.php?action=edit&id=<?php echo $item['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                                                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');" class="inline">
                                                    <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium ml-2">Hapus</button>
                                                </form>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <!-- Form Add/Edit -->
                            <div class="bg-white shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <form action="payment_methods.php" method="POST" enctype="multipart/form-data">
                                        <?php if ($edit_item): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                            <input type="hidden" name="current_image" value="<?php echo $edit_item['image']; ?>">
                                        <?php endif; ?>

                                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                            <div class="sm:col-span-3">
                                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Metode</label>
                                                <div class="mt-1">
                                                    <input type="text" name="name" id="name" required value="<?php echo $edit_item['name'] ?? ''; ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                                </div>
                                            </div>
                                            
                                            <div class="sm:col-span-3">
                                                <label for="code" class="block text-sm font-medium text-gray-700">Kode Unik (contoh: bca, qris)</label>
                                                <div class="mt-1">
                                                    <input type="text" name="code" id="code" required value="<?php echo $edit_item['code'] ?? ''; ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                                </div>
                                            </div>

                                            <div class="sm:col-span-6">
                                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi / Instruksi Pembayaran</label>
                                                <div class="mt-1">
                                                    <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"><?php echo $edit_item['description'] ?? ''; ?></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="sm:col-span-3">
                                                <label for="type" class="block text-sm font-medium text-gray-700">Tipe</label>
                                                <div class="mt-1">
                                                    <select name="type" id="type" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                                        <?php $types = ['transfer', 'qris', 'ewallet', 'cod']; ?>
                                                        <?php foreach ($types as $t): ?>
                                                            <option value="<?php echo $t; ?>" <?php echo ($edit_item['type'] ?? '') === $t ? 'selected' : ''; ?>><?php echo ucfirst($t); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="sm:col-span-6">
                                                <label for="image" class="block text-sm font-medium text-gray-700">Gambar (QRIS / Logo)</label>
                                                <div class="mt-1 flex items-center">
                                                    <?php if (isset($edit_item['image']) && $edit_item['image']): ?>
                                                        <span class="h-12 w-12 rounded overflow-hidden bg-gray-100 mr-4">
                                                            <img class="h-full w-full object-contain" src="<?php echo htmlspecialchars($edit_item['image']); ?>" alt="">
                                                        </span>
                                                    <?php endif; ?>
                                                    <input type="file" name="image" id="image" class="hidden">
                                                    <label for="image" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                                                        Upload Gambar
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="sm:col-span-6">
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input id="requires_proof" name="requires_proof" type="checkbox" <?php echo ($edit_item['requires_proof'] ?? 0) ? 'checked' : ''; ?> class="focus:ring-brown-500 h-4 w-4 text-brown-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="requires_proof" class="font-medium text-gray-700">Wajib Upload Bukti Pembayaran?</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="sm:col-span-6">
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input id="is_active" name="is_active" type="checkbox" <?php echo ($edit_item['is_active'] ?? 1) ? 'checked' : ''; ?> class="focus:ring-brown-500 h-4 w-4 text-brown-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="is_active" class="font-medium text-gray-700">Aktifkan Metode Ini</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-8">
                                            <button type="submit" class="bg-brown-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                                                Simpan
                                            </button>
                                            <a href="payment_methods.php" class="ml-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                                                Batal
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        document.getElementById('image')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                e.target.nextElementSibling.textContent = fileName;
            }
        });
    </script>
</body>
</html>
