# Panduan Kolaborasi Proyek Jaya Bakry

Dokumen ini menjelaskan langkah-langkah teknis agar teman Anda bisa ikut mengerjakan proyek ini di komputer mereka sendiri.

## 0. Penting: Undang Teman di GitHub

Agar teman Anda bisa mengirim (push) kode ke repository Anda, Anda harus mengundangnya dulu:

1. Buka halaman repository Anda di GitHub.
2. Klik tab **Settings** (di bagian atas).
3. Pilih menu **Collaborators** (di menu kiri).
4. Klik tombol **Add people**.
5. Masukkan username GitHub atau email teman Anda.
6. Teman Anda harus membuka emailnya dan menerima undangan tersebut (`Accept Invitation`).

Setelah diterima, barulah teman Anda bisa lanjut ke langkah di bawah ini.

---

## 1. Persiapan Awal (Untuk Teman Anda)

Teman Anda perlu melakukan langkah-langkah ini **hanya satu kali** di awal:

1. **Clone Repository**:
   Download kode proyek dari GitHub ke komputer mereka.

   ```bash
   git clone https://github.com/username-anda/jaya-bakry.git
   cd jaya-bakry
   ```

2. **Install Dependencies**:
   Jalankan perintah ini untuk mendownload semua library (mengisi folder `vendor`).

   ```bash
   composer install
   ```

   *Catatan: Teman Anda harus sudah menginstall PHP dan Composer di komputernya.*

3. **Setup Environment ([.env](file:///c:/Users/Acer/OneDrive/Desktop/Jaya%20Bakry/.env))**:
   * Copy file [.env.example](file:///c:/Users/Acer/OneDrive/Desktop/Jaya%20Bakry/.env.example) ke file baru bernama [.env](file:///c:/Users/Acer/OneDrive/Desktop/Jaya%20Bakry/.env).
   * (Jika pakai Windows CMD): `copy .env.example .env`
   * Minta teman Anda mengisi `CLOUDINARY_URL` di file [.env](file:///c:/Users/Acer/OneDrive/Desktop/Jaya%20Bakry/.env) mereka dengan akun Cloudinary mereka sendiri (atau bisa dikososngkan jika pakai upload lokal).

4. **Setup Database**:
   Jalankan script inisialisasi untuk membuat file [store.db](file:///c:/Users/Acer/OneDrive/Desktop/Jaya%20Bakry/database/store.db) kosong.

   ```bash
   php tools/init_db.php
   ```

   *Sekarang website di komputer teman Anda sudah bisa jalan, tapi datanya masih kosong.*

---

## 2. Cara Kerja Sehari-hari (Coding)

Saat masing-masing sedang mengerjakan fitur:

* **Anda**: Coding di laptop Anda -> `git add` -> `git commit` -> `git push`.
* **Teman**: `git pull` (ambil update dari Anda) -> Coding di laptopnya -> `git push`.

**Penting**: Selama fase ini, data produk yang Anda input di laptop Anda **TIDAK AKAN MUNCUL** di laptop teman, begitu juga sebaliknya. Ini normal dan aman.

---

## 3. Cara Sinkronisasi Data (H-1 Presentasi)

Jika Anda ingin semua laptop memiliki data produk yang sama persis untuk presentasi:

1. **Pilih Sumber Data Utama**: Tentukan laptop siapa yang datanya paling lengkap (misalnya laptop Anda).
2. **Copy File Database**:
   * Ambil file [database/store.db](file:///c:/Users/Acer/OneDrive/Desktop/Jaya%20Bakry/database/store.db) dari laptop Anda.
   * Kirim file tersebut ke teman Anda (via WhatsApp/Email/Flashdisk).
3. **Timpa Database Teman**:
   * Teman Anda harus menimpa (overwrite) file [database/store.db](file:///c:/Users/Acer/OneDrive/Desktop/Jaya%20Bakry/database/store.db) di laptopnya dengan file yang Anda kirim.
   * Sekarang data di laptop teman sama persis dengan laptop Anda.

**⚠️ Peringatan**: Jangan lakukan copy-paste database ini setiap saat, karena data yang baru diinput teman Anda akan hilang tertimpa data Anda. Lakukan hanya saat perlu penyamaan data saja.

---

## 4. Lampiran: Cara Install PHP & Composer (Windows)

Jika teman Anda belum punya PHP atau Composer, kirimkan panduan ini:

### Langkah 1: Install PHP (via XAMPP)

1. Download **XAMPP** dari [apachefriends.org](https://www.apachefriends.org/download.html).
2. Install XAMPP. Pastikan mencentang **PHP** dan **MySQL**.
3. Setelah selesai, PHP biasanya ada di `C:\xampp\php`.

### Langkah 2: Install Composer

1. Download **Composer-Setup.exe** dari [getcomposer.org](https://getcomposer.org/download/).
2. Jalankan file `.exe` tersebut.
3. Saat diminta memilih **PHP Command-Line**, arahkan ke `C:\xampp\php\php.exe` (biasanya otomatis terdeteksi).
4. Klik **Next** sampai selesai.

### Langkah 3: Cek Instalasi

Buka Terminal (CMD/PowerShell) baru, ketik:

```bash
composer -v
```

Jika muncul logo "Composer" besar, berarti instalasi sukses.

