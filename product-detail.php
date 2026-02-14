<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: products.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container mx-auto py-12 text-center'><h2 class='text-2xl text-brown-800'>Produk tidak ditemukan.</h2><a href='products.php' class='text-amber-600 hover:underline'>Kembali ke Menu</a></div>";
    include 'includes/footer.php';
    exit;
}
?>

<div class="bg-white">
    <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Image gallery -->
            <div class="flex-col-reverse">
                <div class="w-full h-96 bg-gray-200 rounded-lg overflow-hidden">
                    <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://via.placeholder.com/600'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-center object-cover shadow-lg">
                </div>
            </div>

            <!-- Product info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <h1 class="text-3xl font-serif font-extrabold tracking-tight text-brown-900"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="mt-3">
                    <h2 class="sr-only">Product information</h2>
                    <?php if ($product['discount_price']): ?>
                        <p class="text-3xl text-amber-600 font-bold">Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?> <span class="text-lg text-gray-400 line-through ml-2 font-normal">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span></p>
                    <?php else: ?>
                        <p class="text-3xl text-brown-900 font-bold">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="text-base text-gray-700 space-y-6">
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                </div>

                <form class="mt-10" action="cart.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-24">
                           <label for="quantity" class="sr-only">Quantity</label>
                           <input type="number" id="quantity" name="quantity" min="1" value="1" class="shadow-sm focus:ring-amber-500 focus:border-amber-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"> 
                        </div>

                        <button type="submit" class="w-full bg-brown-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500 transition-colors">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </form>
                
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Tersedia untuk pengiriman hari ini</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
