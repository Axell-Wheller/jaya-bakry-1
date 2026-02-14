<?php
require __DIR__ . '/../includes/db.php';

try {
    // Create Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS payment_methods (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        code TEXT NOT NULL UNIQUE,
        description TEXT,
        image TEXT,
        type TEXT NOT NULL, -- transfer, qris, ewallet, cod
        requires_proof BOOLEAN DEFAULT 0,
        is_active BOOLEAN DEFAULT 1
    )");

    echo "Table 'payment_methods' created successfully.\n";

    // maximize id to avoid conflict if any (though typical sqlite autoincrement handles it)
    
    // Seed Data
    $methods = [
        [
            'name' => 'Transfer Bank (BCA)',
            'code' => 'transfer_bca',
            'description' => 'BCA 1234567890 a.n Jaya Bakry',
            'type' => 'transfer',
            'requires_proof' => 1
        ],
        [
            'name' => 'QRIS (Scan Barcode)',
            'code' => 'qris',
            'description' => 'Scan QRIS code provided.',
            'type' => 'qris',
            'requires_proof' => 1,
            'image' => '' // User can upload later
        ],
        [
            'name' => 'Bayar di Tempat (COD)',
            'code' => 'cod',
            'description' => 'Bayar saat pesanan sampai.',
            'type' => 'cod',
            'requires_proof' => 0
        ],
        [
            'name' => 'E-Wallet (GoPay/OVO)',
            'code' => 'ewallet',
            'description' => 'Transfer ke nomor WA Admin via GoPay/OVO.',
            'type' => 'ewallet',
            'requires_proof' => 1
        ]
    ];

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO payment_methods (name, code, description, type, requires_proof, image, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
    
    foreach ($methods as $m) {
        $stmt->execute([$m['name'], $m['code'], $m['description'], $m['type'], $m['requires_proof'], $m['image'] ?? null]);
    }

    echo "Seed data inserted successfully.\n";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
