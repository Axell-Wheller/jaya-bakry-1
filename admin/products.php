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
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $success = "Produk berhasil dihapus.";
    } else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $discount_price = !empty($_POST['discount_price']) ? $_POST['discount_price'] : null;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
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
                $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, discount_price=?, image=?, is_featured=? WHERE id=?");
                $stmt->execute([$name, $description, $price, $discount_price, $image_url, $is_featured, $_POST['id']]);
                $success = "Produk berhasil diperbarui.";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, discount_price, image, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $discount_price, $image_url, $is_featured]);
                $success = "Produk berhasil ditambahkan.";
            }
            $action = 'list'; // Redirect back to list
        }
    }
}

// Fetch ID for Edit
$edit_product = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_product = $stmt->fetch();
}

// Fetch All for List
$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Jaya Bakry</title>
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
                            <a href="products.php" class="bg-brown-800 text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Produk
                            </a>
                            <a href="orders.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pesanan
                            </a>
                            <a href="settings.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
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
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 flex justify-between items-center">
                        <h1 class="text-2xl font-semibold text-gray-900">Kelola Produk</h1>
                        <?php if($action === 'list'): ?>
                            <a href="products.php?action=add" class="bg-brown-600 hover:bg-brown-700 text-white px-4 py-2 rounded-md font-medium text-sm">Tambah Produk</a>
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
                                    <?php foreach ($products as $product): ?>
                                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 flex items-center justify-between">
                                            <div class="flex items-center">
                                                <img class="h-12 w-12 rounded-full object-cover" src="<?php echo htmlspecialchars($product['image'] ?: 'https://via.placeholder.com/100'); ?>" alt="">
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium text-brown-600"><?php echo htmlspecialchars($product['name']); ?></p>
                                                    <p class="text-sm text-gray-500">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                                </div>
                                                <?php if($product['is_featured']): ?>
                                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Featured</span>
                                                <?php endif; ?>
                                                <?php if($product['discount_price']): ?>
                                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Diskon</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                                                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="inline">
                                                    <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
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
                                    <form action="products.php" method="POST" enctype="multipart/form-data">
                                        <?php if ($edit_product): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                                            <input type="hidden" name="current_image" value="<?php echo $edit_product['image']; ?>">
                                        <?php endif; ?>

                                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                            <div class="sm:col-span-4">
                                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                                                <div class="mt-1">
                                                    <input type="text" name="name" id="name" required value="<?php echo $edit_product['name'] ?? ''; ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                                </div>
                                            </div>

                                            <div class="sm:col-span-6">
                                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                                <div class="mt-1">
                                                    <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"><?php echo $edit_product['description'] ?? ''; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                                                <div class="mt-1">
                                                    <input type="number" name="price" id="price" required value="<?php echo $edit_product['price'] ?? ''; ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                                </div>
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="discount_price" class="block text-sm font-medium text-gray-700">Harga Diskon (Rp) - Opsional</label>
                                                <div class="mt-1">
                                                    <input type="number" name="discount_price" id="discount_price" value="<?php echo $edit_product['discount_price'] ?? ''; ?>" class="shadow-sm focus:ring-brown-500 focus:border-brown-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                                </div>
                                            </div>

                                            <div class="sm:col-span-6">
                                                <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                                                <div class="mt-1 flex items-center">
                                                    <?php if (isset($edit_product['image']) && $edit_product['image']): ?>
                                                        <span class="h-12 w-12 rounded-full overflow-hidden bg-gray-100 mr-4">
                                                            <img class="h-full w-full object-cover" src="<?php echo htmlspecialchars($edit_product['image']); ?>" alt="">
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
                                                        <input id="is_featured" name="is_featured" type="checkbox" <?php echo ($edit_product['is_featured'] ?? 0) ? 'checked' : ''; ?> class="focus:ring-brown-500 h-4 w-4 text-brown-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="is_featured" class="font-medium text-gray-700">Tampilkan di Beranda (Featured)</label>
                                                        <p class="text-gray-500">Produk ini akan muncul di halaman utama.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-8">
                                            <button type="submit" class="bg-brown-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                                                Simpan Produk
                                            </button>
                                            <a href="products.php" class="ml-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
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
        // Update file input label
        document.getElementById('image')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                e.target.nextElementSibling.textContent = fileName;
            }
        });
    </script>
</body>
</html>
