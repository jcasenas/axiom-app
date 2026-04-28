<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXIOM — Reset Password</title>
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

        .email-pill {
            display: inline-block;
            background: rgba(168, 164, 224, 0.15);
            color: #a8a4e0;
            border-radius: 999px;
            padding: 4px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 20px;
            width: 100%;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

        .form-group { margin-bottom: 14px; position: relative; }

        .form-group .password-wrapper { position: relative; }

        .form-group input {
            width: 100%;
            background: #e8e6f0;
            border: 1.5px solid transparent;
            border-radius: 8px;
            padding: 13px 44px 13px 16px;
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

        .field-error {
            font-size: 0.75rem;
            color: #f09090;
            margin-top: 5px;
            padding-left: 2px;
        }

        .toggle-password {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #8884a8;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: #a8a4e0; }
        .toggle-password svg { width: 19px; height: 19px; }

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
            margin-top: 6px;
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

        <div class="page-heading">Reset Your Password</div>
        <p class="page-subtext">Choose a new password for your account.</p>

        {{-- Show which account is being reset --}}
        <div class="email-pill">{{ session('pwd_reset_email') }}</div>

        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('axiom.password.reset.submit') }}" novalidate>
            @csrf

            <div class="form-group">
                <div class="password-wrapper">
                    <input type="password" name="password"
                           id="password"
                           placeholder="New Password (min. 8 characters)"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="toggle-password"
                            onclick="togglePwd('password', 'icon-pw')"
                            aria-label="Toggle password visibility">
                        <svg id="icon-pw" xmlns="http://www.w3.org/2000/svg" fill="none"
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
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="password-wrapper">
                    <input type="password" name="password_confirmation"
                           id="password_confirmation"
                           placeholder="Confirm New Password"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="toggle-password"
                            onclick="togglePwd('password_confirmation', 'icon-confirm')"
                            aria-label="Toggle confirm password visibility">
                        <svg id="icon-confirm" xmlns="http://www.w3.org/2000/svg" fill="none"
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

            <button type="submit" class="btn-submit">RESET PASSWORD</button>
        </form>
    </div>

    <div class="bottom-link-wrap">
        <a href="{{ route('axiom.password.request') }}">← Use a different email</a>
    </div>
</div>

<script>
const eyeOff = `<path stroke-linecap="round" stroke-linejoin="round"
    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
       a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
       M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29
       m7.532 7.532l3.29 3.29M3 3l3.59 3.59
       m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7
       a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;

const eyeOn = `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
  <path stroke-linecap="round" stroke-linejoin="round"
    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
       -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;

function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    const isHidden = input.type === 'password';
    input.type     = isHidden ? 'text' : 'password';
    icon.innerHTML = isHidden ? eyeOn : eyeOff;
}
</script>
</body>
</html>