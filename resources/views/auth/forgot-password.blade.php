<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXIOM — Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(10, 10, 20, 0.55);
            z-index: 0;
        }

        .auth-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 400px;
            padding: 20px 16px;
        }

        .card {
            position: relative;
            z-index: 1;
            background: #17171f;
            border-radius: 16px 16px 0 0;
            padding: 40px 36px 32px;
            width: 100%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5);
            animation: fadeUp 0.45s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .logo-text {
            font-size: 1.9rem;
            font-weight: 700;
            color: #c8c5f0;
            letter-spacing: 0.06em;
        }

        .page-heading {
            font-size: 1rem;
            font-weight: 600;
            color: #c8c5f0;
            margin-bottom: 6px;
            text-align: center;
        }

        .page-subtext {
            font-size: 0.82rem;
            color: #8884a8;
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .alert {
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 0.82rem;
            margin-bottom: 18px;
            line-height: 1.45;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.35);
            color: #f09090;
        }

        .form-group { margin-bottom: 16px; }

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

        .form-group input::placeholder { color: #8884a8; }
        .form-group input:focus {
            border-color: #a8a4e0;
            background: #f0eeff;
        }

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

        .btn-submit:hover { background: #bbb8f0; transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }

        .bottom-link-wrap {
            position: relative;
            z-index: 1;
            background: rgba(30, 30, 46, 0.72);
            border-radius: 0 0 16px 16px;
            padding: 14px 36px;
            text-align: center;
            width: 100%;
            font-size: 0.84rem;
            color: #b0aec8;
        }

        .bottom-link-wrap a {
            color: #a8a4e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .bottom-link-wrap a:hover { color: #c8c5f0; text-decoration: underline; }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="card">
        <div class="logo">
            <img src="/images/axiom-logo-trans.png" alt="AXIOM Logo" style="height:44px;width:auto;">
            <span class="logo-text">AXIOM</span>
        </div>

        <div class="page-heading">Forgot Password?</div>
        <p class="page-subtext">
            Enter your registered email address and we'll let you reset your password.
        </p>

        @if(session('verify_error'))
            <div class="alert alert-error">{{ session('verify_error') }}</div>
        @endif

        @if($errors->has('email'))
            <div class="alert alert-error">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('axiom.password.verify') }}" novalidate>
            @csrf
            <div class="form-group">
                <input type="email" name="email"
                       placeholder="Email Address"
                       value="{{ old('email') }}"
                       autocomplete="email"
                       required>
            </div>
            <button type="submit" class="btn-submit">CONTINUE</button>
        </form>
    </div>

    <div class="bottom-link-wrap">
        Remembered it? <a href="{{ route('login') }}">Back to Login</a>
    </div>
</div>
</body>
</html>