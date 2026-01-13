# Warehouse Management System (WMS)

Sistem Manajemen Gudang berbasis web yang dibangun dengan Laravel 12, Bootstrap 5, dan MySQL. Aplikasi ini dirancang untuk menangani operasional gudang multi-lokasi dengan fitur pelacakan stok realtime, transfer antar gudang, dan log audit yang lengkap.

## 📋 Fitur Utama

### 🏢 Multi-Warehouse (Banyak Gudang)
- ✅ Kelola banyak gudang (Pusat, Cabang, dll)
- ✅ Stok terpisah per lokasi gudang
- ✅ Filter stok berdasarkan gudang tertentu
- ✅ Total stok gabungan (Global Stock)

### 📦 Manajemen Inventori
- ✅ CRUD Barang dengan barcode otomatis
- ✅ Live Search (Pencarian Cepat dengan AJAX)
- ✅ Kategori & Satuan barang
- ✅ Lokasi rak penyimpanan
- ✅ Notifikasi stok menipis

### 🚛 Transaksi Stok
- ✅ **Stok Masuk & Keluar**: Pencatatan barang masuk/keluar dengan validasi stok.
- ✅ **Transfer Stok**: Pemindahan barang antar gudang dengan mutasi otomatis.
- ✅ **Approval System**: Tanda tangan digital petugas & penerima.
- ✅ **Bukti Transaksi**: Cetak Bukti Serah Terima & Surat Jalan (PDF).

### 🛡️ Keamanan & Audit
- ✅ **Role-based Access**: Admin, Staff Gudang, Owner.
- ✅ **Audit Logs**: Mencatat setiap aktivitas user (siapa, kapan, melakukan apa).
- ✅ **Login Security**: Proteksi rute berdasarkan role.

### 📱 Barcode & Scanner
- ✅ Generate barcode otomatis (Code128)
- ✅ Scanner Multi-Item dengan kamera HP / Scanner Gun
- ✅ Mode Switch (Masuk/Keluar) interaktif

### ⚙️ Pengaturan & Profil
- ✅ **Profil Perusahaan**: Ganti nama, logo, dan alamat perusahaan.
- ✅ **Branding**: Logo tampil di Login Page & Dokumen PDF.

---

## 🛠️ Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL >= 5.7
- Node.js >= 18
- XAMPP/WAMP/Laragon (Local Development)

---

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/azizt91/sistem-multi-gudang.git
cd sistem-multi-gudang
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrasi & Seeder
```bash
php artisan migrate:fresh --seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: `http://127.0.0.1:8000`

---

## 👤 Akun Demo

| Role  | Email                   | Password | Akses Utama |
|-------|-------------------------|----------|-------------|
| **Admin** | admin@warehouse.test    | password | Full Akses Konfigurasi, User, Hapus Data |
| **Staff** | staff@warehouse.test    | password | Operasional Masuk/Keluar/Transfer Stok |
| **Owner** | owner@warehouse.test    | password | Monitoring Dashboard & Laporan (Read Only) |

---

## 📂 Struktur Folder

```
warehouse-management-system/
├── app/
│   ├── Http/Controllers/    
│   │   ├── StockHeaderController.php   # Logika Transaksi Stok
│   │   ├── StockTransferController.php # Logika Transfer Gudang
│   │   ├── AuditLogController.php      # Riwayat Aktivitas
│   │   ├── ItemController.php          # Manajemen Barang (Live Search)
│   │   └── ...
│   ├── Models/              # Eloquent Models (Warehouse, Item, StockHeader...)
│   └── Services/            # Business Logic (StockService, AuditService)
├── database/
│   ├── migrations/          # Struktur Database
│   └── seeders/             # Data Dummy (Include Multi-Warehouse distribution)
├── resources/views/
│   ├── auth/                # Login Page (Custom Branding)
│   ├── dashboard/           # Dashboard Utama
│   ├── items/               # Tampilan Daftar Barang
│   ├── stock-headers/       # Riwayat Transaksi
│   ├── stock-transfers/     # Tampilan Transfer Stok
│   ├── audit-logs/          # Tampilan Audit Trail
│   ├── manual/              # Buku Panduan User
│   └── warehouse/           # Manajemen Data Gudang
└── routes/
    └── web.php              # Definisi Route & Hak Akses
```

---

## 🔐 Hak Akses Role (Permission Matrix)

| Fitur / Modul        | Admin | Staff | Owner |
|----------------------|:-----:|:-----:|:-----:|
| **Dashboard**        | ✅    | ✅    | ✅    |
| **Manajemen User**   | ✅    | ❌    | ❌    |
| **Manajemen Gudang** | ✅    | ❌    | ❌    |
| **Barang (Data Master)** | ✅ | Lihat | Lihat |
| **Stok Masuk/Keluar**| ✅    | ✅    | ❌    |
| **Transfer Stok**    | ✅    | ✅    | ❌    |
| **Hapus Transaksi**  | ✅    | ❌    | ❌    |
| **Audit Logs**       | ✅    | ❌    | ✅    |
| **Laporan**          | ✅    | ❌    | ✅    |
| **Profil Perusahaan**| ✅    | ❌    | ❌    |

*Catatan: Staff gudang hanya fokus pada operasional (input barang, transfer), sedangkan Owner hanya fokus pada monitoring angka dan audit.*

---

## 📱 Akses Mobile

Aplikasi ini responsif dan bisa diakses dari Smartphone/Tablet untuk keperluan scanning barcode di gudang.

1. Pastikan HP dan Laptop/Server ada di **WiFi yang sama**.
2. Jalankan: `php artisan serve --host=0.0.0.0`
3. Cek IP Laptop: `ipconfig` (Windows)
4. Buka Browser HP: `http://192.168.x.x:8000`

---

## 👨‍💻 Pengembang

Sistem ini dikembangkan khusus untuk memudahkan pencatatan stok yang akurat dan transparan.
Menggunakan **Laravel 12** untuk performa tinggi dan **Bootstrap 5** untuk antarmuka yang bersih.
