<?php
session_start();
require 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    
    if ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak sesuai.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username sudah digunakan.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, address, phone) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $full_name, $address, $phone]);
                $success = "Registrasi berhasil! Silakan login.";
            } catch (PDOException $e) {
                $error = "Terjadi kesalahan registrasi.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Daftar Akun Baru</h2>
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
                    <span class="block sm:inline"><?php echo $success; ?> <a href="login.php" class="font-bold underline">Login disini</a>.</span>
                </div>
            <?php endif; ?>
            
            <form class="space-y-6" action="" method="POST">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <div class="mt-1">
                        <input id="full_name" name="full_name" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <div class="mt-1">
                        <input id="username" name="username" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm">
                    </div>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <div class="mt-1">
                        <input id="confirm_password" name="confirm_password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm">
                    </div>
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <div class="mt-1">
                        <textarea id="address" name="address" rows="3" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brown-500 focus:border-brown-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brown-600 hover:bg-brown-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brown-500">
                        Daftar
                    </button>
                </div>
            </form>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Sudah punya akun?</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="login.php" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-brown-600 bg-brown-100 hover:bg-brown-200">
                        Login Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
