<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['reset_user_id'])) {
    header('Location: forgot_password.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Verify OTP
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE user_id = ? AND token = ?");
        $stmt->execute([$_SESSION['reset_user_id'], $otp]);
        $reset = $stmt->fetch();

        if ($reset) {
            // Update Password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['reset_user_id']]);

            // Delete OTP
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$_SESSION['reset_user_id']]);

            // Clear session
            unset($_SESSION['reset_user_id']);

            $success = "Password berhasil diubah. Silakan login dengan password baru.";
            // Redirect after 3 seconds
            header("refresh:3;url=login.php");
        } else {
            $error = "Kode OTP salah atau kedaluwarsa.";
        }
    }
}

include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Reset Password</h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php else: ?>
                <form class="space-y-6" action="" method="POST">
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700">Kode OTP</label>
                        <div class="mt-1">
                            <input id="otp" name="otp" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm" placeholder="Masukkan 6 digit kode">
                        </div>
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <div class="mt-1">
                            <input id="new_password" name="new_password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <div class="mt-1">
                            <input id="confirm_password" name="confirm_password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brown-600 hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                            Reset Password
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
