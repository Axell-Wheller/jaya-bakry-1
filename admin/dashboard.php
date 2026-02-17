<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// 1. Basic Stats
$stats = [
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?: 0
];

// 2. Monthly Revenue Stats
$current_month = date('Y-m');
$last_month = date('Y-m', strtotime('-1 month'));

$revenue_this_month = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE status = 'completed' AND strftime('%Y-%m', created_at) = ?");
$revenue_this_month->execute([$current_month]);
$revenue_this_month = $revenue_this_month->fetchColumn() ?: 0;

$revenue_last_month = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE status = 'completed' AND strftime('%Y-%m', created_at) = ?");
$revenue_last_month->execute([$last_month]);
$revenue_last_month = $revenue_last_month->fetchColumn() ?: 0;

// Percentage change
$percent_change = 0;
if ($revenue_last_month > 0) {
    $percent_change = (($revenue_this_month - $revenue_last_month) / $revenue_last_month) * 100;
} elseif ($revenue_this_month > 0) {
    $percent_change = 100; // From 0 to something is 100% increase (technically infinite, but 100 represents 'new')
}

// 3. Top Selling Products
$top_products_stmt = $pdo->query("
    SELECT p.name, p.image, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as total_revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'completed'
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 5
");
$top_products = $top_products_stmt->fetchAll();

// 4. Chart Data (Last 6 Months)
$chart_labels = [];
$chart_data = [];

for ($i = 5; $i >= 0; $i--) {
    $month_label = date('M Y', strtotime("-$i months"));
    $month_query = date('Y-m', strtotime("-$i months"));
    
    $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE status = 'completed' AND strftime('%Y-%m', created_at) = ?");
    $stmt->execute([$month_query]);
    $month_revenue = $stmt->fetchColumn() ?: 0;
    
    $chart_labels[] = $month_label;
    $chart_data[] = $month_revenue;
}

// 5. Recent Orders
$stmt = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jaya Bakery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="md:pl-64 flex flex-col flex-1">
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <h1 class="text-2xl font-semibold text-gray-900">Dashboard Overview</h1>
                    </div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
                        
                        <!-- Stats Grid Row 1: General -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
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

                        <!-- Stats Grid Row 2: Monthly & Chart -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                            <!-- Monthly Stats -->
                            <div class="lg:col-span-1 space-y-5">
                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="px-4 py-5 sm:p-6">
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pendapatan Bulan Ini</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-brown-600">Rp <?php echo number_format($revenue_this_month, 0, ',', '.'); ?></dd>
                                        <div class="mt-2 flex items-center text-sm">
                                            <?php if ($percent_change >= 0): ?>
                                                <span class="text-green-600 font-medium flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                                    <?php echo number_format($percent_change, 1); ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="text-red-600 font-medium flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                                                    <?php echo number_format(abs($percent_change), 1); ?>%
                                                </span>
                                            <?php endif; ?>
                                            <span class="text-gray-500 ml-2">dari bulan lalu</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="px-4 py-5 sm:p-6">
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pendapatan Bulan Lalu</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-gray-600">Rp <?php echo number_format($revenue_last_month, 0, ',', '.'); ?></dd>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sales Chart -->
                            <div class="lg:col-span-2 bg-white overflow-hidden shadow rounded-lg p-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Grafik Penjualan (6 Bulan Terakhir)</h3>
                                <div class="relative h-64 w-full">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Recent Orders -->
                            <div>
                                <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pesanan Terbaru</h2>
                                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                    <ul class="divide-y divide-gray-200">
                                        <?php if (count($recent_orders) > 0): ?>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <li>
                                                    <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="block hover:bg-gray-50">
                                                        <div class="px-4 py-4 sm:px-6">
                                                            <div class="flex items-center justify-between">
                                                                <p class="text-sm font-medium text-brown-600 truncate">
                                                                    <?php echo htmlspecialchars($order['order_code'] ?? "#" . $order['id']); ?> - <?php echo htmlspecialchars($order['username']); ?>
                                                                </p>
                                                                <div class="ml-2 flex-shrink-0 flex">
                                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                        <?php 
                                                                            echo match($order['status']) {
                                                                                'completed' => 'bg-green-100 text-green-800',
                                                                                'cancelled' => 'bg-red-100 text-red-800',
                                                                                'processing' => 'bg-blue-100 text-blue-800',
                                                                                default => 'bg-yellow-100 text-yellow-800'
                                                                            };
                                                                        ?>">
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
                                        <?php else: ?>
                                            <li class="px-4 py-4 sm:px-6 text-center text-gray-500">Belum ada pesanan.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Top Selling Products -->
                            <div>
                                <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Produk Terlaris</h2>
                                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                    <ul class="divide-y divide-gray-200">
                                        <?php if (count($top_products) > 0): ?>
                                            <?php foreach ($top_products as $product): ?>
                                                <li class="px-4 py-4 sm:px-6">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex-shrink-0">
                                                            <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($product['image'] ?: 'https://via.placeholder.com/100'); ?>" alt="">
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                                <?php echo htmlspecialchars($product['name']); ?>
                                                            </p>
                                                            <p class="text-sm text-gray-500">
                                                                Terjual: <span class="font-bold text-gray-700"><?php echo $product['total_sold']; ?></span>
                                                            </p>
                                                        </div>
                                                        <div class="inline-flex items-center text-sm font-semibold text-brown-600">
                                                            Rp <?php echo number_format($product['total_revenue'], 0, ',', '.'); ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="px-4 py-4 sm:px-6 text-center text-gray-500">Belum ada data penjualan.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Pendapatan',
                    data: <?php echo json_encode($chart_data); ?>,
                    backgroundColor: 'rgba(121, 85, 72, 0.6)', // Brown-500 equivalent with opacity
                    borderColor: 'rgba(121, 85, 72, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
