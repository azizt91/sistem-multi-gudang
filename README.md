# Warehouse Management System (WMS)

Sistem Manajemen Gudang berbasis web yang dibangun dengan Laravel 11, Bootstrap 5, dan MySQL.

## 📋 Fitur Utama

### Manajemen Inventori
- ✅ CRUD Barang dengan barcode otomatis
- ✅ Kategori & Satuan barang
- ✅ Lokasi rak penyimpanan
- ✅ Notifikasi stok menipis

### Transaksi Stok
- ✅ Sistem Dokumen (Bukti Transaksi)
- ✅ Stok Masuk & Keluar Multi-Item
- ✅ Tanda Tangan Digital pada Bukti
- ✅ Validasi stok realtime

### Barcode & Scanner
- ✅ Generate barcode otomatis (Code128)
- ✅ Scanner Multi-Item dengan sistem Cart
- ✅ Mode Switch (Masuk/Keluar) interaktif
- ✅ Cetak barcode massal

### Laporan
- ✅ Laporan harian & bulanan
- ✅ Laporan stok keseluruhan
- ✅ Export ke PDF & Excel

### Keamanan
- ✅ Role-based access (Admin, Staff, Owner)
- ✅ Login authentication

### UI/UX
- ✅ Dark Mode
- ✅ Responsive design (mobile-friendly)
- ✅ Modern dashboard

---

## 🛠️ Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL >= 5.7
- Node.js >= 18
- XAMPP/WAMP/Laragon (untuk local development)

---

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/username/warehouse-management-system.git
cd warehouse-management-system
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
php artisan migrate --seed
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

| Role  | Email                   | Password |
|-------|-------------------------|----------|
| Admin | admin@warehouse.test    | password |
| Staff | staff@warehouse.test    | password |
| Owner | owner@warehouse.test    | password |

---

## 📂 Struktur Folder

```
warehouse-management-system/
├── app/
│   ├── Http/Controllers/    # Controllers
│   ├── Models/              # Eloquent Models
│   ├── Services/            # Business Logic
│   └── Middleware/          # Custom Middleware
├── database/
│   ├── migrations/          # Database Migrations
│   └── seeders/             # Data Seeders
├── resources/views/
│   ├── layouts/             # Layout Templates
│   ├── auth/                # Authentication Views
│   ├── dashboard/           # Dashboard Views
│   ├── items/               # Item Management Views
│   ├── categories/          # Category Views
│   ├── units/               # Unit Views
│   ├── transactions/        # Transaction Views
│   ├── reports/             # Report Views
│   ├── users/               # User Management Views
│   └── barcode/             # Barcode Scanner Views
├── routes/
│   └── web.php              # Route Definitions
└── public/
    └── js/                  # JavaScript Files
```

---

## 🔐 Hak Akses Role

| Fitur              | Admin | Staff | Owner |
|--------------------|-------|-------|-------|
| Dashboard          | ✅    | ✅    | ✅    |
| Lihat Barang       | ✅    | ✅    | ✅    |
| Tambah/Edit Barang | ✅    | ❌    | ❌    |
| Hapus Barang       | ✅    | ❌    | ❌    |
| Kategori & Satuan  | ✅    | ❌    | ❌    |
| Stok Masuk/Keluar  | ✅    | ✅    | ❌    |
| Lihat Transaksi    | ✅    | ✅    | ✅    |
| Laporan            | ✅    | ❌    | ✅    |
| Kelola User        | ✅    | ❌    | ❌    |
| Scan Barcode       | ✅    | ✅    | ✅    |

---

## 📱 Akses dari HP (Mobile)

Untuk mengakses dari HP di jaringan yang sama:

1. Jalankan server dengan:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
2. Cari IP komputer:
   ```bash
   ipconfig  # Windows
   ifconfig  # Mac/Linux
   ```
3. Akses dari HP: `http://[IP_KOMPUTER]:8000`

**Catatan untuk Kamera Scanner:**
- Kamera membutuhkan HTTPS atau localhost
- Untuk HTTP di jaringan lokal, gunakan Chrome flags:
  `chrome://flags/#unsafely-treat-insecure-origin-as-secure`

---

## 🧑‍💻 Teknologi

- **Backend:** Laravel 11
- **Frontend:** Bootstrap 5, Blade Templates
- **Database:** MySQL
- **Icons:** Bootstrap Icons
- **Fonts:** Inter (Google Fonts)
- **Barcode:** picqer/php-barcode-generator, html5-qrcode
- **Export:** barryvdh/laravel-dompdf, maatwebsite/excel

---

## 📄 Lisensi

MIT License - Silakan gunakan untuk keperluan pembelajaran atau komersial.

---

## 👨‍💻 Pengembang

Dikembangkan dengan ❤️ menggunakan Laravel 11.
