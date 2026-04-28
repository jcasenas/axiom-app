<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXIOM — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Page ── */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            background-color: #17171f;
            background-image: url('/images/auth_background.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Dark overlay over the background image */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(10, 10, 20, 0.55);
            z-index: 0;
        }

        /* ── Card ── */
        .card {
            position: relative;
            z-index: 1;
            background: #17171f;
            border-radius: 16px;
            padding: 44px 40px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5);
            animation: fadeUp 0.45s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Logo ── */
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            color: #a8a4e0;
        }

        .logo-text {
            font-size: 2rem;
            font-weight: 700;
            color: #c8c5f0;
            letter-spacing: 0.06em;
        }

        /* ── Alert / Flash messages ── */
        .alert {
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 0.82rem;
            margin-bottom: 18px;
            line-height: 1.45;
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.15);
            border: 1px solid rgba(39, 174, 96, 0.35);
            color: #6ee89b;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.35);
            color: #f09090;
        }

        /* ── Form ── */
        .form-group {
            position: relative;
            margin-bottom: 14px;
        }

        .form-group input {
            width: 100%;
            background: #e8e6f0;
            border: 1.5px solid transparent;
            border-radius: 8px;
            padding: 13px 16px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.92rem;
            color: #1a1a2e;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .form-group input::placeholder {
            color: #8884a8;
        }

        .form-group input:focus {
            border-color: #a8a4e0;
            background: #f0eeff;
        }

        /* Password wrapper — holds input + eye icon */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 46px;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            color: #8884a8;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: #a8a4e0; }

        .toggle-password svg { width: 20px; height: 20px; }

        /* ── Remember + Forgot row ── */
        .row-remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            margin-top: 4px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.84rem;
            color: #b0aec8;
            cursor: pointer;
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #a8a4e0;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.84rem;
            color: #a8a4e0;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: #c8c5f0; text-decoration: underline; }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            background: #a8a4e0;
            color: #1a1a2e;
            border: none;
            border-radius: 8px;
            padding: 13px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-submit:hover {
            background: #bbb8f0;
            transform: translateY(-1px);
        }

        .btn-submit:active { transform: translateY(0); }

        /* ── Bottom link (outside card) ── */
        .bottom-link-wrap {
            position: relative;
            z-index: 1;
            margin-top: 0;
            background: rgba(30, 30, 46, 0.72);
            border-radius: 0 0 16px 16px;
            padding: 14px 40px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            font-size: 0.84rem;
            color: #b0aec8;
            /* negative top margin to visually attach below card */
            margin-top: -6px;
        }

        .bottom-link-wrap a {
            color: #a8a4e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .bottom-link-wrap a:hover { color: #c8c5f0; text-decoration: underline; }

        /* ── Wrapper to stack card + bottom strip ── */
        .auth-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 400px;
            padding: 20px 16px;
        }

        .auth-wrapper .card { border-radius: 16px 16px 0 0; }
        .auth-wrapper .bottom-link-wrap { border-radius: 0 0 16px 16px; }
    </style>
</head>
<body>

<div class="auth-wrapper">

    {{-- ── CARD ── --}}
    <div class="card">

        {{-- Logo --}}
        <div class="logo">
            <img src="/images/axiom-logo-trans.png" alt="AXIOM Logo" style="height: 48px; width: auto;">
            <span class="logo-text">AXIOM</span>
        </div>

        {{-- Success flash (e.g. after registration) --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error flash --}}
        @if($errors->has('email'))
            <div class="alert alert-error">
                {{ $errors->first('email') }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                >
            </div>

            {{-- Password --}}
            <div class="form-group">
                <div class="password-wrapper">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                        {{-- Eye-off icon (default — password is hidden) --}}
                        <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                                     a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                                     M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29
                                     m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                                     m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7
                                     a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember Me + Forgot Password --}}
            <div class="row-remember">
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember Me
                </label>
                <a href="{{ route('axiom.password.request') }}" class="forgot-link">Forgot Password?</a>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">LOG IN</button>

        </form>
    </div>

    {{-- Bottom strip -- outside card --}}
    <div class="bottom-link-wrap">
        Don't have an account? <a href="{{ route('register') }}">Register</a>
    </div>

</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById('icon-' + inputId);
        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';

        // Swap between eye and eye-off icon
        icon.innerHTML = isHidden
            ? /* eye-open */
              `<path stroke-linecap="round" stroke-linejoin="round"
                 d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
               <path stroke-linecap="round" stroke-linejoin="round"
                 d="M2.458 12C3.732 7.943 7.523 5 12 5
                    c4.478 0 8.268 2.943 9.542 7
                    -1.274 4.057-5.064 7-9.542 7
                    -4.477 0-8.268-2.943-9.542-7z"/>`
            : /* eye-off */
              `<path stroke-linecap="round" stroke-linejoin="round"
                 d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                    a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                    M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29
                    m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                    m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7
                    a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
    }
</script>

</body>
</html>