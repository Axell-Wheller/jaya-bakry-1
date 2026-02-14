<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

// Fetch Featured Products
$stmt = $pdo->query("SELECT * FROM products WHERE is_featured = 1 LIMIT 6");
$featured_products = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div class="relative bg-cream overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-cream sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-cream transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>

            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-brown-900 sm:text-5xl md:text-6xl font-serif">
                        <span class="block xl:inline">Kelezatan Roti</span>
                        <span class="block text-amber-600 xl:inline">Setiap Gigitan</span>
                    </h1>
                    <p class="mt-3 text-base text-brown-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Nikmati aneka roti segar yang dibuat dengan bahan-bahan pilihan berkualitas tinggi. Dari roti manis hingga gurih, kami sajikan dengan cinta untuk Anda.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="products.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-brown-600 hover:bg-brown-700 md:py-4 md:text-lg md:px-10 transition-colors">
                                Lihat Menu
                            </a>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            <a href="about.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-brown-700 bg-brown-100 hover:bg-brown-200 md:py-4 md:text-lg md:px-10 transition-colors">
                                Tentan Kami
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80" alt="Roti Segar">
    </div>
</div>

<!-- Featured Products Section -->
<div class="bg-white">
    <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-serif font-bold text-brown-900">Produk Unggulan</h2>
            <p class="mt-4 text-brown-500">Pilihan favorit pelanggan kami bulan ini.</p>
        </div>

        <?php if (count($featured_products) > 0): ?>
            <div class="grid grid-cols-1 gap-y-10 sm:grid-cols-2 gap-x-6 lg:grid-cols-3 xl:gap-x-8">
                <?php foreach ($featured_products as $product): ?>
                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="group">
                        <div class="w-full h-64 bg-gray-200 rounded-lg overflow-hidden">
                            <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://via.placeholder.com/300'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-center object-cover group-hover:opacity-75 transition-opacity">
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-brown-900"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="flex items-center justify-between mt-1">
                            <?php if ($product['discount_price']): ?>
                                <div>
                                    <span class="text-lg font-bold text-amber-600">Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?></span>
                                    <span class="text-sm text-gray-500 line-through ml-2">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                </div>
                            <?php else: ?>
                                <p class="text-lg font-bold text-brown-900">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 bg-brown-50 rounded-lg">
                <p class="text-brown-600">Belum ada produk unggulan saat ini.</p>
                <a href="admin/products.php" class="text-amber-600 hover:text-amber-700 mt-2 inline-block font-medium">Tambah Produk di Admin</a>
            </div>
        <?php endif; ?>
        
        <div class="mt-12 text-center">
            <a href="products.php" class="inline-block bg-brown-600 border border-transparent py-3 px-8 rounded-md font-medium text-white hover:bg-brown-700 transition-colors">
                Lihat Semua Produk
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="bg-brown-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-amber-600 font-semibold tracking-wide uppercase">Kualitas Utama</h2>
            <p class="mt-2 text-3xl leading-8 font-serif font-extrabold tracking-tight text-brown-900 sm:text-4xl">
                Kenapa Memilih Jaya Bakry?
            </p>
        </div>

        <div class="mt-10">
            <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                <div class="relative">
                    <dt>
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-brown-500 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-brown-900">Bahan Premium</p>
                    </dt>
                    <dd class="mt-2 ml-16 text-base text-brown-500">
                        Kami hanya menggunakan tepung, mentega, dan bahan-bahan terbaik untuk menjamin rasa yang konsisten.
                    </dd>
                </div>

                <div class="relative">
                    <dt>
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-brown-500 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-brown-900">Selalu Segar</p>
                    </dt>
                    <dd class="mt-2 ml-16 text-base text-brown-500">
                        Roti kami dipanggang setiap pagi. Kesegaran adalah prioritas utama kami untuk kepuasan Anda.
                    </dd>
                </div>

                <div class="relative">
                    <dt>
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-brown-500 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-brown-900">Harga Terjangkau</p>
                    </dt>
                    <dd class="mt-2 ml-16 text-base text-brown-500">
                        Kualitas premium dengan harga yang bersahabat. Nikmati  kemewahan rasa tanpa merogoh kocek dalam.
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
