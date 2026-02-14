<?php 
session_start();
require 'includes/db.php';

// Fetch Settings
$settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
while ($row = $stmt->fetch()) {
    $settings[$row['key']] = $row['value'];
}

$store_address = $settings['store_address'] ?? 'Jl. Mawar No. 123, Jakarta';
$store_email = $settings['store_email'] ?? 'info@jayabakry.com';
$store_phone = $settings['store_phone'] ?? '+62 812 3456 7890';
$whatsapp_number = $settings['whatsapp_number'] ?? '';
$instagram_username = $settings['instagram_username'] ?? '';
$facebook_username = $settings['facebook_username'] ?? '';

include 'includes/header.php'; 
?>

<main class="container mx-auto px-6 py-12">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8 md:p-12">
        <h1 class="text-3xl font-serif font-bold text-brown-900 mb-8 text-center">Hubungi Kami</h1>
        
        <div class="space-y-8">
            <div class="text-center">
                <p class="text-brown-600 mb-8">Punya pertanyaan atau ingin memesan? Hubungi kami melalui kontak di bawah ini.</p>
            </div>

            <ul class="space-y-6 text-gray-700 max-w-lg mx-auto">
                <li class="flex items-center">
                    <span class="mr-3 text-2xl">📍</span> 
                    <span><?php echo nl2br(htmlspecialchars($store_address)); ?></span>
                </li>
                <li class="flex items-center">
                    <span class="mr-3 text-2xl">📧</span> 
                    <?php if($store_email): ?>
                        <a href="mailto:<?php echo htmlspecialchars($store_email); ?>" class="hover:text-amber-600 transition-colors">
                            <?php echo htmlspecialchars($store_email); ?>
                        </a>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </li>
                <li class="flex items-center">
                    <span class="mr-3 text-2xl">📞</span> 
                    <span><?php echo htmlspecialchars($store_phone); ?></span>
                </li>
                <?php if($whatsapp_number): ?>
                    <li class="flex items-center">
                         <span class="mr-3 text-2xl">💬</span> 
                         <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>" target="_blank" class="hover:text-green-600 transition-colors font-medium">
                            Chat WhatsApp
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($instagram_username): ?>
                    <li class="flex items-center">
                        <span class="mr-3 text-2xl">📸</span> 
                        <a href="https://instagram.com/<?php echo htmlspecialchars($instagram_username); ?>" target="_blank" class="hover:text-pink-600 transition-colors font-medium">
                            @<?php echo htmlspecialchars($instagram_username); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($facebook_username): ?>
                    <li class="flex items-center">
                        <span class="mr-3 text-2xl">📘</span> 
                        <a href="https://facebook.com/<?php echo htmlspecialchars($facebook_username); ?>" target="_blank" class="hover:text-blue-600 transition-colors font-medium">
                            /<?php echo htmlspecialchars($facebook_username); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="flex items-center">
                    <span class="mr-3 text-2xl">⏰</span> 
                    <span>Buka: Senin - Minggu (07:00 - 22:00)</span>
                </li>
            </ul>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
