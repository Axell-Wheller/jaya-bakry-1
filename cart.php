<?php
session_start();
require 'includes/db.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    } elseif ($action === 'update') {
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    } elseif ($action === 'remove') {
        $product_id = $_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    } elseif ($action === 'checkout') {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php?redirect=cart.php');
            exit;
        }
        
        // Process Order
        $user_id = $_SESSION['user_id'];
        $payment_method = $_POST['payment'];
        $delivery_method = $_POST['delivery_method'];
        $shipping_address = ($delivery_method === 'delivery') ? $_POST['address'] : 'Ambil di Toko';
        $location_data = $_POST['location_data'] ?? null;
        
        $total_amount = 0;
        
        // Calculate Total
        $products_in_cart = [];
        if (!empty($_SESSION['cart'])) {
            $ids = implode(',', array_keys($_SESSION['cart']));
            $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
            $products_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        foreach ($products_in_cart as $product) {
            $qty = $_SESSION['cart'][$product['id']];
            $price = $product['discount_price'] ?: $product['price'];
            $total_amount += $price * $qty;
        }
        
        if ($total_amount > 0) {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, delivery_method, location_data, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->execute([$user_id, $total_amount, $shipping_address, $payment_method, $delivery_method, $location_data]);
                $order_id = $pdo->lastInsertId();
                
                $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                foreach ($products_in_cart as $product) {
                    $qty = $_SESSION['cart'][$product['id']];
                    $price = $product['discount_price'] ?: $product['price'];
                    $stmt_item->execute([$order_id, $product['id'], $qty, $price]);
                }
                
                $pdo->commit();
                
                // --- Send WhatsApp Notification to Admin ---
                require_once 'includes/whatsapp.php';
                
                // Get Admin WA Number
                $stmt_wa = $pdo->query("SELECT value FROM settings WHERE key = 'whatsapp_number'");
                $admin_wa = $stmt_wa->fetchColumn();
                
                if ($admin_wa) {
                    $wa_msg = "*PESANAN BARU MASUK*\n";
                    $wa_msg .= "Order ID: #$order_id\n";
                    $wa_msg .= "Total: Rp " . number_format($total_amount, 0, ',', '.') . "\n\n";
                    $wa_msg .= "*Detail Pesanan:*\n";
                    
                    $base_url = "http://" . $_SERVER['HTTP_HOST'];
                    foreach ($products_in_cart as $product) {
                        $qty = $_SESSION['cart'][$product['id']];
                        $wa_msg .= "- $qty x " . $product['name'] . "\n";
                        $wa_msg .= "  (Foto: " . $base_url . $product['image'] . " )\n";
                    }
                    
                    $wa_msg .= "\nMohon cek admin panel utk detail lengkap.";
                    
                    sendWhatsapp($admin_wa, $wa_msg);
                }
                // -------------------------------------------

                $_SESSION['cart'] = []; // Clear Cart
                header("Location: order-success.php?id=$order_id");
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Terjadi kesalahan saat memproses pesanan.";
            }
        }
    }
}

include 'includes/header.php';

// Fetch Cart Items for Display
$cart_items = [];
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $product['qty'] = $_SESSION['cart'][$product['id']];
        $cart_items[] = $product;
        $total_price += ($product['discount_price'] ?: $product['price']) * $product['qty'];
    }
}

