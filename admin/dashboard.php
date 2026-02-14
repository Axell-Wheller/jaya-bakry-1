<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Stats
$stats = [
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?: 0
];

// Recent Orders
$stmt = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jaya Bakry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Same config as header.php -->
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
                            <a href="dashboard.php" class="bg-brown-800 text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Dashboard
                            </a>
                            <a href="products.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Produk
                            </a>
                            <a href="orders.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pesanan
                            </a>
                            <a href="settings.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pengaturan
                            </a>
                            <a href="users.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pengguna
                            </a>
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex border-t border-brown-800 p-4">
                        <a href="../logout.php" class="text-brown-100 hover:text-white text-sm font-medium">Keluar</a>
                        <a href="../index.php" class="text-brown-100 hover:text-white text-sm font-medium ml-4">Lihat Web</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <h1 class="text-2xl font-semibold text-gray-900">Dashboard Overview</h1>
                    </div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Produk</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['products']; ?></dd>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['orders']; ?></dd>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pengguna</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['users']; ?></dd>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pendapatan Total</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">Rp <?php echo number_format($stats['revenue'], 0, ',', '.'); ?></dd>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <h2 class="text-lg leading-6 font-medium text-gray-900 mt-8 mb-4">Pesanan Terbaru</h2>
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul class="divide-y divide-gray-200">
                                <?php foreach ($recent_orders as $order): ?>
                                    <li>
                                        <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-brown-600 truncate">
                                                        Order #<?php echo $order['id']; ?> - <?php echo htmlspecialchars($order['username']); ?>
                                                    </p>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                        <p><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
