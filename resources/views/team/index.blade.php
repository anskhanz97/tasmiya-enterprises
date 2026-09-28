@extends('layouts.app')

@section('title', 'Our Team')

@section('content')
<style>
    /* ===== HERO SECTION ===== */
    .team-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
        padding: 100px 20px 140px;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .team-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="1.5" fill="white" opacity="0.2"/></svg>');
        animation: floatPattern 20s linear infinite;
    }
    
    @keyframes floatPattern {
        0% { transform: translateY(0) translateX(0); }
        50% { transform: translateY(-20px) translateX(10px); }
        100% { transform: translateY(0) translateX(0); }
    }
    
    .team-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
        max-width: 900px;
        animation: fadeInDown 0.8s ease-out;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .team-hero h1 {
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 900;
        margin-bottom: 1.5rem;
        line-height: 1;
        letter-spacing: -2px;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    
    .team-hero p {
        font-size: 1.3rem;
        opacity: 0.95;
        margin-bottom: 3rem;
        line-height: 1.7;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 70px;
        flex-wrap: wrap;
        margin-top: 3rem;
    }
    
    .stat-box {
        text-align: center;
        animation: fadeInUp 0.8s ease-out backwards;
    }
    
    .stat-box:nth-child(1) { animation-delay: 0.2s; }
    .stat-box:nth-child(2) { animation-delay: 0.4s; }
    .stat-box:nth-child(3) { animation-delay: 0.6s; }
    
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
    
    .stat-number {
        font-size: 3.5rem;
        font-weight: 900;
        display: block;
        line-height: 1;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .stat-label {
        font-size: 0.95rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
    }
    
    /* ===== MAIN CONTENT ===== */
    .team-container {
        max-width: 1500px;
        margin: 0 auto;
        padding: 120px 20px;
        perspective: 2000px;
    }
    
    /* ===== TEAM GRID - STAGGERED DIAMOND LAYOUT ===== */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 60px;
        max-width: 1200px;
        margin: 0 auto 80px;
    }
    
    /* ===== 3D FLIP CARD ===== */
    .team-member-wrapper {
        perspective: 1500px;
        animation: slideIn 0.8s ease-out backwards;
    }
    
    .team-member-wrapper:nth-child(1) { animation-delay: 0.1s; }
    .team-member-wrapper:nth-child(2) { animation-delay: 0.2s; }
    .team-member-wrapper:nth-child(3) { animation-delay: 0.3s; }
    .team-member-wrapper:nth-child(4) { animation-delay: 0.4s; }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .team-member {
        position: relative;
        width: 100%;
        height: 600px;
        transform-style: preserve-3d;
        transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1);
        cursor: pointer;
    }
    
    .team-member:hover {
        transform: rotateY(180deg);
    }
    
    .card-front,
    .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    
    .card-front {
        background: white;
        display: flex;
        flex-direction: column;
    }
    
    .card-back {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 50px;
        color: white;
        text-align: center;
    }
    
    /* ===== FRONT CARD ===== */
    .member-banner {
        position: relative;
        height: 220px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        overflow: hidden;
    }
    
    .member-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    
    .team-member-wrapper:hover .member-banner img {
        transform: scale(1.15);
    }
    
    .member-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.5) 100%);
        z-index: 1;
    }
    
    .member-profile-image {
        position: relative;
        margin: -90px auto 0;
        z-index: 10;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 8px solid white;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: transform 0.5s ease, box-shadow 0.5s ease;
    }
    
    .team-member-wrapper:hover .member-profile-image {
        transform: scale(1.1) translateY(-10px);
        box-shadow: 0 25px 60px rgba(102, 126, 234, 0.4);
    }
    
    .member-profile-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .member-profile-image { display: grid; place-items: center; }
    .member-profile-image .member-initial { color: #fff; font-size: 4rem; font-weight: 800; }
    
    .member-info {
        padding: 30px 40px 40px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .member-name {
        font-size: 2rem;
        font-weight: 900;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
        letter-spacing: -1px;
    }
    
    .member-title {
        font-size: 1.05rem;
        color: #667eea;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: divisionBounce 2s ease-in-out infinite;
    }

    @keyframes divisionBounce {
        0%,
        100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-4px);
        }
    }
    
    .member-bio {
        font-size: 1rem;
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 2rem;
        flex-grow: 1;
    }
    
    .member-skills {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    
    .skill-tag {
        display: inline-block;
        padding: 8px 16px;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8edff 100%);
        color: #667eea;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    
    .skill-tag:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
    }
    
    .flip-hint {
        font-size: 0.9rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: auto;
        padding-top: 20px;
        animation: fadeInOut 2s ease-in-out infinite;
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }
    
    /* ===== BACK CARD ===== */
    .back-content h3 {
        font-size: 2rem;
        font-weight: 900;
        margin-bottom: 1.5rem;
        letter-spacing: -1px;
    }
    
    .back-content p {
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: 2.5rem;
        opacity: 0.95;
    }
    
    .member-cta {
        display: inline-block;
        padding: 16px 40px;
        background: white;
        color: #667eea;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 900;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }
    
    .member-cta:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        background: #f8faff;
    }
    
    .team-contact-links {
        display: flex;
        gap: 14px;
        margin-top: 2rem;
        justify-content: center;
    }
    
    .team-contact-link {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #51449e;
        text-decoration: none;
        box-shadow: 0 5px 15px rgba(31, 26, 87, 0.18);
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    
    .team-contact-link svg {
        display: block;
        width: 24px;
        height: 24px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .team-contact-link:hover {
        background: #eef0ff;
        color: #33247e;
        transform: translateY(-3px);
    }

    .team-contact-link:focus-visible {
        outline: 3px solid #fff;
        outline-offset: 4px;
    }
    
    /* ===== CTA SECTION ===== */
    .team-cta {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 100px 40px;
        border-radius: 30px;
        text-align: center;
        color: white;
        margin-top: 100px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    }
    
    .team-cta::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .team-cta-content {
        position: relative;
        z-index: 1;
    }
    
    .team-cta h2 {
        font-size: 2.8rem;
        font-weight: 900;
        margin-bottom: 1.5rem;
        letter-spacing: -1px;
    }
    
    .team-cta p {
        font-size: 1.25rem;
        opacity: 0.95;
        margin-bottom: 3rem;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.8;
    }
    
    .cta-btn {
        display: inline-block;
        padding: 20px 60px;
        background: white;
        color: #667eea;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 900;
        transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
        font-size: 1.15rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .cta-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(102, 126, 234, 0.1);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .cta-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .cta-btn:hover {
        transform: translateY(-8px) scale(1.05);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }
    
    @media (max-width: 992px) {
        .team-grid {
            grid-template-columns: 1fr;
            gap: 50px;
            max-width: 500px;
        }
        
        .team-member {
            height: 550px;
        }
        
        .hero-stats {
            gap: 40px;
        }
        
        .stat-number {
            font-size: 2.8rem;
        }
        
        .team-hero {
            padding: 80px 20px 120px;
        }
        
        .team-container {
            padding: 100px 20px;
        }
    }
    
    @media (max-width: 576px) {
        .member-info {
            padding: 25px 30px 35px;
        }
        
        .member-profile-image {
            width: 150px;
            height: 150px;
            margin-top: -75px;
        }
        
        .member-banner {
            height: 180px;
        }
        
        .team-member {
            height: 580px;
        }
        
        .team-hero h1 {
            font-size: 2.5rem;
        }
        
        .team-cta h2 {
            font-size: 2rem;
        }
        
        .cta-btn {
            padding: 16px 40px;
            font-size: 1rem;
        }
    }
    
    /* Mobile: Tap to Flip */
    @media (hover: none) {
        .team-member {
            cursor: pointer;
        }
    }
    .team-member:focus-within { transform: rotateY(180deg); }
    .member-cta:focus-visible, .cta-btn:focus-visible { outline: 3px solid #132a50; outline-offset: 4px; }
    @media (prefers-reduced-motion: reduce) {
        .team-hero::before, .member-title, .flip-hint, .team-cta::before { animation: none; }
        .team-member, .member-banner img, .member-profile-image { transition-duration: .01ms; }
    }
</style>

<!-- HERO SECTION -->
<div class="team-hero">
    <div class="team-hero-content">
        <h1>Meet Our Leadership Team</h1>
        <p>Dedicated professionals bringing expertise in taxation, technology, and business solutions to help your enterprise thrive.</p>
        @php
            $totalTeam = $profiles->count();
            $totalDivisions = \App\Models\Division::count();
        @endphp
        <div class="hero-stats">
            <div class="stat-box">
                <span class="stat-number">{{ $totalTeam }}</span>
                <span class="stat-label">Core Team</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">{{ $totalDivisions }}</span>
                <span class="stat-label">Divisions</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">20+</span>
                <span class="stat-label">Years Experience</span>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="team-container">
    <!-- TEAM GRID with 3D Flip Cards -->
    <div class="team-grid">
        @foreach($profiles as $profile)
            @php
                $firstName = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::before($profile->user->name, ' '));
                $bannerImage = $profile->getBannerImageUrl();
                $profileImage = $profile->displayImageUrl('profile');
                if (($profile->banner_image_url === null || $profile->banner_image_url === "/images/profiles/{$firstName}/banner.webp") && $bannerImage && is_file(public_path("images/profiles/{$firstName}/banner-card.webp"))) {
                    $bannerImage = asset("images/profiles/{$firstName}/banner-card.webp");
                }
                if (($profile->profile_image_url === null || $profile->profile_image_url === "/images/profiles/{$firstName}/image.webp") && $profileImage && is_file(public_path("images/profiles/{$firstName}/image-card.webp"))) {
                    $profileImage = asset("images/profiles/{$firstName}/image-card.webp");
                }
            @endphp
                <div class="team-member-wrapper">
                    <div class="team-member">
                        <!-- FRONT SIDE -->
                        <div class="card-front">
                            <!-- Banner Image -->
                            <div class="member-banner">
                                @if($bannerImage)<img src="{{ $bannerImage }}" alt="" loading="lazy" decoding="async" width="960" height="360">@endif
                            </div>
                            
                            <!-- Profile Image (Overlapping) -->
                            <div class="member-profile-image">
                                @if($profileImage)
                                    <img src="{{ $profileImage }}" alt="Portrait of {{ $profile->user->name }}" loading="lazy" decoding="async" width="480" height="480">
                                @else
                                    <span class="member-initial" aria-hidden="true">{{ strtoupper(substr($profile->user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            
                            <!-- Member Info -->
                            <div class="member-info">
                                <div class="member-name">{{ $profile->user->name }}</div>
                                <div class="member-title">{{ $profile->user->division->name ?? 'Leadership' }}</div>
                                <div class="member-bio">
                                    {{ Str::limit($profile->bio ?? 'Experienced professional dedicated to delivering excellence and driving business success', 110) }}
                                </div>
                                @if($profile->expertise_areas)
                                    <div class="member-skills">
                                        @foreach(array_slice(explode(',', $profile->expertise_areas), 0, 4) as $skill)
                                            <span class="skill-tag">{{ trim($skill) }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="flip-hint">⟲ Hover to flip ⟲</div>
                            </div>
                        </div>
                        
                        <!-- BACK SIDE -->
                        <div class="card-back">
                            <div class="back-content">
                                <h3>{{ $profile->user->name }}</h3>
                                <p>{{ $profile->bio ?? 'Dedicated to providing exceptional service and expertise in driving business growth and success. With years of experience, committed to delivering results that matter.' }}</p>
                                <a href="{{ route('profiles.show', $profile) }}" class="member-cta">View Full Profile →</a>
                                
                                @php
                                    $links = $profile->social_links ?? [];
                                    $phone = preg_replace('/[^\d+]/', '', $links['phone'] ?? $links['whatsapp'] ?? '');
                                    $linkedIn = $links['linkedin'] ?? null;
                                    if ($linkedIn && ! \Illuminate\Support\Str::startsWith($linkedIn, ['http://', 'https://'])) {
                                        $linkedIn = 'https://' . $linkedIn;
                                    }
                                @endphp
                                @if($phone || !empty($links['email']) || $linkedIn)
                                    <div class="team-contact-links" aria-label="Contact {{ $profile->user->name }}">
                                        @if($phone)
                                            <a href="tel:{{ $phone }}" class="team-contact-link" aria-label="Call {{ $profile->user->name }}" title="Call {{ $profile->user->name }}">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h3l2 5-2 2a16 16 0 0 0 6 6l2-2 5 2v3a2 2 0 0 1-2 2C10.2 21 3 13.8 3 5a2 2 0 0 1 2-2Z"/></svg>
                                            </a>
                                        @endif
                                        @if(!empty($links['email']))
                                            <a href="mailto:{{ $links['email'] }}" class="team-contact-link" aria-label="Email {{ $profile->user->name }}" title="Email {{ $profile->user->name }}">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                                            </a>
                                        @endif
                                        @if($linkedIn)
                                            <a href="{{ $linkedIn }}" class="team-contact-link" target="_blank" rel="noopener noreferrer" aria-label="{{ $profile->user->name }} on LinkedIn" title="LinkedIn">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10v7M8 7.5v.1M12 17v-7h3a3 3 0 0 1 3 3v4M12 13a3 3 0 0 1 3-3"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        @endforeach
    </div>
    
    <!-- CTA SECTION -->
    <div class="team-cta">
        <div class="team-cta-content">
            <h2>Ready to Work With Our Team?</h2>
            <p>Reach out to us and discover how our experts can help transform your business and achieve your goals together.</p>
            <a href="{{ route('contact.create') }}" class="cta-btn">Get in Touch</a>
        </div>
    </div>
</div>

<script>
    // Add touch support for mobile devices
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.team-member');
        
        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Prevent following the link when clicking on the card itself
                if (e.target.closest('.member-cta, .team-contact-link')) {
                    return; // Allow CTA click to work
                }
                
                // Toggle flip on mobile
                if (window.matchMedia('(hover: none)').matches) {
                    e.preventDefault();
                    this.style.transform = this.style.transform === 'rotateY(180deg)' 
                        ? 'rotateY(0deg)' 
                        : 'rotateY(180deg)';
                }
            });
        });
    });
</script>

@endsection
