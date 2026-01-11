<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root, [data-bs-theme="light"] {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --bg-body: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --bg-card: #FFFFFF;
            --text-primary: #374151;
            --text-secondary: #6B7280;
            --border-color: #E5E7EB;
            --input-bg: #F9FAFB;
            --demo-bg: #F9FAFB;
            --code-bg: #EEF2FF;
        }

        [data-bs-theme="dark"] {
            --bg-body: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            --bg-card: #1E293B;
            --text-primary: #F1F5F9;
            --text-secondary: #94A3B8;
            --border-color: #334155;
            --input-bg: #334155;
            --demo-bg: #0F172A;
            --code-bg: #334155;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-card {
            background: var(--bg-card);
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
        }

        .theme-toggle-login {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            border: none;
            padding: 0.5rem;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s ease;
        }

        .theme-toggle-login:hover {
            background: rgba(255,255,255,0.3);
        }

        .login-header .logo {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
        }

        .login-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .login-header p {
            margin: 0.5rem 0 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .login-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 1.5px solid var(--border-color);
            background-color: var(--bg-card);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
            background-color: var(--bg-card);
            color: var(--text-primary);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .input-group-text {
            background: var(--input-bg);
            border: 1.5px solid var(--border-color);
            border-right: none;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .input-group .form-control {
            border-left: none;
        }

        .form-check-label {
            color: var(--text-primary);
        }

        .demo-credentials {
            background: var(--demo-bg);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            transition: background-color 0.3s ease;
        }

        .demo-credentials h6 {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .demo-credentials div {
            color: var(--text-primary);
        }

        .demo-credentials code {
            background: var(--code-bg);
            color: var(--primary);
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
        }

        [data-bs-theme="dark"] .demo-credentials code {
            color: #A5B4FC;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <button class="theme-toggle-login" id="themeToggle" title="Toggle Dark Mode">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>
                <div class="logo">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h4>Warehouse Management</h4>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            
            <div class="login-body">
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                   placeholder="Masukkan email Anda">
                        </div>
                        @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password"
                                   placeholder="Masukkan password Anda">
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </div>
                </form>

                <div class="demo-credentials">
                    <h6><i class="bi bi-info-circle me-1"></i> Demo Credentials</h6>
                    <div><strong>Admin:</strong> <code>admin@warehouse.test</code> / <code>password</code></div>
                    <div><strong>Staff:</strong> <code>staff@warehouse.test</code> / <code>password</code></div>
                    <div><strong>Owner:</strong> <code>owner@warehouse.test</code> / <code>password</code></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        function getPreferredTheme() {
            const savedTheme = localStorage.getItem('wms-theme');
            if (savedTheme) return savedTheme;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function setTheme(theme) {
            html.setAttribute('data-bs-theme', theme);
            localStorage.setItem('wms-theme', theme);
            updateThemeIcon(theme);
        }

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('bi-moon-fill');
                themeIcon.classList.add('bi-sun-fill');
            } else {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-fill');
            }
        }

        setTheme(getPreferredTheme());

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });
    </script>
</body>
</html>
