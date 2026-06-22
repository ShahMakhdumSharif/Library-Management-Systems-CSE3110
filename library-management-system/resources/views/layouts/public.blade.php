<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Central Library')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            --crimson: #a51c30;
            --ink: #1e1e1e;
            --muted: #5f6368;
            --line: #d8d8d8;
            --soft: #f6f4f0;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--ink);
            background: var(--white);
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1160px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            border-bottom: 1px solid var(--line);
            background: var(--soft);
            font-size: 14px;
        }

        .topbar-inner {
            display: flex;
            justify-content: flex-end;
            gap: 22px;
            padding: 10px 0;
        }

        .topbar a:hover,
        .main-nav a:hover {
            color: var(--crimson);
        }

        .site-header {
            border-bottom: 1px solid var(--line);
            background: var(--white);
        }

        .brand-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 24px 0;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            font-size: 25px;
            font-weight: 700;
        }

        .brand-mark {
            display: grid;
            width: 46px;
            height: 46px;
            place-items: center;
            color: var(--white);
            background: var(--crimson);
            font-weight: 700;
        }

        .main-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            color: var(--muted);
            font-weight: 600;
        }

        .auth-links {
            display: flex;
            gap: 10px;
        }

        .login-link,
        .register-link {
            padding: 10px 16px;
            border: 1px solid var(--crimson);
            font-weight: 700;
        }

        .login-link {
            color: var(--white);
            background: var(--crimson);
        }

        .register-link {
            color: var(--crimson);
            background: var(--white);
        }

        main {
            min-height: 58vh;
        }

        .hero {
            padding: 70px 0 54px;
        }

        .eyebrow {
            margin-bottom: 16px;
            color: var(--crimson);
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
        }

        h1 {
            max-width: 780px;
            margin: 0;
            font-size: clamp(42px, 7vw, 72px);
            line-height: 1;
            letter-spacing: 0;
        }

        .hero p {
            max-width: 720px;
            margin: 24px 0 0;
            color: var(--muted);
            font-size: 19px;
            line-height: 1.7;
        }

        .search-panel {
            max-width: 850px;
            margin-top: 34px;
            padding: 24px;
            border-top: 5px solid var(--crimson);
            background: var(--soft);
        }

        .search-panel label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: 700;
        }

        .search-row {
            display: grid;
            grid-template-columns: 1fr auto;
            border: 1px solid var(--line);
            background: var(--white);
        }

        .search-row input,
        .search-row button,
        .login-card input,
        .login-card select {
            min-height: 50px;
            border: 0;
            font: inherit;
        }

        .search-row input {
            width: 100%;
            padding: 0 16px;
        }

        .search-row button,
        .login-card button {
            padding: 0 22px;
            border: 0;
            color: var(--white);
            background: var(--crimson);
            font-weight: 700;
            cursor: pointer;
        }

        .link-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            margin-bottom: 68px;
            background: var(--line);
            border: 1px solid var(--line);
        }

        .link-grid a {
            min-height: 165px;
            padding: 26px;
            background: var(--white);
            color: var(--muted);
            line-height: 1.6;
        }

        .link-grid span {
            display: block;
            margin-bottom: 10px;
            color: var(--ink);
            font-size: 22px;
            font-weight: 700;
        }

        .auth-wrap {
            display: grid;
            grid-template-columns: minmax(0, .9fr) minmax(320px, 460px);
            gap: 48px;
            align-items: start;
            padding: 70px 0;
        }

        .auth-copy p {
            max-width: 560px;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.7;
        }

        .login-card {
            padding: 30px;
            border: 1px solid var(--line);
            border-top: 5px solid var(--crimson);
            background: var(--white);
        }

        .login-card h2 {
            margin: 0 0 24px;
            font-size: 28px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .login-card input,
        .login-card select {
            width: 100%;
            padding: 0 14px;
            border: 1px solid var(--line);
            background: var(--white);
        }

        .login-card button {
            width: 100%;
            min-height: 50px;
            margin-top: 6px;
        }

        .form-note {
            margin: 16px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .status-message {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-left: 4px solid var(--crimson);
            background: var(--soft);
            color: var(--ink);
            font-size: 14px;
            line-height: 1.5;
        }

        .site-footer {
            border-top: 1px solid var(--line);
            background: #171717;
            color: var(--white);
        }

        .footer-inner {
            display: grid;
            grid-template-columns: 1.2fr repeat(3, 1fr);
            gap: 34px;
            padding: 44px 0;
        }

        .site-footer h2,
        .site-footer h3 {
            margin: 0 0 14px;
        }

        .site-footer p,
        .site-footer a {
            color: #d7d7d7;
            line-height: 1.7;
        }

        .site-footer a {
            display: block;
            margin-bottom: 8px;
        }

        .copyright {
            border-top: 1px solid #3a3a3a;
            padding: 18px 0;
            color: #c7c7c7;
            font-size: 14px;
        }

        @media (max-width: 850px) {
            .brand-row,
            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .auth-wrap,
            .link-grid,
            .footer-inner {
                grid-template-columns: 1fr;
            }

            .search-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container topbar-inner">
            <a href="#">Ask a Librarian</a>
            <a href="#">Library Hours</a>
            <a href="#">Catalog</a>
        </div>
    </div>

    <header class="site-header">
        <div class="container brand-row">
            <a href="{{ url('/') }}" class="brand" aria-label="Central Library home">
                <span class="brand-mark">C</span>
                <span>Central Library</span>
            </a>
            <nav class="main-nav" aria-label="Main navigation">
                <a href="{{ url('/') }}">Home</a>
                <a href="#">Collections</a>
                <a href="#">Services</a>
                <a href="#">Reports</a>
                <span class="auth-links">
                    <a class="login-link" href="{{ url('/login') }}">Login</a>
                    <a class="register-link" href="{{ url('/register') }}">Register</a>
                </span>
            </nav>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <section>
                <h2>Central Library</h2>
            </section>
            <section>
                <h3>Services</h3>
                <a href="#">Issue & Return</a>
                <a href="#">Reservations</a>
            </section>
            <section>
                <h3>Support</h3>
                <a href="#">Help Desk</a>
                <a href="#">Library Policy</a>
                <a href="#">Contact</a>
            </section>
            <section>
                <h3>Account</h3>
                <a href="{{ url('/login') }}">Login</a>
                <a href="{{ url('/register') }}">Register</a>
            </section>
        </div>
        <div class="container copyright">
            © {{ date('Y') }} Central Library. All rights reserved.
        </div>
    </footer>
</body>
</html>
