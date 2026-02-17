<?php
function sendWhatsapp($target, $message) {
    global $pdo;
    
    // Fetch API Token
    $stmt = $pdo->query("SELECT value FROM settings WHERE key = 'fonnte_token'");
    $token = $stmt->fetchColumn();
    
    if (!$token) {
        return false; // Token not set, skip notification
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $target,
        'message' => $message,
        'countryCode' => '62', // Optional
      ),
      CURLOPT_HTTPHEADER => array(
        "Authorization: $token"
      ),
    ));

    $response = curl_exec($curl);
    
    // Debug logging
    file_put_contents(__DIR__ . '/../debug_wa.txt', date('Y-m-d H:i:s') . " - Target: $target - Response: $response\n", FILE_APPEND);

    // Check for errors
    if (curl_errno($curl)) {
        file_put_contents(__DIR__ . '/../debug_wa.txt', date('Y-m-d H:i:s') . " - Error: " . curl_error($curl) . "\n", FILE_APPEND);
    }
    

    return $response;
}
?>
