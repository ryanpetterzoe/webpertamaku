# 📦 Panduan Instalasi Website SMK Pertamaku di XAMPP

## Prasyarat

| Software | Versi Minimal | Link Download |
|----------|--------------|---------------|
| XAMPP | 8.0+ | https://www.apachefriends.org |
| PHP | 8.0+ | (sudah termasuk dalam XAMPP) |
| MySQL | 5.7+ / 8.0+ | (sudah termasuk dalam XAMPP) |
| Browser | Chrome / Firefox / Edge terbaru | - |

---

## Langkah 1 — Download & Install XAMPP

1. Download XAMPP dari **https://www.apachefriends.org**
2. Jalankan installer, pilih komponen minimal: **Apache**, **MySQL**, **PHP**, **phpMyAdmin**
3. Install ke direktori default: `C:\xampp\` (Windows) atau `/opt/lampp/` (Linux/Mac)

---

## Langkah 2 — Aktifkan Apache & MySQL

1. Buka **XAMPP Control Panel**
2. Klik tombol **Start** pada baris **Apache**
3. Klik tombol **Start** pada baris **MySQL**
4. Pastikan keduanya berstatus **hijau / Running**

---

## Langkah 3 — Copy File Website

### Windows:
```
Salin folder  → webpertamaku
ke            → C:\xampp\htdocs\webpertamaku\
```

### Linux/Mac:
```bash
sudo cp -r webpertamaku /opt/lampp/htdocs/
```

Struktur akhir harus seperti ini:
```
C:\xampp\htdocs\
└── webpertamaku\
    ├── app\
    ├── assets\
    ├── config\
    ├── database\
    ├── routes\
    ├── views\
    ├── .htaccess
    └── index.php
```

---

## Langkah 4 — Aktifkan mod_rewrite (Apache)

Website ini menggunakan URL cantik (tanpa `index.php?`), pastikan `mod_rewrite` aktif:

### Windows (XAMPP):
1. Buka **XAMPP Control Panel** → Klik **Config** pada baris Apache → pilih **httpd.conf**
2. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Hapus tanda `#` di depannya sehingga menjadi: `LoadModule rewrite_module modules/mod_rewrite.so`
4. Cari bagian `<Directory "C:/xampp/htdocs">` dan ubah `AllowOverride None` menjadi `AllowOverride All`
5. Simpan file, lalu **Restart Apache** dari XAMPP Control Panel

### Linux (XAMPP):
```bash
sudo /opt/lampp/bin/apachectl -M | grep rewrite
# Jika belum aktif:
sudo a2enmod rewrite
sudo service apache2 restart
```

---

## Langkah 5 — Buat Database

1. Buka browser, akses: **http://localhost/phpmyadmin**
2. Klik tab **"Databases"** (atau menu SQL)
3. Pada kolom "Create database" ketik: `websmk`
4. Pilih collation: `utf8mb4_unicode_ci`
5. Klik **Create**

---

## Langkah 6 — Import Database

1. Di phpMyAdmin, klik database **`websmk`** yang baru dibuat (di panel kiri)
2. Klik tab **"Import"**
3. Klik **"Choose File"** → pilih file: `webpertamaku/database/schema.sql`
4. Pastikan format: **SQL**
5. Klik **"Go"** / **"Import"**
6. Tunggu hingga muncul pesan sukses ✅

---

## Langkah 7 — Konfigurasi Koneksi Database

Buka file: `webpertamaku/config/database.php`

```php
define('DB_HOST', 'localhost');   // Tidak perlu diubah
define('DB_USER', 'root');        // Username MySQL XAMPP default: root
define('DB_PASS', '');            // Password MySQL XAMPP default: kosong
define('DB_NAME', 'websmk');      // Nama database yang sudah dibuat
```

> **Catatan:** Jika kamu sudah mengatur password MySQL, ubah `DB_PASS` sesuai password-mu.

---

## Langkah 8 — Konfigurasi URL Aplikasi

Buka file: `webpertamaku/config/app.php`

```php
define('APP_URL', 'http://localhost/webpertamaku');
```

> Jika nama folder berbeda, sesuaikan. Contoh jika folder bernama `smk`:
> ```php
> define('APP_URL', 'http://localhost/smk');
> ```

---

## Langkah 9 — Buat Folder Upload (Permissions)

Pastikan folder upload bisa ditulis:

### Windows:
Folder sudah bisa ditulis secara default di XAMPP. Pastikan folder ini ada:
```
webpertamaku\assets\images\uploads\
```
(sudah dibuat otomatis saat copy)

### Linux/Mac:
```bash
chmod -R 777 /opt/lampp/htdocs/webpertamaku/assets/images/uploads/
```

---

## Langkah 10 — Buka Website

Buka browser dan akses:

