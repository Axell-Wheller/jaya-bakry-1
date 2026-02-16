<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

// Fetch All Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<div class="bg-cream min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-serif font-bold text-brown-900 sm:text-4xl">Menu Kami</h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-brown-500 sm:mt-4">
                Pilih roti favorit Anda dari produk terbaik kami.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
            <?php foreach ($products as $product): ?>
                <div class="group relative bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                    <div class="h-64 w-full bg-gray-200 group-hover:opacity-75 transition-opacity">
                        <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://via.placeholder.com/300'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-center object-cover">
                    </div>
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-brown-900">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-brown-500"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <?php if ($product['discount_price']): ?>
                                <div class="flex flex-col">
                                    <span class="text-lg font-bold text-amber-600">Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?></span>
                                    <span class="text-xs text-gray-500 line-through">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                </div>
                            <?php else: ?>
                                <p class="text-lg font-bold text-brown-900">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <?php endif; ?>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="relative z-10 text-amber-600 hover:text-amber-700 font-medium text-sm">Lihat Detail &rarr;</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (count($products) === 0): ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-brown-500 text-lg">Menu sedang disiapkan. Silakan kembali lagi nanti!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
