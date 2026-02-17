<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle Search
$search = $_GET['search'] ?? '';
$where_clause = "";
$params = [];

if ($search) {
    $where_clause = "WHERE o.order_code LIKE ? OR u.username LIKE ? OR o.id LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

// Fetch Orders
$sql = "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id $where_clause ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Jaya Bakery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
    <div class="md:pl-64 flex flex-col flex-1">
        <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <h1 class="text-2xl font-semibold text-gray-900">Daftar Pesanan</h1>
                        
                        <div class="flex space-x-2 w-full sm:w-auto">
                            <form action="" method="GET" class="flex-1 sm:flex-none flex">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Kode / User..." class="rounded-l-md border-gray-300 shadow-sm focus:border-brown-500 focus:ring-brown-500 sm:text-sm p-2 border w-full sm:w-64">
                                <button type="submit" class="bg-brown-600 px-4 py-2 border border-transparent rounded-r-md shadow-sm text-sm font-medium text-white hover:bg-brown-700 focus:outline-none">
                                    Cari
                                </button>
                            </form>
                            
                            <a href="export_orders.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium text-sm flex items-center justify-center">
                                <svg class="h-4 w-4 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="hidden sm:inline">Export</span>
                            </a>
                        </div>
                    </div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul class="divide-y divide-gray-200">
                                <?php if (empty($orders)): ?>
                                    <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                                        Tidak ada pesanan ditemukan.
                                    </li>
                                <?php endif; ?>
                                <?php foreach ($orders as $order): ?>
                                    <li>
                                        <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-brown-600 truncate">
                                                        <?php echo htmlspecialchars($order['order_code'] ?? "#" . $order['id']); ?> 
                                                        <span class="text-gray-500 font-normal">- <?php echo htmlspecialchars($order['username']); ?></span>
                                                    </p>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <?php
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        if ($order['status'] === 'completed') {
                                                            $statusClass = 'bg-green-100 text-green-800';
                                                        } elseif ($order['status'] === 'pending') {
                                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                                        } elseif ($order['status'] === 'processing') {
                                                            $statusClass = 'bg-blue-100 text-blue-800';
                                                        } elseif ($order['status'] === 'cancelled') {
                                                            $statusClass = 'bg-red-100 text-red-800';
                                                        }
                                                        ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                            </svg>
                                                            <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?>
                                                        </p>
                                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                                        </p>
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
