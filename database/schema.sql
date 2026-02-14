-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'user', -- 'admin' or 'user'
    full_name TEXT,
    address TEXT,
    phone TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    image TEXT, -- Cloudinary URL
    category TEXT,
    is_featured BOOLEAN DEFAULT 0,
    discount_price REAL, -- If not null, this is the sale price
    stock INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    total_amount REAL NOT NULL,
    status TEXT DEFAULT 'pending', -- pending, processing, completed, cancelled
    shipping_address TEXT,
    payment_method TEXT,
    delivery_method TEXT,
    location_data TEXT,
    proof_of_payment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price REAL NOT NULL, -- Price at time of purchase
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Settings Table (for Admin configuration)
CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
);

-- Insert Default Admin (password: admin123)
-- Hash is generated via password_hash('admin123', PASSWORD_DEFAULT)
INSERT OR IGNORE INTO users (username, password, role, full_name) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator');

-- Insert Sample Settings
INSERT OR IGNORE INTO settings (key, value) VALUES ('store_name', 'Jaya Bakry');
INSERT OR IGNORE INTO settings (key, value) VALUES ('store_address', 'Jl. Mawar No. 123');
