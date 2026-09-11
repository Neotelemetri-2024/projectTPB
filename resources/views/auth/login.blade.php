<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Portal TPB</title>
    <link href="/images/logo-unand.png" rel="shortcut icon" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --login-ink: #1c1410;
            --login-muted: #6b5748;
            --login-line: #e5d7c8;
            --login-panel: #fffaf5;
            --login-accent: #d97706;
            --login-accent-deep: #b45309;
            --login-field: #ffffff;
            --login-display: "Fraunces", Georgia, "Times New Roman", serif;
            --login-body: "Source Sans 3", "DM Sans", ui-sans-serif, system-ui, sans-serif;
        }

        .login-page {
            font-family: var(--login-body);
            color: var(--login-ink);
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            background: var(--login-panel);
        }

        .login-visual {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            min-height: 100dvh;
        }

        .login-visual__img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transform: scale(1.06);
            animation: login-drift 22s ease-in-out infinite alternate;
        }

        .login-visual__veil {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(105deg, rgba(40, 22, 8, 0.78) 0%, rgba(55, 28, 8, 0.48) 48%, rgba(80, 40, 10, 0.28) 100%),
                linear-gradient(to top, rgba(30, 16, 6, 0.58), transparent 42%);
        }

        .login-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 2rem;
            position: relative;
            opacity: 0;
            transform: translateY(12px);
            animation: login-rise 0.75s ease 0.28s forwards;
        }

        .login-panel__inner {
            width: 100%;
            max-width: 24rem;
            margin: 0 auto;
        }

        .login-brand {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 2.25rem;
        }

        .login-brand img {
            width: 3.25rem;
            height: 3.25rem;
            object-fit: contain;
            flex-shrink: 0;
        }

        .login-brand__text {
            min-width: 0;
        }

        .login-brand__name {
            font-family: var(--login-display);
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: var(--login-ink);
            margin: 0;
        }

        .login-brand__sub {
            margin: 0.35rem 0 0;
            font-size: 0.8rem;
            line-height: 1.35;
            color: var(--login-muted);
        }

        .login-intro {
            margin-bottom: 1.75rem;
        }

        .login-intro h1 {
            margin: 0;
            font-family: var(--login-display);
            font-size: 1.55rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .login-intro p {
            margin: 0.5rem 0 0;
            font-size: 0.95rem;
            color: var(--login-muted);
            line-height: 1.45;
        }

        .login-alert {
            margin-bottom: 1.25rem;
            padding: 0.85rem 1rem;
            border: 1px solid #f0d3a8;
            background: #fff4e6;
            color: #7a3e08;
            font-size: 0.875rem;
            border-radius: 0.35rem;
        }

        .login-form {
            display: grid;
            gap: 1.15rem;
        }

        .login-field label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            color: var(--login-ink);
        }

        .login-field input[type="email"],
        .login-field input[type="password"],
        .login-field input[type="text"] {
            width: 100%;
            min-height: 2.85rem;
            padding: 0.7rem 0.9rem;
            border: 1px solid var(--login-line);
            border-radius: 0.35rem;
            background: var(--login-field);
            color: var(--login-ink);
            font-size: 0.95rem;
            box-shadow: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .login-field input:focus {
            outline: none;
            border-color: var(--login-accent);
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.18);
        }

        .login-field__password {
            position: relative;
        }

        .login-field__password input {
            padding-right: 2.75rem;
        }

        .login-toggle {
            position: absolute;
            right: 0.35rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: var(--login-muted);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 0.4rem 0.55rem;
            cursor: pointer;
        }

        .login-toggle:hover {
            color: var(--login-ink);
        }

        .login-error {
            margin: 0.4rem 0 0;
            font-size: 0.82rem;
            color: #b42318;
        }

        .login-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .login-remember {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--login-muted);
            cursor: pointer;
            user-select: none;
        }

        .login-remember input {
            width: 1rem;
            height: 1rem;
            accent-color: var(--login-accent);
            border-radius: 0.2rem;
        }

        .login-link {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--login-accent);
            text-decoration: none;
        }

        .login-link:hover {
            color: var(--login-accent-deep);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .login-submit {
            width: 100%;
            min-height: 2.95rem;
            border: 0;
            border-radius: 0.35rem;
            background: var(--login-accent);
            color: #fff;
            font-size: 0.95rem;
            font-weight: 650;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .login-submit:hover {
            background: var(--login-accent-deep);
        }

        .login-submit:active {
            transform: translateY(1px);
        }

        .login-note {
            margin: 0;
            font-size: 0.82rem;
            line-height: 1.45;
            color: var(--login-muted);
            text-align: center;
        }

        .login-footer {
            margin-top: 2.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--login-line);
            font-size: 0.75rem;
            color: #9a816c;
            text-align: center;
            line-height: 1.45;
        }

        @keyframes login-drift {
            from { transform: scale(1.06) translate3d(0, 0, 0); }
            to { transform: scale(1.12) translate3d(-1.5%, -1%, 0); }
        }

        @keyframes login-rise {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-visual__img,
            .login-panel {
                animation: none !important;
                opacity: 1;
                transform: none;
            }
        }

        @media (max-width: 1023px) {
            .login-page {
                grid-template-columns: 1fr;
            }

            .login-visual {
                min-height: 38vh;
                min-height: 38dvh;
            }

            .login-panel {
                padding: 1.75rem 1.25rem 2.5rem;
                justify-content: flex-start;
            }

            .login-brand {
                margin-bottom: 1.5rem;
            }

            .login-brand__name {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <aside class="login-visual" aria-hidden="true">
            <img
                class="login-visual__img"
                src="/images/rektorat.jpg"
                alt=""
                width="1600"
                height="1200"
                decoding="async"
            >
            <div class="login-visual__veil"></div>
        </aside>

        <main class="login-panel">
            <div class="login-panel__inner">
                <div class="login-brand">
                    <img src="/images/logo-unand.png" alt="Logo Universitas Andalas" width="52" height="52">
                    <div class="login-brand__text">
                        <p class="login-brand__name">Portal TPB</p>
                        <p class="login-brand__sub">Teknik Pertanian dan Biosistem</p>
        </div>
                </div>

                <div class="login-intro">
                    <h1>Masuk ke portal</h1>
                    <p>Gunakan akun yang diberikan administrator.</p>
                </div>

                @if (session('status'))
                <div class="login-alert" role="status">{{ session('status') }}</div>
                @endif

                <form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="login-field">
                        <label for="email">Email</label>
                        <input
                            type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                            placeholder="nama@unand.ac.id"
                                required
                                autofocus
                            autocomplete="username"
                        >
                        @error('email')
                        <p class="login-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="login-field">
                        <label for="password">Password</label>
                        <div class="login-field__password">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Password akun Anda"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="login-toggle" id="togglePassword" aria-label="Tampilkan password">
                                Lihat
                            </button>
                        </div>
                        @error('password')
                        <p class="login-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="login-row">
                        <label class="login-remember" for="remember">
                            <input id="remember" name="remember" type="checkbox">
                            <span>Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                        <a class="login-link" href="{{ route('password.request') }}">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit" class="login-submit">Masuk</button>

                    <p class="login-note">Belum punya akun? Hubungi administrator.</p>
                </form>

                <footer class="login-footer">
                    © {{ date('Y') }} Portal TPB · Universitas Andalas
                </footer>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            if (!toggle || !password) return;

            toggle.addEventListener('click', function () {
                const show = password.getAttribute('type') === 'password';
                password.setAttribute('type', show ? 'text' : 'password');
                toggle.textContent = show ? 'Sembunyi' : 'Lihat';
                toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    </script>
</body>
</html>
