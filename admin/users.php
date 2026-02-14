<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Fetch Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin Jaya Bakry</title>
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
                            700: '#5D4037', 
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
                            <a href="settings.php" class="text-brown-100 hover:bg-brown-800 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pengaturan
                            </a>
                            <a href="users.php" class="bg-brown-800 text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Pengguna
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
                        <h1 class="text-2xl font-semibold text-gray-900">Daftar Pengguna</h1>
                    </div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-8">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <?php foreach ($users as $user): ?>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['username']); ?></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                                                <?php echo ucfirst($user['role']); ?>
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
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