// Fetch Active Payment Methods
$stmt_payment = $pdo->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY name ASC");
$payment_methods = $stmt_payment->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-serif font-bold text-brown-900 mb-8">Keranjang Belanja</h1>

        <?php if (empty($cart_items)): ?>
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-brown-500 text-lg">Keranjang Anda kosong.</p>
                <a href="products.php" class="text-amber-600 hover:text-amber-700 font-medium mt-4 inline-block">Mulai Belanja &rarr;</a>
            </div>
        <?php else: ?>
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
                <div class="lg:col-span-7">
                    <ul class="border-t border-b border-gray-200 divide-y divide-gray-200">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="flex py-6 sm:py-10">
                                <div class="flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($item['image'] ?: 'https://via.placeholder.com/150'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-24 h-24 rounded-md object-center object-cover sm:w-48 sm:h-48">
                                </div>

                                <div class="ml-4 flex-1 flex flex-col justify-between sm:ml-6">
                                    <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                        <div>
                                            <div class="flex justify-between">
                                                <h3 class="text-sm">
                                                    <a href="product-detail.php?id=<?php echo $item['id']; ?>" class="font-medium text-brown-700 hover:text-brown-800">
                                                        <?php echo htmlspecialchars($item['name']); ?>
                                                    </a>
                                                </h3>
                                            </div>
                                            <p class="mt-1 text-sm font-medium text-brown-900">
                                                Rp <?php echo number_format($item['discount_price'] ?: $item['price'], 0, ',', '.'); ?>
                                            </p>
                                        </div>

                                        <div class="mt-4 sm:mt-0 sm:pr-9">
                                            <form action="cart.php" method="POST" class="flex items-center">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <label for="quantity-<?php echo $item['id']; ?>" class="sr-only">Quantity</label>
                                                <input type="number" id="quantity-<?php echo $item['id']; ?>" name="quantity" value="<?php echo $item['qty']; ?>" min="1" class="max-w-[4rem] rounded-md border border-gray-300 py-1.5 text-base leading-5 font-medium text-gray-700 text-center sm:text-sm" onchange="this.form.submit()">
                                            </form>

                                            <div class="absolute top-0 right-0">
                                                <form action="cart.php" method="POST">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="-m-2 p-2 inline-flex text-gray-400 hover:text-gray-500">
                                                        <span class="sr-only">Remove</span>
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-5 mt-16 lg:mt-0">
                   <div class="bg-white rounded-lg shadow-sm px-4 py-6 sm:p-6 lg:p-8">
                        <h2 class="text-lg font-medium text-brown-900">Ringkasan Pesanan</h2>
                        <dl class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600">Subtotal</dt>
                                <dd class="text-sm font-medium text-brown-900">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></dd>
                            </div>
                            <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                                <dt class="text-base font-medium text-brown-900">Total</dt>
                                <dd class="text-base font-medium text-brown-900">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="action" value="checkout">
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pengiriman</label>
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center">
                                                <input id="delivery" name="delivery_method" type="radio" value="delivery" checked class="focus:ring-brown-500 h-4 w-4 text-brown-600 border-gray-300" onchange="toggleDelivery(true)">
                                                <label for="delivery" class="ml-2 block text-sm text-gray-700">Diantar (Delivery)</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input id="pickup" name="delivery_method" type="radio" value="pickup" class="focus:ring-brown-500 h-4 w-4 text-brown-600 border-gray-300" onchange="toggleDelivery(false)">
                                                <label for="pickup" class="ml-2 block text-sm text-gray-700">Ambil Sendiri (Pickup)</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="address-section">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                                        <textarea id="address-input" name="address" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 mb-2 p-2 border" rows="3" placeholder="Masukkan alamat lengkap..."></textarea>
                                        
                                        <button type="button" onclick="getLocation()" class="mb-4 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                                            <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Bagikan Lokasi Saya
                                        </button>
                                        <input type="hidden" name="location_data" id="location-data">
                                        <p id="location-status" class="text-xs text-gray-500 mt-1"></p>
                                    </div>
                                    
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                                    <select name="payment" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 mb-4 p-2 border">
                                        <?php if (empty($payment_methods)): ?>
                                            <option value="" disabled>Belum ada metode pembayaran tersedia</option>
                                        <?php else: ?>
                                            <?php foreach ($payment_methods as $pm): ?>
                                                <option value="<?php echo htmlspecialchars($pm['code']); ?>">
                                                    <?php echo htmlspecialchars($pm['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    
                                    <button type="submit" class="w-full bg-brown-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-brown-500">
                                        Checkout Sekarang
                                    </button>
                                </form>

                                <script>
                                    function toggleDelivery(isDelivery) {
                                        const addressSection = document.getElementById('address-section');
                                        const addressInput = document.getElementById('address-input');
                                        
                                        if (isDelivery) {
                                            addressSection.classList.remove('hidden');
                                            addressInput.required = true;
                                        } else {
                                            addressSection.classList.add('hidden');
                                            addressInput.required = false;
                                        }
                                    }

                                    function getLocation() {
                                        const status = document.getElementById('location-status');
                                        const locationInput = document.getElementById('location-data');
                                        const addressInput = document.getElementById('address-input');

                                        if (!navigator.geolocation) {
                                            status.textContent = "Geolocation tidak didukung oleh browser Anda.";
                                            return;
                                        }

                                        status.textContent = "Mencari lokasi...";

                                        navigator.geolocation.getCurrentPosition((position) => {
                                            const latitude = position.coords.latitude;
                                            const longitude = position.coords.longitude;
                                            
                                            locationInput.value = JSON.stringify({lat: latitude, lng: longitude});
                                            status.textContent = "Lokasi ditemukan!";
                                            
                                            // Optional: Reverse geocoding could go here, but for now just saving coords
                                            addressInput.value += ` (Lokasi: http://maps.google.com/maps?q=${latitude},${longitude})`;
                                            
                                        }, () => {
                                            status.textContent = "Gagal mendapatkan lokasi.";
                                        });
                                    }
                                </script>
                            <?php else: ?>
                                <a href="login.php?redirect=cart.php" class="w-full block text-center bg-brown-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-brown-700 transition-colors">
                                    Masuk untuk Checkout
                                </a>
                            <?php endif; ?>
                        </div>
                   </div> 
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