| Halaman | URL |
|---------|-----|
| 🏠 Halaman Utama | http://localhost/webpertamaku/ |
| 📋 SPMB / Pendaftaran | http://localhost/webpertamaku/spmb |
| 📰 Berita | http://localhost/webpertamaku/berita |
| 🔐 Admin Login | http://localhost/webpertamaku/admin/login |

---

## 🔐 Akun Admin Default

| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `password` |
| Role | Super Admin |

> ⚠️ **PENTING:** Segera ubah password setelah login pertama kali!
> Masuk ke CMS → menu **Pengguna** → Edit profil admin.

---

## 🎨 Fitur-Fitur Website

### Halaman Publik
| Halaman | URL | Keterangan |
|---------|-----|------------|
| Beranda | `/` | Hero slider, statistik, jurusan, berita, testimoni |
| Profil | `/profil` | Visi, misi, sejarah, sambutan kepala sekolah |
| Jurusan | `/jurusan` | Daftar program keahlian |
| Guru & Staff | `/guru-staff` | Data tenaga pengajar |
| Berita | `/berita` | Daftar berita + pencarian |
| Galeri | `/galeri` | Galeri foto dengan lightbox |
| Prestasi | `/prestasi` | Pencapaian/penghargaan sekolah |
| SPMB | `/spmb` | Info pendaftaran siswa baru |
| Daftar Online | `/spmb/daftar` | Formulir pendaftaran multi-step |
| Cek Status | `/spmb/cek` | Cek status pendaftaran |
| Kontak | `/kontak` | Formulir kontak |

### Panel Admin CMS
| Menu | Keterangan |
|------|------------|
| Dashboard | Statistik, data terbaru |
| Berita | Tambah/edit/hapus berita |
| Galeri | Kelola foto galeri |
| Slider | Banner halaman utama |
| Jurusan | Kelola program keahlian |
| Guru | Data tenaga pengajar |
| Staff | Data karyawan |
| Prestasi | Pencapaian sekolah |
| Testimoni | Ulasan alumni/ortu |
| Agenda | Kalender kegiatan |
| SPMB | Kelola pendaftar + status |
| Pengaturan SPMB | Buka/tutup pendaftaran |
| Kontak | Pesan masuk |
| Pengaturan | Nama sekolah, logo, visi misi, SEO, dll |

---

## 🌗 Dark / Light Theme

- Klik ikon **☀️/🌙** di navbar untuk toggle tema
- Pilihan disimpan di browser (localStorage)
- Admin dapat mengatur tema default dari **Pengaturan → Tampilan**

---

## ❓ Troubleshooting

### Error: "404 Not Found" pada semua halaman
→ mod_rewrite belum aktif. Ikuti Langkah 4 di atas.

### Error: "Access denied for user 'root'"
→ Password MySQL-mu bukan kosong. Ubah `DB_PASS` di `config/database.php`.

### Gambar tidak muncul setelah upload
→ Pastikan folder `assets/images/uploads/` ada dan bisa ditulis (writable).

### Halaman kosong / blank white
→ Aktifkan error reporting PHP. Tambahkan di awal `index.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### SPMB menampilkan "Pendaftaran belum dibuka"
→ Login admin → SPMB → Pengaturan → pastikan tanggal open/close sudah benar dan `is_active = Ya`.

---

## 📂 Struktur File

```
webpertamaku/
├── app/
│   ├── controllers/
│   │   ├── PublicController.php    # Halaman publik
│   │   ├── AuthController.php      # Login/Logout admin
│   │   ├── AdminController.php     # Dashboard admin
│   │   └── CmsController.php       # CRUD semua konten
│   └── helpers.php                 # Fungsi bantuan
├── assets/
│   ├── css/style.css               # Style utama + dark/light theme
│   ├── js/main.js                  # JavaScript (theme, slider, SPMB form, dll)
│   └── images/uploads/             # Folder upload gambar
├── config/
│   ├── app.php                     # Konfigurasi aplikasi
│   └── database.php                # Konfigurasi database
├── database/
│   └── schema.sql                  # SQL: buat tabel + data awal
├── routes/
│   └── web.php                     # Routing URL
├── views/
│   ├── layouts/                    # Header, footer, admin layout
│   ├── public/                     # Tampilan halaman publik
│   ├── admin/                      # Tampilan panel admin
│   └── auth/                       # Halaman login
├── .htaccess                       # URL rewrite rules
└── index.php                       # Entry point
```

---

## 🔄 Update / Pembaruan

Untuk update website di masa mendatang:
1. Backup database dulu melalui phpMyAdmin (Export → SQL)
2. Copy file baru ke folder yang sama
3. Jika ada perubahan database, jalankan SQL update secara manual

---

*Dibuat dengan ❤️ menggunakan PHP 8 + Bootstrap 5 + MySQL*
