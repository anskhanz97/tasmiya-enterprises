@extends('layouts.app')

@section('title', 'Our Services')

@section('content')
<style>
    /* The service index is the hero artwork: three clear routes into the catalogue. */
    .services-hero {
        background: radial-gradient(circle at 83% 18%, rgba(176, 139, 255, .32), transparent 31%), linear-gradient(125deg, #1d194c 0%, #393078 48%, #7150aa 100%);
        position: relative;
        overflow: hidden;
        padding: clamp(135px, 13vw, 180px) 24px 110px;
        color: white;
    }
    .services-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px), linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px);
        background-size: 72px 72px;
        mask-image: linear-gradient(90deg, transparent, #000 28%, #000 90%);
        pointer-events: none;
    }
    .services-hero::after {
        content: '';
        position: absolute;
        width: min(42vw, 570px);
        aspect-ratio: 1;
        right: -12%;
        bottom: -70%;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 50%;
        box-shadow: 0 0 0 76px rgba(255,255,255,.025), 0 0 0 155px rgba(255,255,255,.025);
        pointer-events: none;
    }
    .services-hero-content {
        position: relative;
        z-index: 2;
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(310px, .8fr);
        align-items: center;
        gap: clamp(44px, 7vw, 110px);
    }
    .services-hero h1 {
        font-size: clamp(3.2rem, 6.6vw, 5.9rem);
        font-weight: 900;
        max-width: 10ch;
        margin: 0 0 30px;
        line-height: 1.04;
        letter-spacing: -.055em;
        color: #fff;
    }
    .services-hero p { max-width: 56ch; color: #e3def7; font-size: clamp(1.08rem, 1.6vw, 1.3rem); line-height: 1.7; margin: 0 0 34px; }
    .services-hero-cta { display: inline-flex; align-items: center; gap: 14px; padding: 15px 23px; border-radius: 10px; background: #fff; color: #392a81; font-weight: 800; text-decoration: none; box-shadow: 0 16px 36px rgba(14,10,52,.2); transition: transform .2s ease, box-shadow .2s ease; }
    .services-hero-cta:hover { color: #392a81; transform: translateY(-3px); box-shadow: 0 20px 38px rgba(14,10,52,.3); }
    .services-hero-cta span { font-size: 1.2rem; }
    .services-hero-guide { padding: 26px; border: 1px solid rgba(255,255,255,.28); border-radius: 24px; background: rgba(255,255,255,.11); box-shadow: 0 25px 65px rgba(16,10,55,.2); backdrop-filter: blur(14px); }
    .services-hero-guide h2 { margin: 0 0 18px; color: #fff; font-size: 1.25rem; font-weight: 800; }
    .services-hero-guide__link { display: grid; grid-template-columns: 48px minmax(0,1fr) auto; gap: 13px; align-items: center; padding: 15px 12px; border-top: 1px solid rgba(255,255,255,.23); color: #fff; text-decoration: none; transition: background .2s ease, padding-left .2s ease; }
    .services-hero-guide__link:hover { background: rgba(255,255,255,.13); color: #fff; padding-left: 18px; }
    .services-hero-guide__icon { display: grid; place-items: center; width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,.93); color: #3e3281; }
    .services-hero-guide__icon .work-icon { width: 31px; height: 31px; }
    .services-hero-guide__name { font-weight: 800; line-height: 1.3; }
    .services-hero-guide__count { color: #ddd5f8; font-size: .78rem; white-space: nowrap; }
    .services-hero-copy { animation: servicesEnter .7s ease-out both; }
    .services-hero-guide { animation: servicesEnter .7s .12s ease-out both; }
    @keyframes servicesEnter { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Main Content */
    .services-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 70px 20px;
    }
    .services-container--catalogue { padding-bottom: 0; }
    .services-container + .services-container { padding-top: 0; }
    .services-jump { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin: 0 0 54px; padding: 15px 18px; border: 1px solid #dcd8ed; border-radius: 14px; background: #fff; box-shadow: 0 8px 26px rgba(43,35,99,.06); }
    .services-jump__label { margin-right: 10px; color: #433776; font-weight: 800; }
    .services-jump a { padding: 9px 13px; border-radius: 8px; color: #514688; font-size: .86rem; font-weight: 700; text-decoration: none; background: #f4f1fb; }
    .services-jump a:hover, .services-jump a:focus-visible { background: #e8ddfa; color: #322569; }
    
    /* Division Section */
    .division-section {
        margin-bottom: 72px;
        padding: 40px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.25s ease;
        scroll-margin-top: 96px;
    }
    
    .division-section:hover {
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    }
    
    .division-header {
        margin-bottom: 36px;
        position: relative;
    }
    .division-breadcrumb { display: flex; align-items: center; gap: 9px; margin-bottom: 20px; color: #746a99; font-size: .82rem; font-weight: 700; }
    .division-breadcrumb a { color: #5e4eb0; text-decoration: none; }
    .division-breadcrumb a:hover { text-decoration: underline; }
    
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
        gap: 26px;
    }
    .service-page-item { min-width: 0; }
    .service-page-item[hidden], .service-pager[hidden] { display: none; }
    .service-pager { display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 14px; margin-top: 32px; }
    .service-pager button { border: 1px solid #c9bce9; background: #fff; color: #493799; border-radius: 9px; padding: 11px 17px; font-weight: 800; transition: background .2s ease, border-color .2s ease; }
    .service-pager button:not(:disabled):hover { background: #eee8fa; border-color: #8b6bdb; }
    .service-pager button:disabled { opacity: .42; cursor: not-allowed; }
    .service-pager__status { min-width: 92px; text-align: center; color: #5b5570; font-size: .88rem; font-weight: 700; }
    
    /* A compact closing summary instead of three oversized statistic cards. */
    .stats-section {
        background: linear-gradient(125deg, #241b59, #5f469c);
        padding: clamp(38px, 5vw, 70px);
        border-radius: 24px;
        position: relative;
        overflow: hidden;
    }
    .stats-section::before {
        content: '';
        position: absolute;
        width: 410px;
        height: 410px;
        right: -120px;
        top: -240px;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 50%;
        box-shadow: 0 0 0 65px rgba(255,255,255,.04), 0 0 0 130px rgba(255,255,255,.03);
    }
    .stats-content { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1fr) minmax(340px, .95fr); align-items: end; gap: 48px; }
    .stats-content h2 { max-width: 13ch; color: #fff; font-size: clamp(2rem, 3.4vw, 3.5rem); line-height: 1.1; letter-spacing: -.04em; font-weight: 900; }
    .stats-content p { max-width: 48ch; color: #e5dffa; line-height: 1.7; margin: 18px 0 22px; }
    .stats-content a { color: #fff; font-weight: 800; text-underline-offset: 5px; }
    .stats-metrics { display: grid; grid-template-columns: repeat(3, 1fr); border-top: 1px solid rgba(255,255,255,.35); padding-top: 25px; }
    .stats-metrics div { padding-inline: 16px; border-right: 1px solid rgba(255,255,255,.24); }
    .stats-metrics div:first-child { padding-left: 0; }
    .stats-metrics div:last-child { border-right: 0; }
    .stats-metrics strong { display: block; color: #fff; font-size: clamp(1.7rem, 3vw, 3rem); line-height: 1.15; font-weight: 900; }
    .stats-metrics span { display: block; margin-top: 8px; color: #e3dcf8; font-size: .78rem; line-height: 1.4; }
    }
    
    /* Service cards render immediately so the catalogue stays responsive. */
    
    @media (max-width: 768px) {
        .services-hero {
            padding: 105px 20px 58px;
        }
        .services-hero-content { grid-template-columns: 1fr; gap: 28px; }
        .services-hero h1 {
            font-size: clamp(2.8rem, 10vw, 4.4rem);
            margin-bottom: 22px;
        }
        .services-hero p { margin-bottom: 25px; }
        .services-hero-guide { padding: 18px; }
        
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
        
        .stats-content { grid-template-columns: 1fr; gap: 28px; }
        
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
    @media (max-width: 480px) {
        .services-jump { align-items: stretch; }
        .services-jump__label { flex-basis: 100%; }
        .services-jump a { flex: 1 1 auto; text-align: center; }
        .services-hero-guide__count { display: none; }
        .division-section { padding: 22px 16px; }
        .stats-section { padding: 34px 24px; }
        .stats-metrics { gap: 12px; }
        .stats-metrics div { padding-inline: 6px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .services-hero-copy, .services-hero-guide { animation: none; }
        .services-hero-cta, .services-hero-guide__link, .service-pager button { transition: none; }
    }
</style>

<div class="services-hero">
    <div class="services-hero-content">
        <div class="services-hero-copy">
            <h1>Expertise for the work ahead.</h1>
            <p>Taxation, digital delivery, and hands-on technical support—find the right specialist and a clear way forward for your business.</p>
            <a href="#services-catalogue" class="services-hero-cta">Explore our services <span aria-hidden="true">↗</span></a>
        </div>
        @if($servicesByDivision->isNotEmpty())
            <nav class="services-hero-guide" aria-label="Choose a service division">
                <h2>Find your focus</h2>
                @foreach($servicesByDivision as $group)
                    @php
                        $heroDivision = $group->first()->service->division;
                    @endphp
                    <a class="services-hero-guide__link" href="#division-{{ $heroDivision->slug }}">
                        <span class="services-hero-guide__icon"><x-work-icon :type="config('service_cards.divisions.' . $heroDivision->slug . '.icon', 'audit')" /></span>
                        <span class="services-hero-guide__name">{{ $heroDivision->name }}</span>
                        <span class="services-hero-guide__count">{{ $group->count() }} {{ \Illuminate\Support\Str::plural('service', $group->count()) }} ↗</span>
                    </a>
                @endforeach
            </nav>
        @endif
    </div>
</div>

<div class="services-container services-container--catalogue" id="services-catalogue">
    @if($servicesByDivision->isNotEmpty())
        <nav class="services-jump" aria-label="Browse service divisions">
            <span class="services-jump__label">Browse divisions</span>
            @foreach($servicesByDivision as $group)
                @php
                    $navDivision = $group->first()->service->division;
                @endphp
                <a href="#division-{{ $navDivision->slug }}">{{ $navDivision->name }}</a>
            @endforeach
        </nav>
    @endif
    {{-- Services by Division --}}
    @forelse($servicesByDivision as $divisionId => $services)
        @php
            $division = $services->first()->service->division;
            $themeColors = $division->getThemeColors();
        @endphp
        
        <section class="division-section" id="division-{{ $division->slug }}" data-service-section style="--division-primary: {{ $themeColors['primary'] }}; --division-secondary: {{ $themeColors['secondary'] }};">
            <div class="division-header">
                <div class="division-breadcrumb"><a href="#services-catalogue">All divisions</a><span aria-hidden="true">/</span><span>{{ $division->name }}</span></div>
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
                    <div class="service-page-item"><x-service-card :offering="$offering" /></div>
                @endforeach
            </div>
            <nav class="service-pager" aria-label="{{ $division->name }} service pages" hidden>
                <button type="button" data-page-prev>← Previous</button>
                <span class="service-pager__status" data-page-status aria-live="polite"></span>
                <button type="button" data-page-next>Next →</button>
            </nav>
            
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
        </section>

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
            <div>
                <h2>One team. Several ways forward.</h2>
                <p>From a tax filing to a new platform or urgent technical help, start with the specialist who knows the work.</p>
                <a href="{{ route('team.index') }}">Meet the team ↗</a>
            </div>
            <div class="stats-metrics" aria-label="Services at a glance">
                <div><strong>{{ $totalServices }}</strong><span>Services</span></div>
                <div><strong>{{ $totalReviews }}</strong><span>Client reviews</span></div>
                <div><strong>{{ $servicesByDivision->count() }}</strong><span>Divisions</span></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const pageSize = 6;
    for (const section of document.querySelectorAll('[data-service-section]')) {
        const items = [...section.querySelectorAll('.service-page-item')];
        const pager = section.querySelector('.service-pager');
        if (items.length <= pageSize || !pager) continue;

        const pageCount = Math.ceil(items.length / pageSize);
        const previous = pager.querySelector('[data-page-prev]');
        const next = pager.querySelector('[data-page-next]');
        const status = pager.querySelector('[data-page-status]');
        let page = 0;

        function showPage() {
            items.forEach((item, index) => { item.hidden = Math.floor(index / pageSize) !== page; });
            previous.disabled = page === 0;
            next.disabled = page === pageCount - 1;
            status.textContent = `Page ${page + 1} of ${pageCount}`;
        }

        function changePage(direction) {
            page += direction;
            showPage();
            section.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
        }

        previous.addEventListener('click', () => changePage(-1));
        next.addEventListener('click', () => changePage(1));
        pager.hidden = false;
        showPage();
    }
});
</script>
@endsection
