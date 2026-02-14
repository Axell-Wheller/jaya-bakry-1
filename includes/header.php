<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jaya Bakry - Aroma Khas, Rasa Pas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#FDF6E3',
                        brown: {
                            50: '#EFEBE9',
                            100: '#D7CCC8',
                            200: '#BCAAA4',
                            300: '#A1887F',
                            400: '#8D6E63',
                            500: '#795548',
                            600: '#6D4C41',
                            700: '#5D4037', // Main Brown
                            800: '#4E342E',
                            900: '#3E2723',
                        },
                        amber: {
                            500: '#FFC107', // Gold/Accent
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Merriweather', 'serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #FDF6E3; /* Cream Background */
            color: #3E2723; /* Dark Brown Text */
        }
        /* Custom Scrollbar for a premium feel */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #FDF6E3;
        }
        ::-webkit-scrollbar-thumb {
            background: #A1887F;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #795548;
        }
    </style>
</head>
<body class="font-sans antialiased flex flex-col min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-brown-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="flex items-center space-x-2 text-2xl font-serif font-bold text-brown-700">
                        <img src="assets/images/logo.png" alt="Logo Jaya Bakry" class="h-10 w-10 mr-2 rounded-full">
                        <span>Jaya<span class="text-amber-600">Bakry</span></span>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden sm:flex sm:space-x-8">
                    <a href="index.php" class="text-brown-600 hover:text-brown-800 px-3 py-2 rounded-md text-sm font-medium transition-colors">Beranda</a>
                    <a href="products.php" class="text-brown-600 hover:text-brown-800 px-3 py-2 rounded-md text-sm font-medium transition-colors">Produk</a>
                    <a href="contact.php" class="text-brown-600 hover:text-brown-800 px-3 py-2 rounded-md text-sm font-medium transition-colors">Kontak</a>
                </div>

                <!-- Right Side (Cart & Auth) -->
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative text-brown-600 hover:text-brown-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center">
                                <?php echo count($_SESSION['cart']); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="relative group">
                            <button class="flex items-center space-x-1 text-brown-600 hover:text-brown-800">
                                <span class="text-sm font-medium">Akun</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div class="absolute right-0 w-48 mt-2 origin-top-right bg-white border border-gray-100 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform z-50">
                                <div class="py-1">
                                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <a href="admin/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-cream">Dashboard Admin</a>
                                    <?php endif; ?>
                                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-cream">Profil Saya</a>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="text-brown-600 hover:text-brown-800 font-medium text-sm">Masuk</a>
                        <a href="register.php" class="bg-brown-600 hover:bg-brown-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-transform transform hover:scale-105 shadow-md">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow">
