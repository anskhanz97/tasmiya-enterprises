@extends('layouts.app')

@section('title', $profile->user->name . ' - Profile')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --primary-color: #667eea;
        --secondary-color: #764ba2;
    }
    
    /* ===== PARALLAX HERO ===== */
    .profile-hero {
        position: relative;
        height: 600px;
        overflow: hidden;
        background: var(--primary-gradient);
    }
    
    .hero-banner {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-size: cover;
        background-position: center;
        will-change: transform;
        transition: transform 0.1s ease-out;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(102, 126, 234, 0.8) 100%);
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        text-align: center;
        padding: 0 20px;
    }
    
    /* ===== FLOATING PROFILE IMAGE ===== */
    .profile-image-container {
        position: relative;
        margin-bottom: -120px;
        z-index: 10;
        animation: floatIn 1.2s ease-out;
    }
    
    @keyframes floatIn {
        from {
            opacity: 0;
            transform: translateY(-100px) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .profile-image-wrapper {
        width: 280px;
        height: 280px;
        border-radius: 50%;
        border: 8px solid white;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        background: white;
        position: relative;
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .profile-image-wrapper:hover {
        transform: scale(1.08) rotate(5deg);
    }
    
    .profile-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-ring {
        position: absolute;
        top: -20px;
        left: -20px;
        right: -20px;
        bottom: -20px;
        border-radius: 50%;
        border: 3px solid rgba(102, 126, 234, 0.3);
        animation: pulse-ring 2s ease-in-out infinite;
    }
    
    @keyframes pulse-ring {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.5; }
    }
    
    .hero-title {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 1rem;
        letter-spacing: -2px;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.8s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .hero-role {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 0.95;
        animation: slideUp 0.8s ease-out 0.2s backwards;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .hero-stats {
        display: flex;
        gap: 60px;
        margin-top: 2rem;
        animation: fadeIn 1s ease-out 0.4s backwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        font-size: 2.2rem;
        font-weight: 900;
        display: block;
    }
    
    .stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
    }
    
    /* ===== INFO SECTION ===== */
    .info-section {
        background: white;
        padding: 120px 20px 60px;
        margin-top: -60px;
        position: relative;
        z-index: 5;
    }
    
    .info-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .bio-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 30px;
        padding: 50px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        margin-bottom: 50px;
        border: 1px solid rgba(102, 126, 234, 0.1);
    }
    
    .bio-card h2 {
        font-size: 2rem;
        font-weight: 900;
        color: #1a1a2e;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    .bio-text {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #4a5568;
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
    }
    
    /* ===== SKILLS WITH ANIMATED BARS ===== */
    .skills-section {
        margin-bottom: 60px;
    }
    
    .section-title {
        font-size: 2rem;
        font-weight: 900;
        text-align: center;
        margin-bottom: 2.5rem;
        color: #1a1a2e;
    }
    
    .skills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .skill-item {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .skill-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2);
    }
    
    .skill-name {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .skill-percentage {
        color: var(--primary-color);
        font-size: 0.95rem;
    }
    
    .skill-bar-bg {
        height: 10px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .skill-bar-fill {
        height: 100%;
        background: var(--primary-gradient);
        border-radius: 10px;
        width: 0;
        transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .skill-item.visible .skill-bar-fill {
        width: var(--skill-width);
    }
    
    /* ===== HORIZONTAL PROJECT SLIDER ===== */
    .projects-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 80px 20px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .projects-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="1" fill="white" opacity="0.3"/></svg>');
        opacity: 0.5;
    }
    
    .projects-container {
        position: relative;
        z-index: 1;
    }
    
    .projects-slider {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 30px;
        padding: 40px 20px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .projects-slider::-webkit-scrollbar {
        display: none;
    }
    
    .project-card {
        flex: 0 0 350px;
        scroll-snap-align: center;
        background: white;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform: perspective(1000px) rotateY(0deg);
    }
    
    .project-card:hover {
        transform: perspective(1000px) rotateY(5deg) translateY(-20px);
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
    }
    
    .project-icon {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
    }
    
    .project-content {
        padding: 25px;
        color: #1a1a2e;
    }
    
    .project-title {
        font-size: 1.15rem;
        font-weight: 900;
        margin-bottom: 0.8rem;
    }
    
    .project-description {
        color: #64748b;
        line-height: 1.6;
        font-size: 0.95rem;
    }
    
    .slider-controls {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 40px;
    }
    
    .slider-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    
    .slider-btn:hover {
        background: white;
        color: var(--primary-color);
        transform: scale(1.1);
    }
    
    /* ===== SERVICES GRID ===== */
    .services-section {
        padding: 80px 20px;
        background: #f8f9fa;
    }
    
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .profile-service-empty { grid-column: 1 / -1; padding: 24px; border: 1px dashed #bbc9dd; border-radius: 14px; color: #60728a; background: #fff; text-align: center; }
    .profile-service-manage { display: inline-block; margin: 14px auto 0; padding: 10px 15px; border-radius: 9px; background: #e9e7fa; color: #5146a5; font-size: 13px; font-weight: 800; text-decoration: none; }
    
    /* ===== TESTIMONIALS CAROUSEL ===== */
    .testimonials-section {
        background: white;
        padding: 80px 20px;
    }
    
    .testimonials-carousel {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        display: grid;
    }
    
    .testimonial-card {
        grid-area: 1 / 1;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 30px;
        padding: 45px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .testimonial-card.active {
        visibility: visible;
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
    }
    
    .testimonial-text {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #4a5568;
        font-style: italic;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    .testimonial-author {
        text-align: center;
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .testimonial-dots {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 40px;
    }
    
    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #cbd5e0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .dot.active {
        background: var(--primary-color);
        transform: scale(1.3);
    }
    
    /* ===== CTA SECTION ===== */
    .cta-section {
        background: var(--primary-gradient);
        padding: 80px 20px;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .cta-content {
        position: relative;
        z-index: 1;
    }
    
    .cta-title {
        font-size: 2.3rem;
        font-weight: 900;
        margin-bottom: 1.2rem;
    }
    
    .cta-description {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.95;
    }
    
    .cta-button {
        display: inline-block;
        padding: 16px 50px;
        background: white;
        color: var(--primary-color);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 900;
        font-size: 1.05rem;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .cta-button:hover {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.2rem;
        }
        
        .hero-role {
            font-size: 1rem;
        }
        
        .hero-stats {
            gap: 25px;
        }
        
        .stat-number {
            font-size: 1.8rem;
        }
        
        .profile-image-wrapper {
            width: 200px;
            height: 200px;
        }
        
        .bio-card {
            padding: 35px 25px;
        }
        
        .project-card {
            flex: 0 0 280px;
        }
        
        .section-title {
            font-size: 1.7rem;
        }
    }
    .profile-sections { display: flex; flex-direction: column; }
    .profile-section-intro { max-width: 700px; margin: 12px auto 30px; text-align: center; color: #6a7e8b; line-height: 1.65; }
    .projects-section .profile-section-intro { color: rgba(255,255,255,.78); }
</style>

@php
    $firstName = strtolower(explode(' ', $profile->user->name)[0] ?? 'user');
    $bannerUrl = $profile->displayImageUrl('banner');
    $imageUrl = $profile->displayImageUrl('profile');
    $sections = collect($profile->presentationSections())->keyBy('key');
@endphp

<!-- PARALLAX HERO -->
<div class="profile-hero" id="hero">
    <div class="hero-banner" @if($bannerUrl) style="background-image: url('{{ $bannerUrl }}');" @endif></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">{{ $profile->user->name }}</h1>
        <p class="hero-role">{{ $profile->title ?? 'Professional' }}</p>
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number">{{ $profile->experience_years ?? '10+' }}</span>
                <span class="stat-label">Years Experience</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">6</span>
                <span class="stat-label">Projects</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">Clients</span>
            </div>
        </div>
    </div>
</div>

<!-- FLOATING PROFILE IMAGE -->
@if($imageUrl)
<div class="profile-image-container">
    <div class="profile-ring"></div>
    <div class="profile-image-wrapper">
        <img src="{{ $imageUrl }}" alt="{{ $profile->user->name }}">
    </div>
</div>
@endif

<!-- INFO SECTION -->
<div class="profile-sections">
@if($sections['about']['visible'])
<div class="info-section" style="order: {{ $sections['about']['order'] }};">
    <div class="info-container">
        <!-- Bio -->
        <div class="bio-card">
            <h2>{{ $sections['about']['title'] }}</h2>
            <p class="bio-text">
                {{ $profile->bio ?? 'Dedicated professional with extensive experience in delivering exceptional results and driving business success. Committed to excellence and continuous improvement in every project.' }}
            </p>
        </div>
        
    </div>
</div>
@endif

@if($sections['skills']['visible'])
<div class="info-section" style="order: {{ $sections['skills']['order'] }};">
    <div class="info-container">
        <div class="skills-section">
            <h2 class="section-title">{{ $sections['skills']['title'] }}</h2>
            <div class="skills-grid">
                @forelse($profile->specializations ?? [] as $skill)
                    <div class="skill-item">
                        <div class="skill-name"><span>{{ $skill }}</span></div>
                        <div class="skill-bar-bg"><div class="skill-bar-fill" style="--skill-width: 100%;"></div></div>
                    </div>
                @empty
                    <p class="bio-text">No specializations have been listed yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

<!-- PROJECTS SLIDER -->
@if($sections['projects']['visible'])
<div class="projects-section" style="order: {{ $sections['projects']['order'] }};">
    <div class="projects-container">
        <h2 class="section-title" style="color: white;">{{ $sections['projects']['title'] }}</h2>
        @if($sections['projects']['description'])<p class="profile-section-intro">{{ $sections['projects']['description'] }}</p>@endif
        <div class="projects-slider" id="projectsSlider">
            @php
                $projectIcons = [
                    'atif' => ['💼', '📊', '🏛️', '📈', '⚖️', '📑'],
                    'waseem' => ['🎨', '🖥️', '📱', '🚀', '💡', '🌐'],
                    'ans' => ['💻', '⚙️', '🔧', '📦', '🔐', '🗃️'],
                    'nazim' => ['🔧', '🔌', '📡', '🛡️', '⚡', '🖧']
                ];
                $icons = $projectIcons[$firstName] ?? ['✨', '🎯', '💡', '🚀', '📊', '🔥'];
                
                $projectTitles = [
                    'atif' => ['Tax Compliance System', 'Corporate Advisory', 'Legal Documentation', 'Audit Support', 'Regulatory Filing', 'Tax Optimization'],
                    'waseem' => ['Brand Identity Design', 'Digital Marketing Campaign', 'Web Application', 'Mobile App Development', 'AI Content Creation', 'E-commerce Platform'],
                    'ans' => ['Backend API Development', 'Database Architecture', 'Laravel Application', 'Secure Authentication', 'Data Migration', 'Server Optimization'],
                    'nazim' => ['Security System Setup', 'Network Infrastructure', 'Automation Solutions', 'System Integration', 'Hardware Installation', 'Technical Support']
                ];
                $titles = $projectTitles[$firstName] ?? ['Professional Service', 'Expert Consultation', 'Strategic Planning', 'Implementation', 'Support & Training', 'Quality Assurance'];
            @endphp
            
            @for($i = 0; $i < 6; $i++)
                <div class="project-card">
                    <div class="project-icon">{{ $icons[$i] }}</div>
                    <div class="project-content">
                        <h3 class="project-title">{{ $titles[$i] }}</h3>
                        <p class="project-description">
                            Professional {{ strtolower($titles[$i]) }} with proven results and exceptional client satisfaction.
                        </p>
                    </div>
                </div>
            @endfor
        </div>
        <x-theme-pagination id="projectsPager" label="Featured project pages" />
    </div>
</div>
@endif

<!-- SERVICES -->
@if($sections['services']['visible'])
<div class="services-section" style="order: {{ $sections['services']['order'] }};">
    <h2 class="section-title">{{ $sections['services']['title'] }}</h2>
    @if($sections['services']['description'])<p class="profile-section-intro">{{ $sections['services']['description'] }}</p>@endif
    <div class="services-grid">
        @forelse($profile->serviceOfferings as $offering)
            <x-service-card :offering="$offering" />
        @empty
            <p class="profile-service-empty">No services listed yet.</p>
        @endforelse
    </div>
    @if($canEdit)<div style="text-align:center"><a class="profile-service-manage" href="{{ route('profiles.services.index', $profile) }}">Manage service cards</a></div>@endif
</div>
@endif

<!-- TESTIMONIALS -->
<style>
.testimonials-section{background:radial-gradient(ellipse at 12% 30%,#ebe4fa,transparent 55%),linear-gradient(110deg,#f8f6fc,#f1edf8);padding:54px 22px}.profile-review-heading{max-width:900px;margin:0 auto 26px;display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap}.profile-review-heading .section-title{margin:0;text-align:left}.profile-review-heading .profile-service-manage{margin:0}.profile-review-heading>div{margin:0!important}.testimonials-section .testimonial-card{background:#fff;border:1px solid #e0d7ed;border-radius:22px;padding:32px 38px;box-shadow:0 14px 35px #30204b0b;scroll-margin-top:100px}.profile-review-meta{display:flex;align-items:center;gap:14px;margin-bottom:10px}.profile-review-meta time{margin-left:auto;color:#82738f;font-size:12px}.profile-review-quote{font-family:Georgia,serif;font-size:64px;line-height:1;color:#a18ac9;height:48px}.testimonials-section .testimonial-text{text-align:left;color:#382b51;font-size:clamp(17px,2vw,22px);font-style:normal;line-height:1.8;overflow-wrap:anywhere}.profile-review-person{display:flex;align-items:center;gap:12px;border-top:1px solid #eee8f5;padding-top:22px}.profile-review-avatar{display:grid;place-items:center;width:46px;height:46px;flex:none;background:#eee6fa;border-radius:50%;color:#624693;font-size:14px;font-weight:800}.testimonials-section .testimonial-author{text-align:left;color:#5e428f;font-size:14px}@media(max-width:600px){.testimonials-section .testimonial-card{padding:22px}.profile-review-heading{display:block}.profile-review-heading>div{margin-top:18px!important}}
</style>
@if($sections['testimonials']['visible'])
<div id="profile-reviews" class="testimonials-section" style="order: {{ $sections['testimonials']['order'] }};">
    <div class="profile-review-heading">
    <h2 class="section-title">{{ $sections['testimonials']['title'] }}</h2>
    @if($sections['testimonials']['description'])<p class="profile-section-intro">{{ $sections['testimonials']['description'] }}</p>@endif
    @if($canEdit)<div style="text-align:center;margin-bottom:24px"><a class="profile-service-manage" href="{{ route('profiles.reviews.index', $profile) }}">Manage my reviews</a></div>@endif
    </div>
    @if($testimonials->isNotEmpty())
    <div class="testimonials-carousel">
        @foreach($testimonials as $index => $testimonial)
            <div id="review-{{ $testimonial->id }}" class="testimonial-card {{ $index === 0 ? 'active' : '' }}" aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                <div class="profile-review-meta"><span class="profile-review-quote" aria-hidden="true">“</span><span style="color:#bd8311" aria-label="{{ $testimonial->rating }} out of 5 stars">{{ $testimonial->getStars() }}</span><time datetime="{{ $testimonial->created_at->toDateString() }}">{{ $testimonial->created_at->format('M Y') }}</time></div>
                <p class="testimonial-text">“{{ $testimonial->content }}”</p>
                <div class="profile-review-person"><span class="profile-review-avatar" aria-hidden="true">{{ $testimonial->getInitials() }}</span><div class="testimonial-author">{{ $testimonial->getAuthorInfo() }}</div></div>
                @if($testimonial->testimonialable_type === \App\Models\Service::class)<p style="text-align:center;color:#65748b;margin-top:10px">{{ $profile->serviceOfferings->firstWhere('service_id', $testimonial->testimonialable_id)?->title() }}</p>@endif
            </div>
        @endforeach
    </div>
    <x-theme-pagination id="reviewsPager" label="Client review pages" />
    @else
    <p class="profile-service-empty" style="max-width:900px;margin:0 auto">No published reviews yet.</p>
    @endif
</div>
@endif
</div>

<!-- CTA -->
<div class="cta-section">
    <div class="cta-content">
        <h2 class="cta-title">Let's Work Together</h2>
        <p class="cta-description">Ready to start your next project? Get in touch today!</p>
        <a href="{{ $whatsappUrl ?? route('contact.create') }}" class="cta-button" @if($whatsappUrl) target="_blank" rel="noopener" @endif>Contact Me</a>
    </div>
</div>

<script>
    // Parallax effect
    document.addEventListener('mousemove', (e) => {
        const banner = document.querySelector('.hero-banner');
        const xAxis = (window.innerWidth / 2 - e.pageX) / 50;
        const yAxis = (window.innerHeight / 2 - e.pageY) / 50;
        banner.style.transform = `translate(${xAxis}px, ${yAxis}px) scale(1.1)`;
    });
    
    // Skills animation on scroll
    const observeSkills = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.5 });
    
    document.querySelectorAll('.skill-item').forEach(skill => {
        observeSkills.observe(skill);
    });
    
    // Project slider
    function scrollProjects(direction) {
        const slider = document.getElementById('projectsSlider');
        const scrollAmount = 430;
        slider.scrollBy({
            left: direction === 'left' ? -scrollAmount : scrollAmount,
            behavior: 'smooth'
        });
    }
    
    // Testimonials carousel
    let currentTestimonial = 0;
    const testimonials = document.querySelectorAll('.testimonial-card');
    const reviewPager = document.getElementById('reviewsPager');
    let reviewControls;
    
    function showTestimonial(index) {
        if (!testimonials.length) return;
        testimonials[currentTestimonial].classList.remove('active');
        testimonials[currentTestimonial].setAttribute('aria-hidden', 'true');
        
        currentTestimonial = index;
        
        testimonials[currentTestimonial].classList.add('active');
        testimonials[currentTestimonial].setAttribute('aria-hidden', 'false');
    }
    if (reviewPager) reviewControls = TasmiyaPager(reviewPager, {count:testimonials.length, onChange:showTestimonial});
    function openLinkedReview() {
        const index = [...testimonials].findIndex(card => '#'+card.id === location.hash);
        if (index >= 0) { reviewControls.set(index); testimonials[index].scrollIntoView({block:'center'}); }
    }
    openLinkedReview(); window.addEventListener('hashchange', openLinkedReview);
    const projectSlider = document.getElementById('projectsSlider');
    if (projectSlider) {
        const projectPager = TasmiyaPager(document.getElementById('projectsPager'), {
            count:()=>Math.ceil(projectSlider.scrollWidth/projectSlider.clientWidth),
            onChange:page=>{
                const pages = Math.ceil(projectSlider.scrollWidth/projectSlider.clientWidth);
                projectSlider.scrollTo({left:pages>1?page*(projectSlider.scrollWidth-projectSlider.clientWidth)/(pages-1):0,behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth'});
            }
        });
        projectSlider.addEventListener('scroll',()=>{
            const pages = Math.ceil(projectSlider.scrollWidth/projectSlider.clientWidth), maximum = projectSlider.scrollWidth-projectSlider.clientWidth;
            projectPager.set(maximum>0?Math.round(projectSlider.scrollLeft/maximum*(pages-1)):0,false);
        },{passive:true});
        new ResizeObserver(()=>projectPager.set(projectPager.current(),false)).observe(projectSlider);
    }
    
    // Auto-advance testimonials
    if (testimonials.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(() => {
        if (document.hidden || document.querySelector('.testimonials-section:hover, .testimonials-section:focus-within')) return;
        const next = (currentTestimonial + 1) % testimonials.length;
        reviewControls.set(next);
    }, 5000);
</script>

@endsection
