@extends('layouts.app')

@section('title', 'Panduan Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card position-sticky" style="top: 80px;">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Daftar Isi
                </div>
                <div class="card-body p-0">
                    <nav class="nav flex-column">
                        <a class="nav-link" href="#getting-started">🚀 Memulai</a>
                        <a class="nav-link" href="#dashboard">📊 Dashboard</a>
                        <a class="nav-link" href="#profile">🏢 Profil Perusahaan <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.6em;">New</span></a>
                        <a class="nav-link" href="#gudang">🏭 Manajemen Gudang <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.6em;">New</span></a>
                        <a class="nav-link" href="#barang">📦 Manajemen Barang</a>
                        <a class="nav-link" href="#transaksi">🔄 Transaksi Stok</a>
                        <a class="nav-link" href="#stock-transfer">🚚 Transfer Stok <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.6em;">New</span></a>
                        <a class="nav-link" href="#tanda-terima">📝 Tanda Terima</a>
                        <a class="nav-link" href="#barcode">📷 Scan Barcode</a>
                        <a class="nav-link ms-3 small" href="#scanner-usb">↳ Scanner USB</a>
                        <a class="nav-link" href="#laporan">📈 Laporan</a>
                        <a class="nav-link" href="#audit">🛡️ Audit Logs <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.6em;">New</span></a>
                        <a class="nav-link" href="#user">👥 Manajemen User</a>
                        <a class="nav-link" href="#dark-mode">🌙 Dark Mode</a>
                        <a class="nav-link" href="#faq">❓ FAQ</a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body text-center py-4">
                    <h2 class="fw-bold mb-2"><i class="bi bi-box-seam me-2"></i>Panduan Pengguna WMS</h2>
                    <p class="text-muted mb-0">Warehouse Management System - Versi 1.0</p>
                </div>
            </div>

            <!-- Getting Started -->
            <div class="card mb-4" id="getting-started">
                <div class="card-header">
                    <h5 class="mb-0">🚀 Memulai</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold">Login ke Sistem</h6>
                    <p>Untuk menggunakan sistem, Anda perlu login terlebih dahulu dengan akun yang sudah didaftarkan oleh Administrator.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/login-page.png') }}" alt="Halaman Login" class="img-fluid rounded shadow" style="max-width: 500px;">
                        <p class="text-muted small mt-2">Gambar 1: Halaman Login</p>
                    </div>

                    <ol>
                        <li>Buka aplikasi WMS di browser</li>
                        <li>Masukkan <strong>Email</strong> dan <strong>Password</strong></li>
                        <li>Klik tombol <strong>Login</strong></li>
                    </ol>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Tip:</strong> Centang "Ingat saya" agar tidak perlu login berulang kali.
                    </div>
                </div>
            </div>

            <!-- Dashboard -->
            <div class="card mb-4" id="dashboard">
                <div class="card-header">
                    <h5 class="mb-0">📊 Dashboard</h5>
                </div>
                <div class="card-body">
                    <p>Dashboard adalah halaman utama yang menyajikan ringkasan visual aktivitas gudang secara realtime.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/dashboard.png') }}" alt="Dashboard" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar 2: Tampilan Dashboard WMS</p>
                    </div>

                    <h6 class="fw-semibold">1. Filter Gudang (Multi-Warehouse)</h6>
                    <p>Di pojok kanan atas, terdapat menu pilihan gudang:</p>
                    <ul>
                        <li><strong>Admin & Owner:</strong> Dapat memilih untuk melihat data dari semua gudang (Gabungan) atau memilih gudang spesifik cabang tertentu.</li>
                        <li><strong>Staff:</strong> Hanya akan melihat data dan stok dari gudang tempat mereka ditugaskan.</li>
                    </ul>

                    <h6 class="fw-semibold mt-4">2. Kartu Statistik Utama</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-box-seam text-primary me-2"></i>Total Barang</span>
                                    <span class="badge bg-primary rounded-pill">Jumlah Item Terdaftar</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-box-arrow-in-down text-success me-2"></i>Stok Masuk Hari Ini</span>
                                    <span class="badge bg-success rounded-pill">Total Qty Masuk</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-box-arrow-up text-danger me-2"></i>Stok Keluar Hari Ini</span>
                                    <span class="badge bg-danger rounded-pill">Total Qty Keluar</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-exclamation-triangle text-warning me-2"></i>Stok Menipis</span>
                                    <span class="badge bg-warning text-dark rounded-pill">Perlu Restock</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <h6 class="fw-semibold mt-4">3. Grafik & Ringkasan Bulanan</h6>
                    <ul>
                        <li><strong>Grafik Transaksi:</strong> Visualisasi pergerakan stok masuk (hijau) dan keluar (merah) selama 7 hari terakhir.</li>
                        <li><strong>Ringkasan Bulan Ini:</strong> Akumulasi total stok yang masuk dan keluar sepanjang bulan berjalan, memudahkan evaluasi performa bulanan.</li>
                    </ul>

                    <h6 class="fw-semibold mt-4">4. Tabel Informasi Penting</h6>
                    <ul>
                        <li><strong>Barang Stok Menipis:</strong> Daftar 5 barang prioritas yang stoknya sudah mencapai batas minimum. Klik tombol "Lihat Semua" untuk melihat daftar lengkap dan melakukan pembelian ulang.</li>
                        <li><strong>Transaksi Terakhir:</strong> Log aktivitas terbaru yang terjadi di sistem, memudahkan pemantauan aktivitas staff secara realtime.</li>
                    </ul>

                    <h6 class="fw-semibold mt-4">5. Aksi Cepat (Quick Actions)</h6>
                    <p>Tombol shortcut untuk akses cepat ke fitur yang sering digunakan:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-success p-2"><i class="bi bi-box-arrow-in-down me-1"></i>Stok Masuk</span>
                        <span class="badge bg-danger p-2"><i class="bi bi-box-arrow-up me-1"></i>Stok Keluar</span>
                        <span class="badge bg-primary p-2"><i class="bi bi-upc-scan me-1"></i>Scan Barcode</span>
                        <span class="badge bg-info p-2"><i class="bi bi-plus-circle me-1"></i>Tambah Barang</span>
                    </div>
                </div>
            </div>

            <!-- Profil Perusahaan -->
            <!-- Profil Perusahaan -->
            <div class="card mb-4" id="profile">
                <div class="card-header">
                    <h5 class="mb-0">🏢 Profil Perusahaan (Settings)</h5>
                </div>
                <div class="card-body">
                    <p>Halaman ini menampilkan informasi perusahaan yang akan muncul di kop laporan dan header tanda terima.</p>
                    
                    <div class="alert alert-danger">
                        <i class="bi bi-lock-fill me-2"></i>
                        <strong>Akses Terbatas:</strong> Hanya <strong>Admin</strong> yang dapat mengubah pengaturan ini.
                    </div>

                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/company_profile.png') }}" alt="Profil Perusahaan" class="img-fluid rounded shadow" style="max-width: 400px; border: 2px dashed #ccc;">
                        <p class="text-muted small mt-2">Gambar 14: Halaman Edit Profile Perusahaan</p>
                    </div>

                    <h6 class="fw-semibold">Data yang Diatur:</h6>
                    <ul>
                        <li><strong>Nama Perusahaan:</strong> Muncul di header aplikasi dan laporan.</li>
                        <li><strong>Alamat & Kontak:</strong> Muncul di kop surat laporan PDF.</li>
                        <li><strong>Logo Perusahaan:</strong> Muncul di halaman Login dan pojok kiri atas laporan.</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> Hubungi Administrator jika ada perubahan data perusahaan.
                    </div>
                </div>
            </div>

            <!-- Manajemen Gudang -->
            <div class="card mb-4" id="gudang">
                <div class="card-header">
                    <h5 class="mb-0">🏭 Manajemen Gudang (Multi-Warehouse)</h5>
                </div>
                <div class="card-body">
                    <p>Sistem ini mendukung pengelolaan banyak gudang (Multi-Warehouse). Anda dapat mengatur gudang cabang dan memantau stok di setiap lokasi.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/manual_warehouse_list.png') }}" alt="Daftar Gudang" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar: Daftar Gudang</p>
                    </div>

                    <h6 class="fw-semibold">Fitur Gudang:</h6>
                    <ul>
                        <li><strong>Tambah Gudang</strong>: Daftarkan lokasi gudang baru beserta alamatnya.</li>
                        <li><strong>Akses User</strong>: Setiap Staff dapat ditugaskan ke satu gudang spesifik.</li>
                        <li><strong>Isolasi Stok</strong>: Transaksi dan stok admin/staff terpisah sesuai gudang masing-masing.</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="bi bi-person-badge me-2"></i>
                        <strong>Penugasan Staff:</strong> Saat membuat user baru, Admin wajib memilih gudang penempatan. Staff hanya bisa melihat dan mengelola stok di gudang tersebut.
                    </div>
                </div>
            </div>

            <!-- Manajemen Barang -->
            <div class="card mb-4" id="barang">
                <div class="card-header">
                    <h5 class="mb-0">📦 Manajemen Barang</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold">Daftar Barang</h6>
                    <p>Lihat semua barang yang tersimpan di gudang. Gunakan filter untuk mencari barang spesifik.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/items-list.png') }}" alt="Daftar Barang" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar 3: Daftar Barang dengan Filter</p>
                    </div>

                    <h6 class="fw-semibold mt-4">Tambah Barang Baru</h6>
                    <p>Klik tombol <strong>"+ Tambah Barang"</strong> untuk menambah barang baru.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/add-item.png') }}" alt="Tambah Barang" class="img-fluid rounded shadow" style="max-width: 600px;">
                        <p class="text-muted small mt-2">Gambar 4: Form Tambah Barang dengan Scan Barcode</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Field yang harus diisi:</h6>
                            <ul>
                                <li><strong>Kode Barang</strong> - Dapat scan atau input manual</li>
                                <li><strong>Nama Barang</strong> - Nama lengkap item</li>
                                <li><strong>Kategori</strong> - Pilih dari daftar</li>
                                <li><strong>Satuan</strong> - Pilih unit (pcs, kg, dll)</li>
                                <li><strong>Stok Awal</strong> - Jumlah awal</li>
                                <li><strong>Minimum Stok</strong> - Batas notifikasi</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="bi bi-upc-scan me-2"></i>
                                <strong>Fitur Scan Barcode:</strong><br>
                                Klik "Mulai Scan" untuk mengaktifkan kamera dan scan barcode produk secara otomatis.
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-semibold mt-4">Detail Barang</h6>
                    <p>Klik nama barang untuk melihat detail lengkap termasuk barcode dan riwayat transaksi.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/item-detail.png') }}" alt="Detail Barang" class="img-fluid rounded shadow" style="max-width: 600px;">
                        <p class="text-muted small mt-2">Gambar 5: Detail Barang dengan Barcode</p>
                    </div>
                </div>
            </div>

            <!-- Transaksi Stok -->
            <div class="card mb-4" id="transaksi">
                <div class="card-header">
                    <h5 class="mb-0">🔄 Transaksi Stok</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Fitur Baru:</strong> Satu transaksi sekarang dapat berisi banyak barang sekaligus!
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-success"><i class="bi bi-box-arrow-in-down me-1"></i> Stok Masuk</h6>
                            <p>Catat barang yang masuk ke gudang (pembelian, retur, dll) dalam satu dokumen transaksi.</p>
                            
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/manual/stock-in.png') }}" alt="Stok Masuk" class="img-fluid rounded shadow">
                                <p class="text-muted small mt-2">Gambar 6: Form Stok Masuk Multi-Item</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-danger"><i class="bi bi-box-arrow-up me-1"></i> Stok Keluar</h6>
                            <p>Catat barang yang keluar dari gudang (penjualan, rusak, dll) dalam satu dokumen transaksi.</p>
                            
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/manual/stock-out.png') }}" alt="Stok Keluar" class="img-fluid rounded shadow">
                                <p class="text-muted small mt-2">Gambar 7: Form Stok Keluar Multi-Item</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-semibold">Langkah Transaksi Multi-Item:</h6>
                    <ol>
                        <li>Klik <strong>"Stok Masuk"</strong> atau <strong>"Stok Keluar"</strong> di sidebar</li>
                        <li>Isi <strong>Catatan Transaksi</strong> jika diperlukan</li>
                        <li>Klik <strong>"+ Tambah Barang"</strong> untuk menambah baris item</li>
                        <li>Untuk setiap item: pilih barang, masukkan jumlah, tambah catatan item (opsional)</li>
                        <li>Klik tombol <strong>Simpan Transaksi</strong></li>
                    </ol>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Stok keluar tidak bisa melebihi stok tersedia untuk setiap item!
                    </div>

                    <h6 class="fw-semibold mt-4">Riwayat Transaksi</h6>
                    <p>Lihat semua dokumen transaksi dengan informasi total item, total quantity, dan status tanda terima.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/transaction-history.png') }}" alt="Riwayat Transaksi" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar 8: Daftar Dokumen Transaksi</p>
                    </div>
                </div>
            </div>

            <!-- Transfer Stok -->
            <div class="card mb-4" id="stock-transfer">
                <div class="card-header">
                    <h5 class="mb-0">🚚 Transfer Stok Antar Gudang</h5>
                </div>
                <div class="card-body">
                    <p>Pindahkan stok barang dari satu gudang ke gudang lain dengan aman dan tercatat.</p>

                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/manual_stock_transfer.png') }}" alt="Transfer Stok" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar: Form Transfer Stok</p>
                    </div>

                    <h6 class="fw-semibold">Cara Melakukan Transfer:</h6>
                    <ol>
                        <li>Pilih menu <strong>Transfer Stok</strong>.</li>
                        <li>Klik <strong>Buat Transfer Baru</strong>.</li>
                        <li>Pilih <strong>Gudang Asal</strong> (Otomatis untuk Staff) dan <strong>Gudang Tujuan</strong>.</li>
                        <li>Masukkan daftar barang dan jumlah yang ingin dipindahkan.</li>
                        <li>Klik <strong>Proses Transfer</strong>.</li>
                    </ol>

                    <div class="alert alert-success">
                        <i class="bi bi-shield-check me-2"></i>
                        <strong>Otomatisasi:</strong> Sistem akan otomatis membuat dokumen <strong>Stok Keluar</strong> di gudang asal dan <strong>Stok Masuk</strong> di gudang tujuan. Kedua dokumen saling terhubung dengan Nomor Referensi Transfer (TRF-XXX).
                    </div>
                </div>
            </div>

            <!-- Tanda Terima -->
            <div class="card mb-4" id="tanda-terima">
                <div class="card-header">
                    <h5 class="mb-0">📝 Tanda Terima Digital</h5>
                </div>
                <div class="card-body">
                    <p>Membuat bukti serah terima barang secara digital tanpa kertas.</p>
                    
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/manual/receipt-form.png') }}" alt="Form Tanda Terima" class="img-fluid rounded shadow" style="max-width: 700px;">
                        <p class="text-muted small mt-2">Gambar 9: Form Input Tanda Tangan Digital</p>
                    </div>

                    <h6 class="fw-semibold">Fitur Tanda Terima:</h6>
                    <ul>
                        <li><i class="bi bi-check text-success me-1"></i> Menampilkan semua barang dalam satu dokumen</li>
                        <li><i class="bi bi-check text-success me-1"></i> Tanda tangan digital dengan canvas (gambar langsung)</li>
                        <li><i class="bi bi-check text-success me-1"></i> Opsi upload gambar tanda tangan</li>
                        <li><i class="bi bi-check text-success me-1"></i> Export ke PDF untuk dicetak</li>
                        <li><i class="bi bi-check text-success me-1"></i> Penguncian dokumen setelah ditandatangani</li>
                    </ul>

                    <h6 class="fw-semibold mt-4">Langkah Generate Tanda Terima:</h6>
                    <ol>
                        <li>Buka <strong>Riwayat Transaksi</strong></li>
                        <li>Klik dropdown ⋮ pada transaksi yang diinginkan</li>
                        <li>Pilih <strong>"Tanda Terima"</strong></li>
                        <li>Isi nama <strong>Pengirim</strong> dan <strong>Penerima</strong></li>
                        <li>Gambar tanda tangan di canvas atau upload gambar</li>
                        <li>Centang <strong>"Kunci tanda terima"</strong> jika sudah final</li>
                        <li>Klik <strong>"Simpan Tanda Tangan"</strong></li>
                        <li>Download PDF dengan tombol <strong>"Download PDF"</strong></li>
                    </ol>

                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/receipt-pdf.png') }}" alt="PDF Tanda Terima" class="img-fluid rounded shadow" style="width: 100%; max-width: 500px;">
                        <p class="text-muted small mt-2">Gambar 10: Contoh PDF Tanda Terima</p>
                    </div>

                    <div class="alert alert-danger">
                        <i class="bi bi-lock me-2"></i>
                        <strong>Perhatian:</strong> Setelah tanda terima dikunci, dokumen tidak dapat diubah lagi. Pastikan semua informasi sudah benar sebelum mengunci!
                    </div>
                </div>
            </div>

            <!-- Barcode Scanner -->
            <div class="card mb-4" id="barcode">
                <div class="card-header">
                    <h5 class="mb-0">📷 Scan Barcode (Multi-Item)</h5>
                </div>
                <div class="card-body">
                    <p>Fitur scan barcode telah diperbarui dengan sistem <strong>Keranjang (Cart)</strong>, memungkinkan Anda men-scan banyak barang sekaligus sebelum disimpan.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/barcode-scanner.png') }}" alt="Barcode Scanner Multi-Item" class="img-fluid rounded shadow" style="width: 100%; max-width: 500px;">
                        <p class="text-muted small mt-2">Gambar 9: Halaman Scan Barcode dengan Sistem Keranjang</p>
                    </div>

                    <h6 class="fw-semibold">Cara Menggunakan:</h6>
                    <ol>
                        <li>Pilih <strong>Mode Transaksi</strong> di panel kiri (Stok Masuk / Stok Keluar).</li>
                        <li>Klik <strong>"Mulai Kamera"</strong> dan arahkan ke barcode barang.</li>
                        <li>Barang yang discan akan otomatis masuk ke <strong>Tabel Keranjang</strong> di sebelah kanan.</li>
                        <li>Scan barang yang sama berulang kali akan <strong>menambah jumlah (quantity)</strong> secara otomatis.</li>
                        <li>Anda juga bisa mengubah jumlah secara manual dengan tombol (+) atau (-).</li>
                        <li>Jika semua barang sudah masuk, klik tombol <strong>"Proses Transaksi"</strong> untuk menyimpan semua item dalam satu dokumen.</li>
                    </ol>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Validasi Stok Keluar:</strong><br>
                        Jika memilih mode <strong>Stok Keluar</strong>, sistem akan memberi peringatan dan mencegah proses jika jumlah barang yang discan melebihi stok yang tersedia di gudang.
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-phone me-2"></i>
                        <strong>Akses dari HP:</strong><br>
                        Untuk scan dengan kamera HP, akses aplikasi menggunakan IP komputer: <code>http://[IP_KOMPUTER]:8000</code>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-semibold mt-4">Cetak Barcode</h6>
                    <p>Setiap barang memiliki barcode unik yang bisa dicetak.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/print-barcode.png') }}" alt="Cetak Barcode" class="img-fluid rounded shadow" style="width: 100%; max-width: 400px;">
                        <p class="text-muted small mt-2">Gambar 10: Preview Cetak Barcode</p>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-semibold mt-4" id="scanner-usb">📷 Menggunakan Scanner USB (Alat Tembak)</h6>
                    <div class="alert alert-primary">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> Scanner USB bekerja dengan cara menggantikan fungsi Keyboard. Alat ini mendeteksi barcode lalu mengetikkan kodenya secara cepat.
                    </div>
                    
                    <p><strong>Cara Penggunaan:</strong></p>
                    <ol>
                        <li>
                            Klik kolom input teks di mana Anda ingin memasukkan kode barang (Contoh: "Ketik kode barang..." di menu Scanner, atau kolom Kode Barang di Tambah Barang).
                            <div class="text-center my-3">
                                <img src="{{ asset('images/manual/manual_usb_scanner_1.png') }}" alt="Ilustrasi Input Focus" class="img-fluid rounded shadow" style="max-width: 400px; border: 2px dashed #ccc;">
                                <p class="text-muted small mt-2">Gambar 11: Pastikan kursor aktif di kolom input</p>
                            </div>
                        </li>
                        <li>
                            Arahkan alat scanner ke barcode barang lalu tekan pemicu (tembak).
                        </li>
                        <li>Kode akan otomatis terisi di kolom tersebut.</li>
                        <li>Jika scanner Anda memiliki fitur <strong>"Auto Enter"</strong> (biasanya default), aplikasi akan langsung memproses kode tersebut tanpa perlu klik tombol Enter atau Cek manual.</li>
                    </ol>
                    
                    <div class="alert alert-secondary">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Tip:</strong> Untuk pengguna Scanner USB, Anda <strong>TIDAK PERLU</strong> mengklik tombol "Mulai Kamera". Cukup pastikan kursor mouse aktif di kolom input (berkedip).
                    </div>
                </div>
            </div>

            <!-- Laporan -->
            <div class="card mb-4" id="laporan">
                <div class="card-header">
                    <h5 class="mb-0">📈 Laporan</h5>
                </div>
                <div class="card-body">
                    <p>Sistem menyediakan berbagai laporan untuk analisis gudang:</p>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-day text-primary fs-1"></i>
                                    <h6 class="mt-2">Laporan Harian</h6>
                                    <p class="small text-muted">Transaksi per tanggal</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-month text-success fs-1"></i>
                                    <h6 class="mt-2">Laporan Bulanan</h6>
                                    <p class="small text-muted">Rangkuman per bulan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="bi bi-box-seam text-info fs-1"></i>
                                    <h6 class="mt-2">Laporan Stok</h6>
                                    <p class="small text-muted">Stok semua barang</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/reports.png') }}" alt="Halaman Laporan" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar 11: Menu Laporan</p>
                    </div>

                    <h6 class="fw-semibold">Export Laporan</h6>
                    <p>Semua laporan dapat di-export ke format:</p>
                    <ul>
                        <li><i class="bi bi-file-pdf text-danger me-1"></i> <strong>PDF</strong> - Untuk print atau arsip</li>
                        <li><i class="bi bi-file-excel text-success me-1"></i> <strong>Excel</strong> - Untuk analisis lanjutan</li>
                    </ul>
                </div>
            </div>

            <!-- Audit Logs -->
            <div class="card mb-4" id="audit">
                <div class="card-header">
                    <h5 class="mb-0">🛡️ Audit Logs (Keamanan)</h5>
                </div>
                <div class="card-body">
                    <p>Untuk menjaga keamanan dan akuntabilitas, sistem mencatat setiap aktivitas penting yang dilakukan oleh pengguna.</p>

                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/manual_audit_log.png') }}" alt="Audit Log" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar: Halaman Audit Logs</p>
                    </div>

                    <h6 class="fw-semibold">Cara Akses Menu:</h6>
                    <ol>
                        <li>Klik nama/foto profil Anda di <strong>Pojok Kanan Atas</strong> (Topbar).</li>
                        <li>Pilih menu <strong>"Audit Logs"</strong> dari dropdown yang muncul.</li>
                    </ol>

                    <h6 class="fw-semibold">Aktivitas yang Dicatat:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Aktivitas</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary">Create Transaction</span></td>
                                    <td>Mencatat siapa yang membuat transaksi, waktu, dan detail barang.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info text-dark">Transfer Stock</span></td>
                                    <td>Mencatat perpindahan stok antar gudang (Asal -> Tujuan).</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">Sign Receipt</span></td>
                                    <td>Mencatat saat dokumen ditandatangani digital.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Lock Receipt</span></td>
                                    <td>Mencatat penguncian dokumen (finalisasi).</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Delete Transaction</span></td>
                                    <td>Mencatat penghapusan data transaksi (Hanya Admin).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small mt-2">
                        *Halaman ini hanya dapat diakses oleh Level <strong>Admin</strong> dan <strong>Owner</strong>.
                    </p>
                </div>
            </div>

            <!-- User Management -->
            <div class="card mb-4" id="user">
                <div class="card-header">
                    <h5 class="mb-0">👥 Manajemen User (Admin Only)</h5>
                </div>
                <div class="card-body">
                    <p>Hanya Admin yang dapat mengelola pengguna sistem.</p>
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/manual/users-list.png') }}" alt="Daftar User" class="img-fluid rounded shadow">
                        <p class="text-muted small mt-2">Gambar 12: Daftar User</p>
                    </div>

                    <h6 class="fw-semibold">Role Pengguna:</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Hak Akses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td>Akses penuh ke semua fitur termasuk kelola user</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary">Staff</span></td>
                                <td>
                                    Akses terbatas sesuai <strong>Penempatan Gudang/Kota</strong>.
                                    <ul>
                                        <li>Hanya bisa melihat stok di gudang tempat mereka ditugaskan.</li>
                                        <li>Memproses transaksi masuk/keluar hanya untuk gudang tersebut.</li>
                                        <li>Tidak bisa melihat data gudang lain.</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning text-dark">Owner</span></td>
                                <td>Lihat barang, lihat transaksi, akses laporan</td>
                            </tr>
                        </tbody>
                    </table>
                    <h6 class="fw-semibold mt-4">➕ Cara Menambah User Baru:</h6>
                    <ol>
                        <li>Masuk ke menu <strong>Users</strong> (hanya Admin).</li>
                        <li>Klik tombol <strong>"Tambah User"</strong>.</li>
                        <li>Isi <strong>Nama</strong>, <strong>Email</strong>, dan <strong>Password</strong>.</li>
                        <li>Pilih <strong>Role</strong> (Admin, Staff, atau Owner).</li>
                        <li>
                            <strong>PENTING:</strong> Jika memilih Role <strong>Staff</strong>, kolom <strong>"Penempatan Gudang"</strong> akan muncul.
                            <br>
                            Pilih gudang/kota di mana staff tersebut akan bertugas. Staff <strong>TIDAK AKAN</strong> bisa mengakses data gudang lain selain yang dipilih di sini.
                        </li>
                        <li>Klik <strong>Simpan</strong>.</li>
                    </ol>

                    <div class="text-center my-3">
                        <img src="{{ asset('images/manual/manual_add_user.png') }}" alt="Form Tambah User" class="img-fluid rounded shadow" style="max-width: 400px; border: 2px dashed #ccc;">
                        <p class="text-muted small mt-2">Gambar 13: Form Tambah User (Pilih Gudang untuk Staff)</p>
                    </div>
                </div>
            </div>



            <!-- Dark Mode -->
            <div class="card mb-4" id="dark-mode">
                <div class="card-header">
                    <h5 class="mb-0">🌙 Dark Mode</h5>
                </div>
                <div class="card-body">
                    <p>Aplikasi mendukung mode gelap untuk kenyamanan mata.</p>
                    
                    <div class="row">
                        <div class="col-md-6 text-center mb-3">
                            <img src="{{ asset('images/manual/light-mode.png') }}" alt="Light Mode" class="img-fluid rounded shadow">
                            <p class="text-muted small mt-2">Gambar 13: Light Mode</p>
                        </div>
                        <div class="col-md-6 text-center mb-3">
                            <img src="{{ asset('images/manual/dark-mode.png') }}" alt="Dark Mode" class="img-fluid rounded shadow">
                            <p class="text-muted small mt-2">Gambar 14: Dark Mode</p>
                        </div>
                    </div>

                    <h6 class="fw-semibold">Cara Mengaktifkan:</h6>
                    <ol>
                        <li>Klik icon <i class="bi bi-moon-fill"></i> (bulan) di topbar</li>
                        <li>Mode akan berganti ke Dark Mode</li>
                        <li>Klik icon <i class="bi bi-sun-fill"></i> (matahari) untuk kembali ke Light Mode</li>
                    </ol>

                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Pilihan tema akan tersimpan otomatis di browser Anda.
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card mb-4" id="faq">
                <div class="card-header">
                    <h5 class="mb-0">❓ FAQ (Pertanyaan Umum)</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Kenapa kamera tidak bisa diakses?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Kamera membutuhkan koneksi <strong>HTTPS</strong> atau akses via <strong>localhost</strong>. 
                                    Jika akses dari HP via IP, gunakan Chrome flags untuk mengizinkan origin tidak aman.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Bagaimana cara mengakses dari HP?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Pastikan HP dan komputer di WiFi yang sama</li>
                                        <li>Jalankan server dengan: <code>php artisan serve --host=0.0.0.0</code></li>
                                        <li>Akses dari HP: <code>http://[IP_KOMPUTER]:8000</code></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Lupa password, bagaimana cara reset?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Hubungi Administrator untuk mereset password akun Anda.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Apakah bisa menghapus transaksi?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, Admin dapat menghapus transaksi dari halaman riwayat transaksi 
                                    <strong>selama tanda terima belum dikunci</strong>. 
                                    Stok akan otomatis dikembalikan/dikurangi sesuai tipe transaksi.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Bagaimana cara menambah banyak barang dalam satu transaksi?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Buka form Stok Masuk atau Stok Keluar</li>
                                        <li>Klik tombol <strong>"+ Tambah Barang"</strong></li>
                                        <li>Untuk setiap baris, pilih barang dan masukkan jumlah</li>
                                        <li>Ulangi langkah 2-3 untuk menambah item lainnya</li>
                                        <li>Klik <strong>"Simpan Transaksi"</strong> untuk menyimpan semua item sekaligus</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    Apa yang terjadi setelah tanda terima dikunci?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Setelah dikunci:
                                    <ul>
                                        <li>Tanda tangan tidak bisa diubah lagi</li>
                                        <li>Transaksi tidak bisa dihapus (bahkan oleh Admin)</li>
                                        <li>PDF tanda terima bisa diunduh kapan saja</li>
                                    </ul>
                                    <strong>Pastikan semua informasi sudah benar sebelum mengunci!</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-muted py-4">
                <p class="mb-0">Warehouse Management System © {{ date('Y') }}</p>
                <small>Versi 1.0 - Dokumentasi terakhir diperbarui: {{ date('d M Y') }}</small>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-link {
        color: var(--text-secondary);
        padding: 0.5rem 1rem;
        border-left: 3px solid transparent;
    }
    .nav-link:hover {
        color: var(--primary);
        background: var(--table-hover);
        border-left-color: var(--primary);
    }
    html {
        scroll-behavior: smooth;
    }
</style>
@endpush
@endsection
