<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Tasmiya Enterprises</title>

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Modern Styles --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #667eea;
            --primary-dark: #5568d3;
            --secondary: #764ba2;
            --accent: #f093fb;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.15);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            color: #333;
            overflow-x: hidden;
            padding-top: 70px;
        }

        /* ============================================
           NAVBAR - Glassmorphism Effect
           ============================================ */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-lg);
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .navbar-brand {
            font-size: 26px;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-brand span {
            font-size: 28px;
            filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.4));
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 30px;
            list-style: none;
        }

        .nav-mobile-trigger,
        .nav-mobile-menu { display: none; }

        .nav-mobile-trigger {
            border: 1px solid #d7d4ef;
            border-radius: 10px;
            background: #fff;
            color: #5145a3;
            width: 40px;
            height: 40px;
            font-size: 22px;
            cursor: pointer;
        }

        .nav-mobile-menu {
            position: absolute;
            top: 70px;
            left: 20px;
            right: 20px;
            padding: 8px;
            border: 1px solid #dddaf0;
            border-radius: 15px;
            background: #fff;
            box-shadow: var(--shadow-lg);
        }

        .nav-mobile-menu a {
            display: block;
            padding: 10px 13px;
            border-radius: 9px;
            color: #28375b;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .nav-mobile-menu a:hover,
        .nav-mobile-menu a:focus-visible {
            background: #efedfb;
            color: #5045a1;
            outline: none;
        }

        .navbar-nav a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            position: relative;
            transition: color 0.3s ease;
        }

        .navbar-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: width 0.3s ease;
        }

        .navbar-nav a:hover {
            color: var(--primary);
        }

        .navbar-nav a:hover::after {
            width: 100%;
        }

        .user-menu {
            position: relative;
        }

        .user-menu-trigger {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .user-menu-trigger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #dcdcf1;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            min-width: 220px;
            padding: 6px;
            margin-top: 10px;
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
            overflow: hidden;
        }

        .user-dropdown.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .user-dropdown a,
        .user-dropdown form button {
            display: block;
            width: 100%;
            padding: 12px 16px;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
            border-radius: 10px;
        }

        .user-dropdown a:hover,
        .user-dropdown form button:hover,
        .user-dropdown a:focus-visible,
        .user-dropdown form button:focus-visible {
            background: #efedfb;
            color: #5045a1;
            outline: none;
        }

        .btn-login {
            background: var(--gradient-primary);
            color: white;
            padding: 10px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: inline-block;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* ============================================
           MAIN CONTENT AREA
           ============================================ */
        .main-content {
            min-height: calc(100vh - 140px);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            overflow: hidden;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        /* ============================================
           FOOTER
           ============================================ */
        footer {
            background-color: var(--dark);
            background-image: 
                radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.05) 2%, transparent 0%), 
                radial-gradient(circle at 75px 75px, rgba(255, 255, 255, 0.05) 2%, transparent 0%);
            background-size: 100px 100px;
            color: white;
            position: relative;
            overflow: hidden;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 40px 30px;
        }

        .footer-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .footer-brand-section {
            flex: 2;
            min-width: 250px;
        }

        .footer-brand {
            font-size: 26px;
            font-weight: 800;
            background: var(--gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        .footer-brand-section p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            line-height: 1.7;
            max-width: 400px;
        }

        .footer-links-section {
            flex: 3;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 30px;
        }

        .footer-section h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 18px;
            color: white;
            letter-spacing: 0.5px;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 12px;
        }

        .footer-section ul li a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-block; /* Changed from inline-flex */
            position: relative;
            padding-bottom: 4px; /* Space for the underline */
        }

        .footer-section ul li a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1.5px;
            display: block;
            margin-top: 2px;
            right: 0;
            background: var(--accent);
            transition: width 0.3s ease;
            -webkit-transition: width 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--accent);
        }

        .footer-section ul li a:hover::after {
            width: 100%;
            left: 0;
            background: var(--accent);
        }

        .footer-divider {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            height: 1px;
            margin: 20px 0;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            padding-top: 20px;
        }

        .footer-copyright {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            order: 2;
        }

        .footer-socials {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            order: 1;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .social-link svg {
            width: 22px;
            height: 22px;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.4s ease;
        }

        .social-link:hover {
            transform: translateY(-6px) scale(1.1);
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--accent);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3), 0 0 15px var(--accent);
        }
        
        .social-link:hover svg {
            color: white;
            transform: rotate(360deg) scale(1.2);
        }

        @media (max-width: 768px) {
            .footer-main {
                flex-direction: column;
            }
            .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            .footer-socials {
                order: 1;
                justify-content: center;
            }
            .footer-copyright {
                order: 2;
            }
            .scroll-to-top {
                bottom: 20px;
                right: 20px;
            }
        }

        /* ============================================
           SCROLL TO TOP BUTTON
           ============================================ */
        .scroll-to-top {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 26px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px) scale(0.8);
            z-index: 999;
            border: 3px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
            }
            50% {
                box-shadow: 0 8px 35px rgba(102, 126, 234, 0.55), 0 0 0 8px rgba(102, 126, 234, 0.1);
            }
        }

        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .scroll-to-top:hover {
            transform: translateY(-8px) scale(1.1) rotate(5deg);
            box-shadow: 0 12px 40px rgba(102, 126, 234, 0.6);
            border-color: rgba(255, 255, 255, 0.4);
            animation: none;
        }

        .scroll-to-top:active {
            transform: translateY(-5px) scale(1.05) rotate(0deg);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
        }

        .scroll-to-top::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .scroll-to-top:hover::before {
            opacity: 1;
        }

        @media (max-width: 640px) {
            .scroll-to-top {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 22px;
            }
        }

        /* ============================================
           CARD COMPONENTS
           ============================================ */
        .modern-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .modern-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            padding: 12px 32px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: var(--gradient-secondary);
            color: white;
            padding: 12px 32px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
        }

        @media (max-width: 768px) {
            .navbar-container {
                padding: 0 20px;
            }
            .nav-mobile-trigger { display: block; margin-left: auto; }
            .nav-mobile-menu.show { display: block; }
            .navbar-nav { margin-left: 10px; }
            .navbar-nav > a { display: none; }
            .footer-container {
                padding: 40px 20px 20px;
            }
            .footer-top {
                flex-direction: column;
                align-items: flex-start;
            }
            .footer-newsletter {
                width: 100%;
            }
            .footer-bottom {
                flex-direction: column;
            }
        }

        @media (max-width: 420px) {
            .navbar-brand { font-size: 21px; }
            .user-menu-trigger > span { display: none; }
            .user-dropdown { min-width: 200px; }
        }
    </style>
