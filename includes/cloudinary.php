<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
// use Cloudinary\Api\Upload\UploadApi;

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
try {
    $dotenv->load();
} catch (Exception $e) {
    // .env might not exist in production or during initial setup
}

// Configure Cloudinary
if (isset($_ENV['CLOUDINARY_URL']) && !empty($_ENV['CLOUDINARY_URL'])) {
    try {
        Configuration::instance($_ENV['CLOUDINARY_URL']);
    } catch (Exception $e) {
        // Handle invalid URL silently or log it
    }
}

function uploadImage($file) {
    if (!isset($_ENV['CLOUDINARY_URL']) || empty($_ENV['CLOUDINARY_URL'])) {
        // Fallback: Local upload if Cloudinary is not configured
        $target_dir = __DIR__ . "/../assets/images/uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return "/assets/images/uploads/" . basename($file["name"]);
        }
        return null;
    }

    try {
        $upload = (new Cloudinary\Api\Upload\UploadApi())->upload($file['tmp_name'], [
            'folder' => 'jaya_bakry_products',
            'resource_type' => 'image'
        ]);
        return $upload['secure_url'];
    } catch (Exception $e) {
        // Fallback to local if Cloudinary fails
        $target_dir = __DIR__ . "/../assets/images/uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return "assets/images/uploads/" . basename($file["name"]);
        }
        return null;
    }
}
?>
