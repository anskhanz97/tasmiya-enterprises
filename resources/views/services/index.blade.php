@extends('layouts.app')

@section('title', 'Our Services')

@section('content')
<style>
    /* Hero Section */
    .services-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        background-size: 200% 200%;
        position: relative;
        overflow: hidden;
        padding: 120px 20px 140px;
        color: white;
    }
    
    @keyframes heroGradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .services-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
    }
    
    .services-hero::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-image: 
            url('data:image/svg+xml,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="white" opacity="0.2"/></svg>');
        background-size: 50px 50px;
        pointer-events: none;
    }
    
    @keyframes sparkle {
        0% { transform: translateY(0); opacity: 1; }
        100% { transform: translateY(-50px); opacity: 0.5; }
    }
    
    @keyframes floatPattern {
        0% { transform: translateY(0) translateX(0); }
        50% { transform: translateY(-20px) translateX(10px); }
        100% { transform: translateY(0) translateX(0); }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    
    .services-hero-content {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
        animation: fadeInUp 0.8s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .services-hero h1 {
        font-size: clamp(2.8rem, 6vw, 4.5rem);
        font-weight: 900;
        margin-bottom: 1.5rem;
        letter-spacing: -2px;
        background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #ddd6fe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: none;
        animation: textShine 3s ease-in-out infinite;
        position: relative;
    }
    
    @keyframes textShine {
        0%, 100% { filter: brightness(1); }
        50% { filter: brightness(1.2); }
    }
    
    .services-hero p {
        font-size: 1.25rem;
        opacity: 0.95;
        line-height: 1.8;
        max-width: 750px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        font-weight: 300;
    }

    .services-hero-art { position: relative; width: 245px; height: 205px; margin-left: auto; }
    .services-hero-art::before { content: ''; position: absolute; inset: 12px 18px; border: 1px solid rgba(255,255,255,.42); border-radius: 24px; transform: rotate(-9deg); }
    .services-hero-art__tile { position: absolute; display: grid; place-items: center; width: 108px; height: 108px; border: 1px solid rgba(255,255,255,.56); border-radius: 22px; background: rgba(255,255,255,.18); backdrop-filter: blur(8px); box-shadow: 0 15px 36px rgba(31,28,96,.15); }
    .services-hero-art__tile .work-icon { width: 76px; height: 76px; }
    .services-hero-art__tile--tax { left: 0; top: 0; transform: rotate(-8deg); }
    .services-hero-art__tile--code { right: 0; top: 34px; transform: rotate(7deg); }
    .services-hero-art__tile--support { left: 48px; bottom: 0; transform: rotate(3deg); }
    
    /* Main Content */
    .services-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 100px 20px;
    }
    
    /* Division Section */
    .division-section {
        margin-bottom: 100px;
        padding: 40px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.25s ease;
    }
    
    .division-section:hover {
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    }
    
    .division-header {
        margin-bottom: 50px;
        position: relative;
    }
    
    .division-title-wrapper {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 15px;
    }
    
    .division-accent {
        width: 8px;
        height: 60px;
        border-radius: 4px;
        background: linear-gradient(to bottom, var(--division-primary), var(--division-secondary));
        box-shadow: 0 4px 20px var(--division-primary)60, 0 0 30px var(--division-primary)40;
        position: relative;
        overflow: hidden;
    }
    
    .division-accent::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.5), transparent);
    }
    
    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    .division-title {
        font-size: 2.8rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -2px;
        position: relative;
        display: inline-block;
    }
    
    .division-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60%;
        height: 4px;
        background: linear-gradient(90deg, var(--division-primary), transparent);
        border-radius: 2px;
        opacity: 0.5;
    }
    
    .division-description {
        font-size: 1.1rem;
        color: #64748b;
        line-height: 1.7;
        margin-left: 26px;
    }
    
    /* Service Grid */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 35px;
    }
    
    /* Stats Section */
    .stats-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        background-size: 200% 200%;
        padding: 80px 20px;
        margin-top: 60px;
        border-radius: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    }
    
    @keyframes statsGradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="1.5" fill="%23667eea" opacity="0.1"/></svg>');
        opacity: 0.5;
    }
    
    .stats-content {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .stat-box {
        text-align: center;
        padding: 40px 30px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    .stat-box:hover {
        transform: translateY(-15px) scale(1.03);
        box-shadow: 0 25px 60px rgba(102, 126, 234, 0.25);
        border-color: rgba(255, 255, 255, 0.8);
    }
    
    .stat-box:hover::before {
        opacity: 1;
    }
    
    .stat-number {
        font-size: 4rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
        display: block;
        line-height: 1;
        position: relative;
        z-index: 2;
    }
    
    .stat-label {
        font-size: 1.05rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        z-index: 2;
    }
    
    .divider {
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3), transparent);
        margin: 80px 0;
        position: relative;
        border-radius: 2px;
    }
    
    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        opacity: 0.1;
        filter: blur(20px);
    }
    
    /* Service cards render immediately so the catalogue stays responsive. */
    
    @media (max-width: 768px) {
        .services-hero {
            padding: 60px 20px 80px;
        }
        
        .services-hero h1 {
            font-size: 2.5rem;
        }
        
        .services-container {
            padding: 60px 20px;
        }
        
        .division-section {
            margin-bottom: 60px;
            padding: 25px;
        }
        
        .division-title {
            font-size: 2rem;
        }
        
        .services-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 3rem;
        }
        
        .cta-card {
            padding: 35px 25px !important;
        }
        
        .cta-card h3 {
            font-size: 1.6rem !important;
        }
        
        .whatsapp-btn {
            width: 100% !important;
            justify-content: center !important;
        }
    }
    
    /* WhatsApp Button Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .whatsapp-btn:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4) !important;
    }
    
    .whatsapp-btn:hover .btn-shine {
        animation: btnShine 0.8s ease;
    }
    
    .whatsapp-btn-secondary:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.2) !important;
    }
    
    @keyframes btnShine {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    /* Scroll Indicator */
    .scroll-indicator {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        animation: bounce 2s infinite;
        opacity: 0.7;
        cursor: pointer;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
        40% { transform: translateX(-50%) translateY(-15px); }
        60% { transform: translateX(-50%) translateY(-8px); }
    }
    
    .scroll-indicator svg {
        filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
    }
