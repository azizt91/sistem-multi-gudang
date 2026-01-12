<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Models\CompanyProfile::get()->favicon_url }}">
    
    <style>
        /* ===== Theme Variables ===== */
        :root, [data-bs-theme="light"] {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --primary-light: #818CF8;
            --secondary: #0EA5E9;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            --sidebar-width: 260px;
            
            /* Light Theme Colors */
            --bg-body: #F1F5F9;
            --bg-card: #FFFFFF;
            --bg-topbar: #FFFFFF;
            --bg-input: #FFFFFF;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --border-color: #E5E7EB;
            --shadow: rgba(0,0,0,0.1);
            --sidebar-bg: #1E293B;
            --sidebar-bg-end: #0F172A;
            --table-hover: #F9FAFB;
            --dropdown-bg: #FFFFFF;
            --btn-light-bg: #F3F4F6;
            --btn-light-border: #E5E7EB;
        }

        [data-bs-theme="dark"] {
            --bg-body: #0F172A;
            --bg-card: #1E293B;
            --bg-topbar: #1E293B;
            --bg-input: #334155;
            --text-primary: #F1F5F9;
            --text-secondary: #CBD5E1;
            --text-muted: #94A3B8;
            --border-color: #334155;
            --shadow: rgba(0,0,0,0.3);
            --sidebar-bg: #0F172A;
            --sidebar-bg-end: #020617;
            --table-hover: #334155;
            --dropdown-bg: #1E293B;
            --btn-light-bg: #334155;
            --btn-light-border: #475569;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Theme transition */
        body, .topbar, .card, .sidebar, .dropdown-menu, .form-control, .form-select, .table, .btn {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #0F172A 100%);
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .sidebar-nav .nav-item {
            margin: 0.15rem 0.75rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar-nav .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .sidebar-nav .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .nav-section-title {
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1rem 1.5rem 0.5rem;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            background: var(--bg-topbar);
            padding: 1rem 1.5rem;
            box-shadow: 0 1px 3px var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border-color);
        }

        .topbar h5 {
            color: var(--text-primary);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-badge {
            position: relative;
        }

        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.65rem;
        }

        /* Content Area */
        .content-wrapper {
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px var(--shadow);
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .card-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-color);
        }

        /* Stats Cards */
        .stats-card {
            border-radius: 0.75rem;
            padding: 1.25rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stats-card .stats-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.3;
        }

        .stats-card .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stats-card .stats-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .stats-card.primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); }
        .stats-card.success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); }
        .stats-card.danger { background: linear-gradient(135deg, var(--danger) 0%, #DC2626 100%); }
        .stats-card.warning { background: linear-gradient(135deg, var(--warning) 0%, #D97706 100%); }
        .stats-card.info { background: linear-gradient(135deg, var(--info) 0%, #2563EB 100%); }

        /* Tables */
        .table {
            --bs-table-bg: var(--bg-card);
            --bs-table-color: var(--text-primary);
            --bs-table-border-color: var(--border-color);
            --bs-table-hover-bg: var(--table-hover);
        }

        .table th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
        }

        .table td {
            vertical-align: middle;
            padding: 0.875rem 0.75rem;
            color: var(--text-primary);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Forms */
        .form-control, .form-select {
            background-color: var(--bg-input);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--bg-input);
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
            color: var(--text-primary);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .input-group-text {
            background-color: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-secondary);
        }

        .form-label {
            color: var(--text-primary);
        }

        /* Dropdown */
        .dropdown-menu {
            background-color: var(--dropdown-bg);
            border-color: var(--border-color);
        }

        .dropdown-item {
            color: var(--text-primary);
        }

        .dropdown-item:hover {
            background-color: var(--table-hover);
            color: var(--text-primary);
        }

        .dropdown-header {
            color: var(--text-secondary);
        }

        .dropdown-divider {
            border-color: var(--border-color);
        }

        /* Buttons */
        .btn-light {
            background-color: var(--btn-light-bg);
            border-color: var(--btn-light-border);
            color: var(--text-primary);
        }

        .btn-light:hover {
            background-color: var(--border-color);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .page-header .breadcrumb {
            margin: 0;
            background: none;
            padding: 0;
            font-size: 0.85rem;
        }

        /* Text colors */
        .text-muted {
            color: var(--text-muted) !important;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            background: none;
            border: none;
            padding: 0.35rem 0.5rem;
            font-size: 1.25rem;
            cursor: pointer;
            color: var(--text-secondary);
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .theme-toggle:hover {
            background: var(--table-hover);
            color: var(--text-primary);
        }

        /* Modal */
        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .modal-header, .modal-footer {
            border-color: var(--border-color);
        }

        .btn-close {
            filter: var(--bs-btn-close-white-filter);
        }

        [data-bs-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            /* Hide user name on mobile */
            .topbar .user-name {
                display: none;
            }

            .topbar .topbar-left h5 {
                font-size: 1rem;
            }

            .topbar {
                padding: 0.75rem 1rem;
            }

            .topbar-right {
                gap: 0.5rem;
            }

            .topbar-right .btn {
                padding: 0.35rem 0.5rem;
            }

            .topbar-right .role-badge {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .topbar .topbar-left h5 {
                font-size: 0.9rem;
                max-width: 120px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Alert styles */
        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        /* Low stock badge */
        .low-stock-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* Scanner styles */
        .scanner-container {
            background: #000;
            border-radius: 0.75rem;
            overflow: hidden;
            position: relative;
        }

        .scanner-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: var(--danger);
            animation: scan 2s linear infinite;
        }

        @keyframes scan {
            0% { top: 0; }
            100% { top: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            @php
                $globalProfile = \App\Models\CompanyProfile::first();
            @endphp
            <div class="d-flex align-items-center">
                @if($globalProfile && $globalProfile->logo_url)
                    <img src="{{ $globalProfile->logo_url }}" alt="Logo" style="height: 30px; margin-right: 10px;" class="rounded">
                    <div>
                        <h4 style="font-size: 0.75rem; margin: 0; line-height: 1.2; word-break: break-word;">{{ $globalProfile->company_name }}</h4>
                        <small style="font-size: 0.65rem;">Warehouse Management</small>
                    </div>
                @else
                    <h4><i class="bi bi-box-seam me-2"></i>WMS</h4>
                    <small>Warehouse Management</small>
                @endif
            </div>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>

                <div class="nav-section-title">Master Data</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}">
                        <i class="bi bi-box-seam"></i> Barang
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}" href="{{ route('warehouses.index') }}">
                        <i class="bi bi-building"></i> Gudang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}">
                        <i class="bi bi-rulers"></i> Satuan
                    </a>
                </li>
                @endif

                <div class="nav-section-title">Transaksi</div>
                @if(auth()->user()->canCreateTransaction())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock-headers.create-in') ? 'active' : '' }}" href="{{ route('stock-headers.create-in') }}">
                        <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock-headers.create-out') ? 'active' : '' }}" href="{{ route('stock-headers.create-out') }}">
                        <i class="bi bi-box-arrow-up"></i> Stok Keluar
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock-headers.*') && !request()->routeIs('stock-headers.create-*') ? 'active' : '' }}" href="{{ route('stock-headers.index') }}">
                        <i class="bi bi-clock-history"></i> Riwayat Transaksi
                    </a>
                </li>
                @if(!auth()->user()->isOwner())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}" href="{{ route('stock-transfers.index') }}">
                        <i class="bi bi-arrow-left-right"></i> Transfer Stok
                    </a>
                </li>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                <div class="nav-section-title">Laporan</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                    </a>
                </li>
                @endif

                <div class="nav-section-title">Tools</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('scanner.*') ? 'active' : '' }}" href="{{ route('scanner.index') }}">
                        <i class="bi bi-upc-scan"></i> Scan Barcode
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manual.*') ? 'active' : '' }}" href="{{ route('manual.index') }}">
                        <i class="bi bi-book"></i> Panduan
                    </a>
                </li>

                @if(auth()->user()->isAdmin())
                <div class="nav-section-title">Pengaturan</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('company-profile.*') ? 'active' : '' }}" href="{{ route('company-profile.edit') }}">
                        <i class="bi bi-building-gear"></i> Profil Perusahaan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    </aside>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h5 class="mb-0 fw-semibold">@yield('title', 'Dashboard')</h5>
                </div>
            </div>
            <div class="topbar-right">
                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>

                @php
                    $lowStockCount = \App\Models\Item::lowStock()->count();
                @endphp
                @if($lowStockCount > 0)
                <a href="{{ route('items.index', ['low_stock' => 1]) }}" class="btn btn-light btn-sm notification-badge" title="Stok Menipis">
                    <i class="bi bi-bell-fill text-warning"></i>
                    <span class="badge bg-danger rounded-pill">{{ $lowStockCount }}</span>
                </a>
                @endif
                
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="user-name ms-1">{{ auth()->user()->name }}</span>
                        <span class="badge bg-primary ms-1 role-badge">{{ ucfirst(auth()->user()->role) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="dropdown-header">
                                <strong>{{ auth()->user()->name }}</strong>
                                <br><small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                <li class="nav-item">
                    <a class="dropdown-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                        <i class="bi bi-journal-text me-2"></i> Audit Logs
                    </a>
                </li>
                @endif
                
                <li class="nav-item mt-3">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ===== Theme Management =====
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        // Get saved theme or system preference
        function getPreferredTheme() {
            const savedTheme = localStorage.getItem('wms-theme');
            if (savedTheme) {
                return savedTheme;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        // Apply theme
        function setTheme(theme) {
            html.setAttribute('data-bs-theme', theme);
            localStorage.setItem('wms-theme', theme);
            updateThemeIcon(theme);
        }

        // Update icon
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('bi-moon-fill');
                themeIcon.classList.add('bi-sun-fill');
                themeToggle.title = 'Switch to Light Mode';
            } else {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-fill');
                themeToggle.title = 'Switch to Dark Mode';
            }
        }

        // Initialize theme
        setTheme(getPreferredTheme());

        // Toggle theme on click
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('wms-theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });

        // ===== Sidebar Toggle =====
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });

        // CSRF Token for AJAX
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    
    @stack('scripts')
</body>
</html>
