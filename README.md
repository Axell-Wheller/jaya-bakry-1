# Jaya Bakery Project Collaboration Guide

Welcome to the Jaya Bakery project! This guide will help you set up your local development environment and contribute to the project.

## Prerequisites

Before you begin, ensure you have the following installed on your machine:

-   [PHP](https://www.php.net/downloads) (Version 8.0 or higher recommended)
-   [Composer](https://getcomposer.org/download/) (For managing PHP dependencies)
-   [SQLite](https://www.sqlite.org/download.html) (For the database)
-   [Git](https://git-scm.com/downloads) (For version control)

## 📦 Cara Install (Panduan Lengkap untuk Windows)

Ikuti langkah-langkah ini agar website bisa berjalan di komputer temanmu.

### 1. Persiapan Awal
Pastikan di komputer sudah terinstall:
*   **Git**: [Download Git](https://git-scm.com/downloads) (Pilih "Windows", install dengan setting default).
*   **XAMPP**: [Download XAMPP](https://www.apachefriends.org/download.html) (Untuk PHP dan Database). Pastikan install PHP versi 8.0 ke atas.
*   **Composer**: [Download Composer](https://getcomposer.org/download/) (Pilih `Composer-Setup.exe`). *Saat install, arahkan ke `php.exe` di folder XAMPP (`C:\xampp\php\php.exe`).*

---

### 2. Download Project (Clone)
1.  Buka folder dimana project mau disimpan (misal: `Documents`).
2.  Klik kanan di ruang kosong, pilih **"Open Git Bash Here"** (atau pakai Command Prompt).
3.  Ketik perintah ini dan tekan Enter:
    ```bash
    git clone https://github.com/USERNAME/REPOSITORY.git
    ```
    *(Ganti link di atas dengan link GitHub kamu yang asli)*

4.  Masuk ke folder project:
    ```bash
    cd jaya-bakery
    ```

---

### 3. Install Library Pendukung
Di terminal yang sama, ketik perintah ini agar semua fitur berjalan:
```bash
composer install
```
*Tunggu sampai proses download selesai.*

---

### 4. Setup Database & Konfigurasi
1.  **Buat File Konfigurasi**:
    *   Duplikat file `.env.example` lalu ubah namanya menjadi `.env`.
    *   (Bisa klik kanan file -> Copy -> Paste -> Rename jadi `.env`).

2.  **Siapkan Database**:
    *   Buka folder `database`.
    *   Buat file kosong baru beri nama `store.db`.
    *   Buka terminal di folder project, jalankan:
        ```bash
        sqlite3 database/store.db < database/schema.sql
        ```
    *   *Alternatif (Jika sqlite3 error)*: Gunakan aplikasi **DB Browser for SQLite**, buka `database/store.db`, pilih tab "Execute SQL", copy isi `database/schema.sql`, paste ke situ, lalu klik tombol Play/Run.

---

### 5. Jalankan Website
1.  Pastikan terminal masih terbuka di folder `jaya-bakery`.
2.  Jalankan perintah:
    ```bash
    php -S localhost:8000
    ```
3.  Buka browser (Chrome/Edge), buka link: [http://localhost:8000](http://localhost:8000)

Selesai! Website **Jaya Bakery** siap digunakan.

---


You can use the built-in PHP development server to run the application locally:

```bash
php -S localhost:8000
```

Once the server is running, open your browser and navigate to:

[http://localhost:8000](http://localhost:8000)

## Project Structure

-   `admin/` - Admin panel for managing products and orders.
-   `assets/` - Static assets like images, CSS, and JavaScript.
-   `database/` - Database schema and SQLite database file.
-   `includes/` - Reusable PHP components (header, footer, database connection).
-   `vendor/` - Composer dependencies (do not edit manually).
-   `*.php` - Main application pages (index, login, product-detail, etc.).

## Contribution Guidelines

We welcome contributions! Please follow these steps to contribute:

1.  **Fork the Repository**: Click the "Fork" button on the top right of the repository page.
2.  **Create a Branch**: Create a new branch for your feature or bug fix.
    ```bash
    git checkout -b feature/your-feature-name
    ```
3.  **Make Changes**: Implement your changes and commit them with descriptive messages.
    ```bash
    git commit -m "Add feature: description of your changes"
    ```
4.  **Push Changes**: Push your branch to your forked repository.
    ```bash
    git push origin feature/your-feature-name
    ```
5.  **Create a Pull Request**: Go to the original repository and open a Pull Request from your branch.

## License

This project is open-source and available under the [MIT License](LICENSE).
