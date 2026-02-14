<?php
require __DIR__ . '/../includes/db.php';

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Update password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "Password untuk user '$username' BERHASIL direset.\n";
    } else {
        // Create admin user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, 'admin', 'Administrator')");
        $stmt->execute([$username, $hash]);
        echo "User '$username' BERHASIL dibuat.\n";
    }
    
    echo "Username: $username\n";
    echo "Password Baru: $password\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
