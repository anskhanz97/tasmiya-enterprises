<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071b35">
    <title>Sign in - Tasmiya Enterprises</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { color-scheme: light; --navy: #071b35; --deep: #041328; --blue: #155aa5; --ink: #142840; --muted: #66768b; --line: #dce4eb; }
        *, *::before, *::after { box-sizing: border-box; }
        html { min-height: 100%; }
        body { min-height: 100vh; margin: 0; color: var(--ink); background: #fbfcfd; font-family: 'DM Sans', 'Segoe UI', sans-serif; }
        button, input { font: inherit; }
        a { color: inherit; }
        .login-shell { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.12fr) minmax(440px, .88fr); }

        .brand-stage {
            position: relative; min-height: 740px; overflow: hidden; isolation: isolate;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: clamp(32px, 4.3vw, 70px); color: #fff;
            background: radial-gradient(circle at 75% 46%, rgba(23,109,165,.55), transparent 34%),
                radial-gradient(circle at 17% 79%, rgba(12,83,139,.62), transparent 39%),
                linear-gradient(138deg, #092b4d 0%, var(--navy) 48%, var(--deep) 100%);
        }
        .brand-stage::before { content: ''; position: absolute; inset: 0; z-index: -1; opacity: .3; background-image: linear-gradient(rgba(185,232,242,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(185,232,242,.12) 1px, transparent 1px); background-size: 70px 70px; mask-image: linear-gradient(to bottom, transparent 4%, #000 48%, transparent 100%); }
        .brand-stage::after { content: ''; position: absolute; width: 920px; height: 920px; border: 1px solid rgba(159,231,232,.1); border-radius: 50%; top: 12%; left: 12%; z-index: -1; box-shadow: 0 0 0 115px rgba(159,231,232,.018), 0 0 0 230px rgba(159,231,232,.014); }
        .brand-mark { display: inline-flex; align-items: center; gap: 15px; text-decoration: none; width: fit-content; position: relative; z-index: 2; }
        .brand-monogram { width: 48px; height: 48px; display: grid; place-items: center; border: 1px solid rgba(189,239,239,.65); border-radius: 14px; background: linear-gradient(145deg, rgba(193,250,245,.18), rgba(62,164,204,.08)); box-shadow: inset 0 1px rgba(255,255,255,.24), 0 12px 30px rgba(0,0,0,.12); font: 700 19px/1 'Outfit', sans-serif; letter-spacing: -.08em; }
        .brand-name { font: 600 18px/1.08 'Outfit', sans-serif; letter-spacing: -.035em; }
        .brand-name span { display: block; font-weight: 400; letter-spacing: .13em; font-size: 10px; margin-top: 4px; text-transform: uppercase; color: #b7d4e4; }
        .stage-copy { position: relative; z-index: 2; max-width: 600px; margin-top: auto; }
        .stage-copy h1 { margin: 0; max-width: 10ch; font: 500 clamp(55px, 6.25vw, 104px)/.96 'Outfit', sans-serif; letter-spacing: -.07em; text-wrap: balance; }
        .stage-copy p { color: #bcd2e0; max-width: 390px; margin: 28px 0 0; font-size: 16px; line-height: 1.7; }
        .stage-footer { display: flex; gap: 12px; align-items: center; position: relative; z-index: 2; color: #b5d3df; font-size: 12px; letter-spacing: .025em; margin-top: 58px; }
        .stage-footer span + span::before { content: '/'; display: inline-block; margin-right: 12px; color: #61adaf; }

        .artwork { position: absolute; width: min(65vw, 800px); height: min(65vw, 800px); top: 46%; left: 63%; transform: translate(-50%, -50%); pointer-events: none; z-index: 1; perspective: 900px; }
        .artwork-glow { position: absolute; inset: 24%; background: #74d9d4; border-radius: 50%; filter: blur(110px); opacity: .28; animation: glow 9s ease-in-out infinite alternate; }
        .orbit { position: absolute; border: 1px solid rgba(164,237,232,.27); border-radius: 50%; transform-style: preserve-3d; }
        .orbit-one { inset: 12%; transform: rotate(-24deg) rotateX(68deg); animation: orbit-one 28s linear infinite; }
        .orbit-two { inset: 24%; border-color: rgba(191,247,247,.18); transform: rotate(44deg) rotateX(66deg); animation: orbit-two 24s linear infinite; }
        .orbit-one::before, .orbit-two::before { content: ''; display: block; position: absolute; top: 15%; left: 14%; width: 8px; height: 8px; border-radius: 50%; background: #baf7ed; box-shadow: 0 0 22px 8px rgba(122,234,224,.48); }
        .glass-form { position: absolute; inset: 23%; transform-style: preserve-3d; animation: float-form 10s ease-in-out infinite alternate; }
        .glass-face { position: absolute; inset: 0; border: 1px solid rgba(206,255,251,.58); border-radius: 30%; background: linear-gradient(145deg, rgba(174,250,247,.36) 4%, rgba(83,186,211,.1) 44%, rgba(6,45,88,.32) 90%); box-shadow: inset 14px 18px 36px rgba(220,255,252,.2), inset -25px -28px 36px rgba(5,47,85,.38), 0 35px 90px rgba(0,12,36,.35); backdrop-filter: blur(4px); }
        .face-back { transform: translate(15%, -16%) scale(.91); opacity: .42; }
        .face-middle { transform: translate(7%, -8%) scale(.96); opacity: .66; }
        .face-front { transform: rotate(-22deg); }
        .face-front::after { content: ''; position: absolute; inset: 19%; border-radius: 28%; border: 1px solid rgba(222,255,251,.43); transform: rotate(45deg); background: linear-gradient(130deg, rgba(224,255,252,.18), transparent 68%); }
        .artwork-line { position: absolute; left: -15%; right: -15%; top: 54%; height: 1px; background: linear-gradient(90deg, transparent, rgba(165,239,234,.5), transparent); transform: rotate(-33deg); }
        .artwork-line.second { top: 42%; transform: rotate(36deg); opacity: .48; }

        .login-panel { display: flex; align-items: center; justify-content: center; min-width: 0; padding: 48px clamp(32px, 6vw, 92px); background: #fbfcfd; }
        .login-content { width: min(100%, 430px); }
        .return-link { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: #64768b; font-size: 13px; font-weight: 600; margin-bottom: clamp(50px, 9vh, 110px); }
        .return-link svg { width: 16px; height: 16px; transition: transform .2s ease; }
        .return-link:hover { color: var(--blue); }
        .return-link:hover svg { transform: translateX(-3px); }
        .login-heading h2 { margin: 0; color: #0c2542; font: 600 clamp(40px, 3.4vw, 52px)/1.04 'Outfit', sans-serif; letter-spacing: -.055em; }
        .login-heading p { margin: 15px 0 0; font-size: 15px; line-height: 1.6; color: var(--muted); }
        .login-form { margin-top: 40px; }
        .form-field { margin-bottom: 22px; }
        .field-label { display: block; margin: 0 0 9px; color: #18344e; font-size: 13px; font-weight: 700; }
        .input-wrap { position: relative; }
        .field-input { display: block; width: 100%; height: 54px; padding: 0 16px; border: 1px solid var(--line); border-radius: 11px; color: var(--ink); background: #fff; box-shadow: 0 2px 5px rgba(18,46,71,.025); outline: none; transition: border-color .2s ease, box-shadow .2s ease; }
        .field-input::placeholder { color: #9aa9b7; }
        .field-input:hover { border-color: #b7cad8; }
        .field-input:focus { border-color: #2372b5; box-shadow: 0 0 0 4px rgba(35,114,181,.11); }
        .field-input[aria-invalid="true"] { border-color: #c44d59; }
        .password-input { padding-right: 59px; }
        .password-toggle { position: absolute; top: 50%; right: 7px; width: 42px; height: 42px; display: grid; place-items: center; transform: translateY(-50%); border: 0; border-radius: 8px; background: transparent; color: #73869a; cursor: pointer; }
        .password-toggle:hover { background: #eef4f8; color: var(--blue); }
        .password-toggle svg { width: 19px; height: 19px; }
        .password-toggle .eye-off, .password-toggle.is-visible .eye-on { display: none; }
        .password-toggle.is-visible .eye-off { display: block; }
        .field-error { margin: 7px 0 0; color: #a83744; font-size: 12px; line-height: 1.45; }
        .form-options { display: flex; align-items: center; gap: 12px; margin: 6px 0 28px; }
        .remember { display: inline-flex; align-items: center; gap: 10px; color: #52667b; font-size: 13px; cursor: pointer; }
        .remember input { width: 17px; height: 17px; margin: 0; accent-color: var(--blue); cursor: pointer; }
        .login-button { width: 100%; min-height: 56px; padding: 14px 18px; display: flex; align-items: center; justify-content: center; gap: 12px; border: 0; border-radius: 11px; background: #0c3765; color: #fff; font: 600 15px 'DM Sans', sans-serif; cursor: pointer; box-shadow: 0 12px 26px rgba(8,55,102,.16); transition: background .2s ease, transform .2s ease, box-shadow .2s ease; }
        .login-button:hover { background: #14558e; transform: translateY(-2px); box-shadow: 0 15px 30px rgba(8,55,102,.22); }
        .login-button svg { width: 18px; height: 18px; }
        .login-button:active { transform: translateY(0); }
        .login-button:disabled { cursor: wait; transform: none; background: #24517b; }
        .login-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,.42); border-top-color: #fff; border-radius: 50%; }
        .login-button.is-submitting .login-spinner { display: block; animation: button-spin .8s linear infinite; }
        .login-button.is-submitting svg { display: none; }
        .notice { margin: 26px 0 0; padding: 15px 16px; border-radius: 10px; font-size: 13px; line-height: 1.55; }
        .notice-error { background: #fff0f1; color: #96313f; border: 1px solid #f6d0d5; }
        .notice-success { background: #e9f7f2; color: #205b49; border: 1px solid #c8e8db; }
        .access-note { display: flex; align-items: flex-start; gap: 11px; padding-top: 27px; margin-top: 36px; border-top: 1px solid #e7edf2; color: #77899a; font-size: 12px; line-height: 1.6; text-decoration: none; }
        .access-note:hover { color: #155aa5; }
        .access-note svg { flex: none; width: 18px; height: 18px; margin-top: 1px; color: #6f91a6; }
        .access-note strong { color: #3e596f; font-weight: 700; }
        :is(a, button, input):focus-visible { outline: 3px solid rgba(25,113,179,.45); outline-offset: 3px; }
        @keyframes float-form { from { transform: translate3d(-12px,18px,0) rotate(4deg) rotateY(-12deg); } to { transform: translate3d(16px,-17px,0) rotate(-5deg) rotateY(15deg); } }
        @keyframes orbit-one { to { transform: rotate(336deg) rotateX(68deg); } }
        @keyframes orbit-two { to { transform: rotate(-316deg) rotateX(66deg); } }
        @keyframes glow { to { opacity: .4; transform: scale(1.2); } }
        @keyframes button-spin { to { transform: rotate(360deg); } }
        @media (max-width: 1050px) {
            .login-shell { grid-template-columns: minmax(0, 1fr) minmax(400px, 1fr); }
            .brand-stage { padding: 36px; }
            .stage-copy h1 { font-size: clamp(54px, 6vw, 76px); }
            .artwork { width: 680px; height: 680px; left: 65%; }
            .login-panel { padding: 40px; }
        }
        @media (max-width: 760px) {
            .login-shell { display: block; }
            .brand-stage { min-height: 335px; padding: 27px 26px; }
            .brand-monogram { width: 42px; height: 42px; font-size: 17px; }
            .brand-name { font-size: 16px; }
            .stage-copy { margin-top: 55px; }
            .stage-copy h1 { max-width: 11ch; font-size: clamp(39px, 9vw, 61px); }
            .stage-copy p, .stage-footer { display: none; }
            .artwork { width: 460px; height: 460px; left: 88%; top: 63%; opacity: .55; }
            .login-panel { padding: 36px 26px 55px; }
            .login-content { max-width: 520px; }
            .return-link { margin-bottom: 42px; }
            .login-heading h2 { font-size: 42px; }
        }
        @media (max-width: 420px) {
            .brand-stage { min-height: 300px; }
            .stage-copy h1 { font-size: 39px; }
            .login-panel { padding-inline: 22px; }
            .login-heading h2 { font-size: 38px; }
            .login-form { margin-top: 32px; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="brand-stage" aria-label="Tasmiya Enterprises">
            <a class="brand-mark" href="{{ url('/') }}" aria-label="Tasmiya Enterprises home">
                <span class="brand-monogram" aria-hidden="true">TE</span>
                <span class="brand-name">Tasmiya<span>Enterprises</span></span>
            </a>
            <div class="artwork" aria-hidden="true">
                <div class="artwork-glow"></div><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div>
                <div class="artwork-line"></div><div class="artwork-line second"></div>
                <div class="glass-form"><div class="glass-face face-back"></div><div class="glass-face face-middle"></div><div class="glass-face face-front"></div></div>
            </div>
            <div class="stage-copy">
                <h1>Working better, together.</h1>
                <p>One connected space for the people and work behind every Tasmiya division.</p>
            </div>
            <div class="stage-footer" aria-label="Our divisions"><span>Taxation</span><span>Digital services</span><span>Technical support</span></div>
        </section>

        <section class="login-panel" aria-labelledby="login-title">
            <div class="login-content">
                <a class="return-link" href="{{ url('/') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m14 5-7 7 7 7"/><path d="M7 12h13"/></svg>Back to website
                </a>
                <header class="login-heading"><h2 id="login-title">Welcome back.</h2><p>Sign in to continue to your Tasmiya workspace.</p></header>
                @if (session('status')) <div class="notice notice-success" role="status">{{ session('status') }}</div> @endif
                @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
                    <div class="notice notice-error" role="alert">{{ $errors->first() }}</div>
                @endif
                <form class="login-form" action="{{ route('login.store') }}" method="POST">
                    @csrf
                    <div class="form-field">
                        <label class="field-label" for="email">Email address</label>
                        <div class="input-wrap"><input class="field-input" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" autocomplete="username" inputmode="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @error('email') aria-describedby="email-error" @enderror required autofocus></div>
                        @error('email') <p class="field-error" id="email-error" role="alert">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-field">
                        <label class="field-label" for="password">Password</label>
                        <div class="input-wrap">
                            <input class="field-input password-input" type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" @error('password') aria-describedby="password-error" @enderror required>
                            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" title="Show password">
                                <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3 21 21"/><path d="M10.6 6.1A11 11 0 0 1 12 6c6.5 0 10 6 10 6a15.6 15.6 0 0 1-3.1 3.6M6.4 6.4C3.6 8.1 2 12 2 12s3.5 6 10 6a10.9 10.9 0 0 0 4.1-.8"/><path d="M10 10a2.8 2.8 0 0 0 4 4"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="field-error" id="password-error" role="alert">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-options"><label class="remember" for="remember"><input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>Keep me signed in</label></div>
                    <button class="login-button" type="submit"><span class="login-button-text">Sign in</span><span class="login-spinner" aria-hidden="true"></span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></button>
                </form>
                <a class="access-note" href="https://wa.me/923051852884?text=Hello%2C%20I%27d%20like%20to%20request%20access%20to%20the%20Tasmiya%20Enterprises%20workspace." target="_blank" rel="noopener noreferrer" aria-label="Request a login on WhatsApp">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4.5" y="10" width="15" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    <span><strong>Team access only.</strong> Need an account? Request a login on WhatsApp.</span>
                </a>
            </div>
        </section>
    </main>
    <script>
        const passwordToggle = document.querySelector('.password-toggle');
        const passwordInput = document.getElementById('password');
        passwordToggle.addEventListener('click', () => {
            const isVisible = passwordInput.type === 'password';
            passwordInput.type = isVisible ? 'text' : 'password';
            passwordToggle.classList.toggle('is-visible', isVisible);
            passwordToggle.setAttribute('aria-pressed', String(isVisible));
            passwordToggle.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
            passwordToggle.title = isVisible ? 'Hide password' : 'Show password';
        });
        const loginForm = document.querySelector('.login-form');
        const loginButton = loginForm.querySelector('.login-button');
        loginForm.addEventListener('submit', () => {
            loginForm.setAttribute('aria-busy', 'true');
            loginButton.disabled = true;
            loginButton.classList.add('is-submitting');
            loginButton.querySelector('.login-button-text').textContent = 'Signing in…';
        });
        window.addEventListener('pageshow', () => {
            loginForm.removeAttribute('aria-busy');
            loginButton.disabled = false;
            loginButton.classList.remove('is-submitting');
            loginButton.querySelector('.login-button-text').textContent = 'Sign in';
        });
    </script>
</body>
</html>
