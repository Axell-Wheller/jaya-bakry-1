<?php
// Get current page filename for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Count pending orders for badge
if (!isset($pdo)) {
    require_once '../includes/db.php';
}
try {
    $pending_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
} catch (Exception $e) {
    $pending_count = 0;
}
?>
<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-brown-900 border-r border-brown-800">
    <!-- Sidebar component -->
    <div class="flex-1 flex flex-col min-h-0">
        <div class="flex items-center h-16 flex-shrink-0 px-4 bg-brown-800">
            <h1 class="text-xl font-serif font-bold text-white tracking-wider">Jaya <span class="text-amber-500">Bakery</span></h1>
        </div>
        <div class="flex-1 flex flex-col overflow-y-auto">
            <nav class="flex-1 px-2 py-4 space-y-1">
                <a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'bg-brown-800 text-white' : 'text-brown-100 hover:bg-brown-800 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <svg class="mr-3 flex-shrink-0 h-6 w-6 <?php echo $current_page === 'dashboard.php' ? 'text-amber-500' : 'text-brown-300 group-hover:text-amber-500'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="products.php" class="<?php echo $current_page === 'products.php' ? 'bg-brown-800 text-white' : 'text-brown-100 hover:bg-brown-800 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <svg class="mr-3 flex-shrink-0 h-6 w-6 <?php echo $current_page === 'products.php' ? 'text-amber-500' : 'text-brown-300 group-hover:text-amber-500'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Produk
                </a>

                <a href="orders.php" class="<?php echo $current_page === 'orders.php' ? 'bg-brown-800 text-white' : 'text-brown-100 hover:bg-brown-800 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md justify-between">
                    <div class="flex items-center">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 <?php echo $current_page === 'orders.php' ? 'text-amber-500' : 'text-brown-300 group-hover:text-amber-500'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Pesanan
                    </div>
                    <?php if ($pending_count > 0): ?>
                        <span class="bg-red-500 text-white ml-auto py-0.5 px-2 rounded-full text-xs font-bold shadow-sm">
                            <?php echo $pending_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <a href="payment_methods.php" class="<?php echo $current_page === 'payment_methods.php' ? 'bg-brown-800 text-white' : 'text-brown-100 hover:bg-brown-800 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <svg class="mr-3 flex-shrink-0 h-6 w-6 <?php echo $current_page === 'payment_methods.php' ? 'text-amber-500' : 'text-brown-300 group-hover:text-amber-500'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Metode Pembayaran
                </a>

                <a href="users.php" class="<?php echo $current_page === 'users.php' ? 'bg-brown-800 text-white' : 'text-brown-100 hover:bg-brown-800 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                     <svg class="mr-3 flex-shrink-0 h-6 w-6 <?php echo $current_page === 'users.php' ? 'text-amber-500' : 'text-brown-300 group-hover:text-amber-500'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Pengguna
                </a>

                <a href="settings.php" class="<?php echo $current_page === 'settings.php' ? 'bg-brown-800 text-white' : 'text-brown-100 hover:bg-brown-800 hover:text-white'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <svg class="mr-3 flex-shrink-0 h-6 w-6 <?php echo $current_page === 'settings.php' ? 'text-amber-500' : 'text-brown-300 group-hover:text-amber-500'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </a>
            </nav>
        </div>
        <div class="flex-shrink-0 flex bg-brown-900 p-4 border-t border-brown-800">
            <a href="../logout.php" class="flex-shrink-0 w-full group block">
                <div class="flex items-center">
                    <div class="ml-3">
                        <p class="text-xs font-medium text-brown-300 group-hover:text-white">Sign out</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
