<?php
session_start();
require 'includes/db.php';
require 'includes/whatsapp.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = $_POST['mobile'];

    // Basic validation
    if (empty($mobile)) {
        $error = "Nomor Handphone harus diisi.";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$mobile]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Delete old OTPs for this user
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            
            // Insert new OTP
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token) VALUES (?, ?)");
            $stmt->execute([$user['id'], $otp]);

            // Send WhatsApp
            $wa_message = "Kode OTP untuk reset password Anda adalah: *$otp*\n\nJANGAN BERIKAN KODE INI KEPADA SIAPAPUN.";
            $wa_response = sendWhatsapp($mobile, $wa_message);
            
            $res_data = json_decode($wa_response, true);
            if ($res_data && isset($res_data['status']) && $res_data['status']) {
                $_SESSION['reset_user_id'] = $user['id'];
                header('Location: reset_password.php');
                exit;
            } else {
                $error = "Gagal mengirim OTP. Pastikan nomor WhatsApp benar dan aktif (termasuk kode negara 62).";
            }
        } else {
            $error = "Nomor Handphone tidak ditemukan.";
        }
    }
}

include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Lupa Password?</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Masukkan nomor WhatsApp Anda untuk menerima kode OTP.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form class="space-y-6" action="" method="POST">
                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">📞</span>
                        </div>
                        <input type="text" name="mobile" id="mobile" class="focus:ring-brown-500 focus:border-brown-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Contoh: 628123456789">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brown-600 hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                        Kirim Kode OTP
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Sudah ingat password?</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="login.php" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-brown-600 bg-brown-100 hover:bg-brown-200">
                        Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
