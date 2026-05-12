# 🏫 Website SMK Pertamaku

Website sekolah menengah kejuruan (SMK) lengkap dengan CMS, SPMB online, dan tampilan modern responsif.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-Compatible-FB7A24?logo=xampp&logoColor=white)

---

## ✨ Fitur Utama

### 🌐 Website Publik
- **Beranda** — Hero slider, statistik sekolah, jurusan, berita terbaru, testimoni alumni, agenda
- **Profil Sekolah** — Visi misi, sejarah, sambutan kepala sekolah
- **Jurusan** — Detail 4 program keahlian (TKJ, RPL, TAV, AKL)
- **Guru & Staff** — Profil tenaga pengajar dan karyawan
- **Berita** — Artikel dengan kategori, pencarian, dan pagination
- **Galeri** — Grid foto dengan lightbox dan filter kategori
- **Prestasi** — Pencapaian sekolah dengan filter tingkat
- **Kontak** — Formulir pesan + Google Maps + info kontak
- **🌗 Dark/Light Theme** — Toggle tema, tersimpan di browser

### 📝 SPMB (Penerimaan Siswa Baru)
- Form pendaftaran **6 langkah** (multi-step wizard)
- Upload dokumen (foto, KK, akta, ijazah, raport)
- Nomor pendaftaran otomatis format `REG-2025-XXXX`
- **Cek status** pendaftaran online
- Buka/tutup pendaftaran dari admin

### 🔧 CMS Admin Panel
- **Dashboard** dengan statistik real-time
- Kelola: Berita, Galeri, Slider, Jurusan, Guru, Staff
- Kelola: Prestasi, Testimoni, Agenda
- **SPMB**: Lihat semua pendaftar, update status, export CSV
- **Pengaturan lengkap**: nama sekolah, logo, visi misi, SEO, media sosial
- Semua konten bisa diubah tanpa sentuh kode!

---

## 🚀 Instalasi Cepat

```bash
# 1. Salin ke htdocs XAMPP
cp -r webpertamaku C:\xampp\htdocs\

# 2. Import database
# Buka phpMyAdmin → buat DB 'websmk' → import database/schema.sql

# 3. Buka browser
# http://localhost/webpertamaku/
```

**👉 Panduan lengkap ada di [INSTALL.md](INSTALL.md)**

---

## 🔐 Login Admin

- URL: `http://localhost/webpertamaku/admin/login`
- Username: `admin`
- Password: `password`

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.0+ (Pure, MVC pattern) |
| Frontend | Bootstrap 5, Font Awesome 6 |
| Database | MySQL 5.7+ / 8.0+ |
| Server | Apache (XAMPP) |
| No dependencies | Tidak perlu Composer/NPM |

---

## 📸 Halaman-halaman

| URL | Deskripsi |
|-----|-----------|
| `/` | Beranda |
| `/profil` | Profil Sekolah |
| `/jurusan` | Program Keahlian |
| `/guru-staff` | Guru & Staff |
| `/berita` | Berita & Artikel |
| `/galeri` | Galeri Foto |
| `/prestasi` | Prestasi |
| `/spmb` | Info SPMB |
| `/spmb/daftar` | Form Pendaftaran |
| `/spmb/cek` | Cek Status |
| `/kontak` | Kontak |
| `/admin` | Panel Admin |

---

*Made with ❤️ — PHP 8 + Bootstrap 5 + MySQL*
