<?php
require 'includes/db.php';

// Fetch Settings
$stmt = $pdo->query("SELECT key, value FROM settings WHERE key IN ('fonnte_token', 'whatsapp_number')");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$token = $settings['fonnte_token'] ?? '';
$target = $settings['whatsapp_number'] ?? '';

echo "Token: $token\n";
echo "Target: $target\n";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.fonnte.com/send',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30, // Increased timeout
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL Check
  CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL Check
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
    'target' => $target,
    'message' => "Debug Message from Jaya Bakry",
    'countryCode' => '62',
  ),
  CURLOPT_HTTPHEADER => array(
    "Authorization: $token"
  ),
));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo "cURL Error: " . curl_error($curl) . "\n";
} else {
    echo "Response: " . $response . "\n";
}


?>