</style>

<div class="services-hero">
    <div class="services-hero-content">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1>Our Professional Services</h1>
                <p class="mb-0">
                    Comprehensive solutions across taxation, IT, and technical support. 
                    All delivered by our team of certified experts dedicated to your success.
                </p>
            </div>
            <div class="col-lg-4 text-end d-none d-lg-block">
                <div class="services-hero-art" aria-hidden="true">
                    <span class="services-hero-art__tile services-hero-art__tile--tax"><x-work-icon type="tax" /></span>
                    <span class="services-hero-art__tile services-hero-art__tile--code"><x-work-icon type="code" /></span>
                    <span class="services-hero-art__tile services-hero-art__tile--support"><x-work-icon type="support" /></span>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
            </svg>
        </div>
    </div>
</div>

<div class="services-container">
    {{-- Services by Division --}}
    @forelse($servicesByDivision as $divisionId => $services)
        @php
            $division = $services->first()->service->division;
            $themeColors = $division->getThemeColors();
        @endphp
        
        <div class="division-section" id="division-{{ $division->slug }}" style="--division-primary: {{ $themeColors['primary'] }}; --division-secondary: {{ $themeColors['secondary'] }};">
            <div class="division-header">
                <div class="division-title-wrapper">
                    <div class="division-accent"></div>
                    <h2 class="division-title" style="color: {{ $themeColors['primary'] }};">
                        {{ $division->name }}
                    </h2>
                </div>
                <p class="division-description">{{ $division->description }}</p>
            </div>

            <div class="services-grid">
                @foreach($services as $offering)
                    <x-service-card :offering="$offering" />
                @endforeach
            </div>
            
            {{-- WhatsApp CTA Section --}}
            <div class="whatsapp-cta-section" style="margin-top: 60px; position: relative;">
                @if($division->name === 'FBR Taxation')
                    <div class="cta-card" style="background: linear-gradient(135deg, {{ $themeColors['primary'] }}15 0%, {{ $themeColors['secondary'] }}15 100%); border: 2px solid {{ $themeColors['primary'] }}40; border-radius: 24px; padding: 50px; position: relative; overflow: hidden;">
                        <!-- Animated Background -->
                        <div style="position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: {{ $themeColors['gradient'] }}; border-radius: 50%; opacity: 0.08; filter: blur(60px); animation: float 6s ease-in-out infinite;"></div>
                        
                        <div class="row align-items-center" style="position: relative; z-index: 2;">
                            <div class="col-lg-8">
                                <div style="display: inline-flex; align-items: center; gap: 12px; background: {{ $themeColors['primary'] }}20; padding: 12px 24px; border-radius: 50px; margin-bottom: 20px; border: 1px solid {{ $themeColors['primary'] }}30;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ $themeColors['primary'] }}">
                                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.23-8.24 8.23-1.48 0-2.93-.39-4.19-1.15l-.3-.17-3.12.82.83-3.04-.2-.32a8.188 8.188 0 01-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31-.22.25-.87.85-.87 2.07 0 1.22.89 2.39 1 2.56.14.17 1.76 2.67 4.25 3.73.59.27 1.05.42 1.41.53.59.19 1.13.16 1.56.1.48-.07 1.46-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.16-.48-.27-.25-.14-1.47-.74-1.69-.82-.23-.08-.37-.12-.56.12-.16.25-.64.81-.78.97-.15.17-.29.19-.53.07-.26-.13-1.06-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.11-.56-1.35-.77-1.84-.2-.48-.4-.42-.56-.43-.14 0-.3-.01-.46-.01z"/>
                                    </svg>
                                    <span style="font-weight: 800; font-size: 0.85rem; color: {{ $themeColors['primary'] }}; text-transform: uppercase; letter-spacing: 1px;">Direct Expert Consultation</span>
                                </div>
                                <h3 style="font-size: 2.2rem; font-weight: 900; color: #0f172a; margin-bottom: 12px; line-height: 1.2;">Ready to optimize your taxes?</h3>
                                <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 0; line-height: 1.6;">Get instant expert advice on FBR taxation, compliance, and financial planning from our certified tax consultant.</p>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                <a href="https://wa.me/923124246916" target="_blank" class="whatsapp-btn" style="display: inline-flex; align-items: center; gap: 12px; background: {{ $themeColors['gradient'] }}; color: white; padding: 20px 40px; border-radius: 16px; text-decoration: none; font-weight: 800; font-size: 1.1rem; box-shadow: 0 10px 30px {{ $themeColors['primary'] }}40; transition: all 0.3s ease; position: relative; overflow: hidden;">
                                    <span style="position: relative; z-index: 2;">Chat with Atif</span>
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white" style="position: relative; z-index: 2;">
                                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.23-8.24 8.23-1.48 0-2.93-.39-4.19-1.15l-.3-.17-3.12.82.83-3.04-.2-.32a8.188 8.188 0 01-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31-.22.25-.87.85-.87 2.07 0 1.22.89 2.39 1 2.56.14.17 1.76 2.67 4.25 3.73.59.27 1.05.42 1.41.53.59.19 1.13.16 1.56.1.48-.07 1.46-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.16-.48-.27-.25-.14-1.47-.74-1.69-.82-.23-.08-.37-.12-.56.12-.16.25-.64.81-.78.97-.15.17-.29.19-.53.07-.26-.13-1.06-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.11-.56-1.35-.77-1.84-.2-.48-.4-.42-.56-.43-.14 0-.3-.01-.46-.01z"/>
                                    </svg>
                                    <div class="btn-shine" style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);"></div>
                                </a>
                                <p style="margin-top: 12px; font-size: 0.9rem; color: #94a3b8; font-weight: 600;">Usually responds within minutes</p>
                            </div>
                        </div>
                    </div>
                @elseif($division->name === 'IT & Digital Services')
                    <div class="cta-card" style="background: linear-gradient(135deg, {{ $themeColors['primary'] }}15 0%, {{ $themeColors['secondary'] }}15 100%); border: 2px solid {{ $themeColors['primary'] }}40; border-radius: 24px; padding: 50px; position: relative; overflow: hidden;">
                        <!-- Animated Background -->
                        <div style="position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: {{ $themeColors['gradient'] }}; border-radius: 50%; opacity: 0.08; filter: blur(60px); animation: float 6s ease-in-out infinite;"></div>
                        
                        <div class="row align-items-center" style="position: relative; z-index: 2;">
                            <div class="col-lg-7">
                                <div style="display: inline-flex; align-items: center; gap: 12px; background: {{ $themeColors['primary'] }}20; padding: 12px 24px; border-radius: 50px; margin-bottom: 20px; border: 1px solid {{ $themeColors['primary'] }}30;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ $themeColors['primary'] }}">
                                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.23-8.24 8.23-1.48 0-2.93-.39-4.19-1.15l-.3-.17-3.12.82.83-3.04-.2-.32a8.188 8.188 0 01-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31-.22.25-.87.85-.87 2.07 0 1.22.89 2.39 1 2.56.14.17 1.76 2.67 4.25 3.73.59.27 1.05.42 1.41.53.59.19 1.13.16 1.56.1.48-.07 1.46-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.16-.48-.27-.25-.14-1.47-.74-1.69-.82-.23-.08-.37-.12-.56.12-.16.25-.64.81-.78.97-.15.17-.29.19-.53.07-.26-.13-1.06-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.11-.56-1.35-.77-1.84-.2-.48-.4-.42-.56-.43-.14 0-.3-.01-.46-.01z"/>
                                    </svg>
                                    <span style="font-weight: 800; font-size: 0.85rem; color: {{ $themeColors['primary'] }}; text-transform: uppercase; letter-spacing: 1px;">Expert IT Consultation</span>
                                </div>
                                <h3 style="font-size: 2.2rem; font-weight: 900; color: #0f172a; margin-bottom: 12px; line-height: 1.2;">Transform your digital presence</h3>
                                <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 0; line-height: 1.6;">Connect with our IT specialists for web development, digital marketing, and comprehensive IT solutions.</p>
                            </div>
                            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                                <div class="d-flex flex-column gap-3 align-items-lg-end">
                                    <a href="https://wa.me/923034829937" target="_blank" class="whatsapp-btn" style="display: inline-flex; align-items: center; gap: 12px; background: {{ $themeColors['gradient'] }}; color: white; padding: 18px 35px; border-radius: 16px; text-decoration: none; font-weight: 800; font-size: 1.05rem; box-shadow: 0 10px 30px {{ $themeColors['primary'] }}40; transition: all 0.3s ease; position: relative; overflow: hidden; width: fit-content;">
                                        <span style="position: relative; z-index: 2;">Chat with Waseem</span>
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="white" style="position: relative; z-index: 2;">
                                            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.23-8.24 8.23-1.48 0-2.93-.39-4.19-1.15l-.3-.17-3.12.82.83-3.04-.2-.32a8.188 8.188 0 01-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31-.22.25-.87.85-.87 2.07 0 1.22.89 2.39 1 2.56.14.17 1.76 2.67 4.25 3.73.59.27 1.05.42 1.41.53.59.19 1.13.16 1.56.10.48-.07 1.46-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.16-.48-.27-.25-.14-1.47-.74-1.69-.82-.23-.08-.37-.12-.56.12-.16.25-.64.81-.78.97-.15.17-.29.19-.53.07-.26-.13-1.06-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.11-.56-1.35-.77-1.84-.2-.48-.4-.42-.56-.43-.14 0-.3-.01-.46-.01z"/>
                                        </svg>
                                        <div class="btn-shine" style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);"></div>
                                    </a>
                                    <a href="https://wa.me/923051852884" target="_blank" class="whatsapp-btn whatsapp-btn-secondary" style="display: inline-flex; align-items: center; gap: 12px; background: white; color: {{ $themeColors['primary'] }}; border: 2px solid {{ $themeColors['primary'] }}; padding: 18px 35px; border-radius: 16px; text-decoration: none; font-weight: 800; font-size: 1.05rem; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; position: relative; overflow: hidden; width: fit-content;">
                                        <span style="position: relative; z-index: 2;">Chat with Ans</span>
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="{{ $themeColors['primary'] }}" style="position: relative; z-index: 2;">
                                            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.23-8.24 8.23-1.48 0-2.93-.39-4.19-1.15l-.3-.17-3.12.82.83-3.04-.2-.32a8.188 8.188 0 01-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31-.22.25-.87.85-.87 2.07 0 1.22.89 2.39 1 2.56.14.17 1.76 2.67 4.25 3.73.59.27 1.05.42 1.41.53.59.19 1.13.16 1.56.1.48-.07 1.46-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.16-.48-.27-.25-.14-1.47-.74-1.69-.82-.23-.08-.37-.12-.56.12-.16.25-.64.81-.78.97-.15.17-.29.19-.53.07-.26-.13-1.06-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.11-.56-1.35-.77-1.84-.2-.48-.4-.42-.56-.43-.14 0-.3-.01-.46-.01z"/>
                                        </svg>
                                    </a>
                                </div>
                                <p style="margin-top: 12px; font-size: 0.9rem; color: #94a3b8; font-weight: 600;">Available 24/7 for urgent inquiries</p>
                            </div>
                        </div>
                    </div>
                @elseif($division->name === 'Technical Support')
                    <div class="cta-card" style="background: linear-gradient(135deg, {{ $themeColors['primary'] }}15 0%, {{ $themeColors['secondary'] }}15 100%); border: 2px solid {{ $themeColors['primary'] }}40; border-radius: 24px; padding: 50px; position: relative; overflow: hidden;">
                        <!-- Animated Background -->
                        <div style="position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: {{ $themeColors['gradient'] }}; border-radius: 50%; opacity: 0.08; filter: blur(60px); animation: float 6s ease-in-out infinite;"></div>
                        
                        <div class="row align-items-center" style="position: relative; z-index: 2;">
                            <div class="col-lg-8">
                                <div style="display: inline-flex; align-items: center; gap: 12px; background: {{ $themeColors['primary'] }}20; padding: 12px 24px; border-radius: 50px; margin-bottom: 20px; border: 1px solid {{ $themeColors['primary'] }}30;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ $themeColors['primary'] }}">
                                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.23-8.24 8.23-1.48 0-2.93-.39-4.19-1.15l-.3-.17-3.12.82.83-3.04-.2-.32a8.188 8.188 0 01-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31-.22.25-.87.85-.87 2.07 0 1.22.89 2.39 1 2.56.14.17 1.76 2.67 4.25 3.73.59.27 1.05.42 1.41.53.59.19 1.13.16 1.56.1.48-.07 1.46-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.16-.48-.27-.25-.14-1.47-.74-1.69-.82-.23-.08-.37-.12-.56.12-.16.25-.64.81-.78.97-.15.17-.29.19-.53.07-.26-.13-1.06-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.11-.56-1.35-.77-1.84-.2-.48-.4-.42-.56-.43-.14 0-.3-.01-.46-.01z"/>
                                    </svg>
                                    <span style="font-weight: 800; font-size: 0.85rem; color: {{ $themeColors['primary'] }}; text-transform: uppercase; letter-spacing: 1px;">24/7 Technical Assistance</span>
                                </div>
                                <h3 style="font-size: 2.2rem; font-weight: 900; color: #0f172a; margin-bottom: 12px; line-height: 1.2;">Need technical assistance?</h3>
                                <p style="font-size: 1.1rem; color: #64748b; margin-bottom: 0; line-height: 1.6;">Get immediate help with networking, system administration, and all your technical challenges from our certified support specialist.</p>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                <a href="https://wa.me/923205889344" target="_blank" class="whatsapp-btn" style="display: inline-flex; align-items: center; gap: 12px; background: {{ $themeColors['gradient'] }}; color: white; padding: 20px 40px; border-radius: 16px; text-decoration: none; font-weight: 800; font-size: 1.1rem; box-shadow: 0 10px 30px {{ $themeColors['primary'] }}40; transition: all 0.3s ease; position: relative; overflow: hidden;">
                                    <span style="position: relative; z-index: 2;">Chat with Nazim</span>
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white" style="position: relative; z-index: 2;">
                                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.23-8.24 8.23-1.48 0-2.93-.39-4.19-1.15l-.3-.17-3.12.82.83-3.04-.2-.32a8.188 8.188 0 01-1.26-4.38c.01-4.54 3.7-8.24 8.25-8.24M8.53 7.33c-.16 0-.43.06-.66.31-.22.25-.87.85-.87 2.07 0 1.22.89 2.39 1 2.56.14.17 1.76 2.67 4.25 3.73.59.27 1.05.42 1.41.53.59.19 1.13.16 1.56.1.48-.07 1.46-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.16-.48-.27-.25-.14-1.47-.74-1.69-.82-.23-.08-.37-.12-.56.12-.16.25-.64.81-.78.97-.15.17-.29.19-.53.07-.26-.13-1.06-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.39.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.11-.56-1.35-.77-1.84-.2-.48-.4-.42-.56-.43-.14 0-.3-.01-.46-.01z"/>
                                    </svg>
                                    <div class="btn-shine" style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);"></div>
                                </a>
                                <p style="margin-top: 12px; font-size: 0.9rem; color: #94a3b8; font-weight: 600;">Emergency support available</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if(!$loop->last)
            <div class="divider"></div>
        @endif
    @empty
        <div class="alert alert-info text-center py-5" style="border-radius: 20px; border: 2px dashed #667eea; background: rgba(102, 126, 234, 0.05);">
            <div style="display: flex; justify-content: center; color: #667eea; margin-bottom: 20px;"><x-work-icon type="audit" style="width: 64px; height: 64px;" /></div>
            <h4 style="color: #667eea; font-weight: 700;">No Services Available</h4>
            <p class="mb-0 text-muted">Please check back soon for our professional services.</p>
        </div>
    @endforelse
</div>

{{-- Stats Section --}}
<div class="services-container">
    <div class="stats-section">
        <div class="stats-content">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="stat-number">{{ $totalServices }}</span>
                        <p class="stat-label mb-0">Professional Services</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="stat-number">{{ $totalReviews }}</span>
                        <p class="stat-label mb-0">Client Reviews</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="stat-number">{{ $servicesByDivision->count() }}</span>
                        <p class="stat-label mb-0">Service Divisions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
