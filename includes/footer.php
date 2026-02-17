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

$store_name = $footer_settings['store_name'] ?? 'Jaya Bakery';
$store_address = $footer_settings['store_address'] ?? 'Jl. Mawar No. 123, Jakarta';
$store_phone = $footer_settings['store_phone'] ?? '+62 812 3456 7890';
$whatsapp_number = $footer_settings['whatsapp_number'] ?? '';
$store_email = $footer_settings['store_email'] ?? '';
$instagram_username = $footer_settings['instagram_username'] ?? '';
$facebook_username = $footer_settings['facebook_username'] ?? '';
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
                         <?php if($whatsapp_number): ?>
                            <li class="flex items-center space-x-2">
                                <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>" target="_blank" class="flex items-center space-x-2 hover:text-green-400 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span>WhatsApp</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($store_email): ?>
                            <li class="flex items-center space-x-2">
                                <a href="mailto:<?php echo htmlspecialchars($store_email); ?>" class="flex items-center space-x-2 hover:text-amber-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span><?php echo htmlspecialchars($store_email); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Social -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Ikuti Kami</h4>
                    <div class="flex flex-col space-y-3">
                        <?php if($facebook_username): ?>
                             <a href="https://facebook.com/<?php echo htmlspecialchars($facebook_username); ?>" target="_blank" class="flex items-center space-x-2 text-brown-300 hover:text-white transition-colors">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                                <span><?php echo htmlspecialchars($facebook_username); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if($instagram_username): ?>
                            <a href="https://instagram.com/<?php echo htmlspecialchars($instagram_username); ?>" target="_blank" class="flex items-center space-x-2 text-brown-300 hover:text-white transition-colors">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.48 2h.165zm-2.32 2H9.25C6.702 4 6.22 4.053 5.485 4.34a2.9 2.9 0 00-1.06 1.06c-.286.736-.34 1.218-.34 3.766v.834c0 2.544.054 3.027.34 3.766a2.9 2.9 0 001.06 1.06c.735.287 1.217.34 3.766.34h.834c2.544 0 3.027-.054 3.766-.34a2.9 2.9 0 001.06-1.06c.287-.735.34-1.217.34-3.766v-.834c0-2.544-.054-3.027-.34-3.766a2.9 2.9 0 00-1.06-1.06c-.736-.286-1.218-.34-3.766-.34h-.165zm3.83 2c.66 0 1.2.537 1.2 1.2 0 .66-.54 1.2-1.2 1.2-.66 0-1.2-.54-1.2-1.2 0-.663.54-1.2 1.2-1.2zm-3.83 2a3.834 3.834 0 013.829 3.83 3.834 3.834 0 01-3.83 3.83 3.834 3.834 0 01-3.829-3.83A3.834 3.834 0 0110.155 8zm0 2a1.834 1.834 0 00-1.829 1.83 1.834 1.834 0 001.83 1.83 1.834 1.834 0 001.829-1.83A1.834 1.834 0 0010.155 10z" clip-rule="evenodd" /></svg>
                                <span>@<?php echo htmlspecialchars($instagram_username); ?></span>
                            </a>
                        <?php endif; ?>
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
