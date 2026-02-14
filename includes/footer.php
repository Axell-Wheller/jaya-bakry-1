<?php
// Fetch Settings if not already fetched
if (!isset($pdo)) {
    require_once 'includes/db.php';
}

$footer_settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM settings");
    while ($row = $stmt->fetch()) {
        $footer_settings[$row['key']] = $row['value'];
    }
} catch (Exception $e) {
    // Ignore error if table doesn't exist yet
}

$store_name = $footer_settings['store_name'] ?? 'Jaya Bakry';
$store_address = $footer_settings['store_address'] ?? 'Jl. Mawar No. 123, Jakarta';
$store_phone = $footer_settings['store_phone'] ?? '+62 812 3456 7890';
?>

    </main>
    
    <footer class="bg-brown-900 text-brown-100 border-t border-brown-800 mt-auto">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="space-y-4">
                    <h3 class="text-2xl font-serif font-bold text-white"><?php echo htmlspecialchars($store_name); ?></h3>
                    <p class="text-brown-200 text-sm">Menghadirkan kehangatan dan kelezatan roti segar setiap hari untuk keluarga Anda.</p>
                </div>
                
                <!-- Links -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="index.php" class="hover:text-amber-400 transition-colors">Beranda</a></li>
                        <li><a href="products.php" class="hover:text-amber-400 transition-colors">Produk Kami</a></li>
                        <li><a href="about.php" class="hover:text-amber-400 transition-colors">Tentang Kami</a></li>
                        <li><a href="contact.php" class="hover:text-amber-400 transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm text-brown-200">
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span><?php echo htmlspecialchars($store_address); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span><?php echo htmlspecialchars($store_phone); ?></span>
                        </li>
                    </ul>
                </div>
                
                <!-- Social -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-brown-300 hover:text-white transition-colors">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.48 2h.165zm-2.32 2H9.25C6.702 4 6.22 4.053 5.485 4.34a2.9 2.9 0 00-1.06 1.06c-.286.736-.34 1.218-.34 3.766v.834c0 2.544.054 3.027.34 3.766a2.9 2.9 0 001.06 1.06c.735.287 1.217.34 3.766.34h.834c2.544 0 3.027-.054 3.766-.34a2.9 2.9 0 001.06-1.06c.287-.735.34-1.217.34-3.766v-.834c0-2.544-.054-3.027-.34-3.766a2.9 2.9 0 00-1.06-1.06c-.736-.286-1.218-.34-3.766-.34h-.165zm3.83 2c.66 0 1.2.537 1.2 1.2 0 .66-.54 1.2-1.2 1.2-.66 0-1.2-.54-1.2-1.2 0-.663.54-1.2 1.2-1.2zm-3.83 2a3.834 3.834 0 013.829 3.83 3.834 3.834 0 01-3.83 3.83 3.834 3.834 0 01-3.829-3.83A3.834 3.834 0 0110.155 8zm0 2a1.834 1.834 0 00-1.829 1.83 1.834 1.834 0 001.83 1.83 1.834 1.834 0 001.829-1.83A1.834 1.834 0 0010.155 10z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-brown-800 text-center text-brown-400 text-sm">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($store_name); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