</head>
<body>
    {{-- Navigation Bar --}}
    <nav class="navbar" id="navbar">
        <div class="navbar-container">
            <a href="{{ route('home') }}" class="navbar-brand">
                <span>🏢</span> Tasmiya
            </a>

            <button type="button" class="nav-mobile-trigger" aria-label="Open site navigation" aria-controls="mobileNav" aria-expanded="false">☰</button>
            <div class="nav-mobile-menu" id="mobileNav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('services.index') }}">Services</a>
                <a href="{{ route('team.index') }}">Team</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact.create') }}">Contact</a>
            </div>

            <div class="navbar-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('services.index') }}">Services</a>
                <a href="{{ route('team.index') }}">Team</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact.create') }}">Contact</a>
                
                {{-- User Menu --}}
                @auth
                    <div class="user-menu">
                        <button type="button" class="user-menu-trigger" aria-controls="userDropdown" aria-expanded="false" aria-label="Open account menu">
                            <div class="user-avatar">{{ auth()->user()->initials }}</div>
                            <span>{{ auth()->user()->name }}</span>
                        </button>

                        <div class="user-dropdown" id="userDropdown">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                            <a href="{{ route('payments.index') }}">💳 Payments</a>
                            <a href="{{ route('contact.create') }}">📧 Contact</a>
                            @if(auth()->user()->profile)
                                <a href="{{ route('profiles.services.index', auth()->user()->profile) }}">💼 Add / Manage Services</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit">Sign Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="main-content">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    {{-- Scroll to Top Button --}}
    <button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
        ↑
    </button>

    {{-- Footer --}}
    <footer>
        <div class="footer-container">
            <div class="footer-main">
                {{-- Company Info --}}
                <div class="footer-brand-section">
                    <div class="footer-brand">Tasmiya Enterprises</div>
                    <p>
                        Transforming businesses through innovative solutions and professional expertise. We are dedicated to delivering excellence and driving success for our clients worldwide.
                    </p>
                </div>

                {{-- Links Grid --}}
                <div class="footer-links-section">
                    <div class="footer-section">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('services.index') }}">Services</a></li>
                            <li><a href="{{ route('team.index') }}">Team</a></li>
                            <li><a href="{{ route('about') }}">About Us</a></li>
                            <li><a href="{{ route('contact.create') }}">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="footer-section">
                        <h4>Support</h4>
                        <ul>
                            <li><a href="{{ route('faq') }}">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="footer-section">
                        <h4>Legal</h4>
                        <ul>
                            <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                            <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Footer Bottom --}}
            <div class="footer-divider"></div>
            <div class="footer-bottom">
                <div class="footer-copyright">&copy; {{ date('Y') }} Tasmiya Enterprises. All rights reserved.</div>
                
                <div class="footer-socials">
                    @php
                        $socialLinks = \App\Models\SiteSetting::getSocialLinks();
                    @endphp
                    
                    @foreach($socialLinks as $socialLink)
                        <a href="{{ $socialLink['url'] }}" class="social-link" title="{{ $socialLink['label'] }}" aria-label="{{ $socialLink['label'] }}" target="_blank" rel="noopener noreferrer" style="width:auto;min-width:42px;padding:0 10px;font-size:12px;font-weight:700;text-decoration:none">{{ $socialLink['label'] }} ↗</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
