<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXIOM — Register</title>
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

        /* ── Card — wider than login to fit two-column rows ── */
        .card {
            position: relative;
            z-index: 1;
            background: #17171f;
            border-radius: 16px 16px 0 0;
            padding: 40px 36px 32px;
            width: 100%;
            max-width: 480px;
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
            margin-bottom: 30px;
        }

        .logo-icon { width: 44px; height: 44px; color: #a8a4e0; }

        .logo-text {
            font-size: 1.9rem;
            font-weight: 700;
            color: #c8c5f0;
            letter-spacing: 0.06em;
        }

        /* ── Validation errors list ── */
        .alert-error {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.35);
            color: #f09090;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.82rem;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .alert-error ul { padding-left: 16px; }
        .alert-error li { margin-bottom: 2px; }

        /* ── Two-column row ── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        /* ── Single field ── */
        .form-group { margin-bottom: 12px; }

        /* ── All inputs and selects ── */
        .form-group input,
        .form-group select,
        .form-row .form-group input,
        .form-row .form-group select {
            width: 100%;
            background: #e8e6f0;
            border: 1.5px solid transparent;
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            color: #1a1a2e;
            outline: none;
            appearance: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .form-group input::placeholder { color: #8884a8; }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #a8a4e0;
            background: #f0eeff;
        }

        /* Select arrow icon */
        .select-wrapper {
            position: relative;
        }

        .select-wrapper select { padding-right: 38px; cursor: pointer; }

        .select-wrapper::after {
            content: '';
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #8884a8;
        }

        /* Select placeholder option color */
        .form-group select option[value=""] { color: #8884a8; }
        .form-group select option           { color: #1a1a2e; }

        /* Password wrapper */
        .password-wrapper { position: relative; }

        .password-wrapper input { padding-right: 44px; }

        .toggle-password {
            position: absolute;
            right: 12px;
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
        .toggle-password svg { width: 19px; height: 19px; }

        /* ── Field-level error hint ── */
        .field-error {
            font-size: 0.75rem;
            color: #f09090;
            margin-top: 4px;
            padding-left: 2px;
        }

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
            margin-top: 6px;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-submit:hover { background: #bbb8f0; transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }

        /* ── Bottom link strip ── */
        .bottom-link-wrap {
            position: relative;
            z-index: 1;
            background: rgba(30, 30, 46, 0.72);
            border-radius: 0 0 16px 16px;
            padding: 14px 36px;
            text-align: center;
            max-width: 480px;
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

        /* ── Outer wrapper ── */
        .auth-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 480px;
            padding: 20px 16px;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">

    <div class="card">

        {{-- Logo --}}
        <div class="logo">
            <img src="/images/axiom-logo-trans.png" alt="AXIOM Logo" style="height: 48px; width: auto;">
            <span class="logo-text">AXIOM</span>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            {{-- Row 1: First Name + Last Name --}}
            <div class="form-row">
                <div class="form-group">
                    <input
                        type="text"
                        name="first_name"
                        placeholder="First Name"
                        value="{{ old('first_name') }}"
                        autocomplete="given-name"
                        required
                    >
                    @error('first_name')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <input
                        type="text"
                        name="last_name"
                        placeholder="Last Name"
                        value="{{ old('last_name') }}"
                        autocomplete="family-name"
                        required
                    >
                    @error('last_name')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Row 2: Department + Role --}}
            <div class="form-row">
                <div class="form-group">
                    <div class="select-wrapper">
                        <select name="department" required>
                            <option value="" disabled {{ old('department') ? '' : 'selected' }}>Department</option>
                            @foreach($departments as $dept)
                                <option
                                    value="{{ $dept->department_id }}"
                                    {{ old('department') == $dept->department_id ? 'selected' : '' }}
                                >
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('department')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="select-wrapper">
                        <select name="role" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Role</option>
                            @foreach($roles as $role)
                                <option
                                    value="{{ $role->role_id }}"
                                    {{ old('role') == $role->role_id ? 'selected' : '' }}
                                >
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="form-group">
                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                >
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Row 3: Password + Confirm Password --}}
            <div class="form-row">
                <div class="form-group">
                    <div class="password-wrapper">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Password"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="toggle-password"
                                onclick="togglePassword('password', 'icon-password')"
                                aria-label="Toggle password visibility">
                            <svg id="icon-password" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
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
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Confirm Password"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="toggle-password"
                                onclick="togglePassword('password_confirmation', 'icon-confirm')"
                                aria-label="Toggle confirm password visibility">
                            <svg id="icon-confirm" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
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
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">REGISTER</button>

        </form>
    </div>

    {{-- Bottom strip --}}
    <div class="bottom-link-wrap">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
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

    const eyeOn = `<path stroke-linecap="round" stroke-linejoin="round"
        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M2.458 12C3.732 7.943 7.523 5 12 5
           c4.478 0 8.268 2.943 9.542 7
           -1.274 4.057-5.064 7-9.542 7
           -4.477 0-8.268-2.943-9.542-7z"/>`;

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type     = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden ? eyeOn : eyeOff;
    }
</script>

</body>
</html>