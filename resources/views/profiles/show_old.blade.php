@extends('layouts.app')

@section('content')
<style>
    /* ============================================
       VISUAL PORTFOLIO - Image-Heavy Design
       ============================================ */
    
    .profile-container {
        min-height: 100vh;
        background: #f8f9fa;
    }
    
    /* Extended Hero Section - 900px */
    .profile-hero-section {
        position: relative;
        width: 100%;
        height: 900px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        overflow: visible;
    }
    
    /* Particles Area - Upper 300px */
    .profile-particles-area {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 300px;
        width: 100%;
        z-index: 1;
        overflow: hidden;
    }
    
    /* Canvas for particles */
    #particle-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    
    /* Centered Profile Photo - Starts at 300px */
    .profile-photo-container {
        position: absolute;
        top: 300px;
        left: 50%;
        transform: translateX(-50%);
        width: 600px;
        height: 600px;
        z-index: 10;
    }
    
    .profile-photo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 20px;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
        border: 5px solid white;
        background: white;
    }
    
    /* Expertise Icons in Particles Area */
    .expertise-icons {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 6rem;
        opacity: 0.2;
        z-index: 2;
        animation: float 4s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(-50%, -50%) translateY(0px); }
        50% { transform: translate(-50%, -50%) translateY(-30px); }
    }
    
    /* Profile Info Overlay - Below Hero */
    .profile-info-overlay {
        position: relative;
        background: white;
        padding: 120px 20px 40px;
        margin-top: -80px;
        z-index: 5;
        text-align: center;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.1);
    }
    
    .profile-name {
        font-size: 3rem;
        font-weight: 900;
        color: #1a1a2e;
        margin: 0 0 10px;
        letter-spacing: -1px;
    }
    
    .profile-title {
        font-size: 1.5rem;
        color: #667eea;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .profile-division-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 700;
        margin: 15px 0 25px;
        font-size: 1rem;
    }
    
    /* WhatsApp Contact Button */
    .whatsapp-contact-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #25D366 0%, #20ba5a 100%);
        color: white;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.4s ease;
        font-size: 1.1rem;
        box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
    }
    
    .whatsapp-contact-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(37, 211, 102, 0.4);
    }
    
    /* Main Content Container */
    .portfolio-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }
    
    /* Section Styling */
    .portfolio-section {
        margin-bottom: 80px;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: #1a1a2e;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }
    
    .section-subtitle {
        font-size: 1.1rem;
        color: #666;
        margin-top: 20px;
    }
    
    /* About Section - Full Width Card */
    .about-card {
        background: white;
        padding: 50px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .about-text {
        font-size: 1.2rem;
        line-height: 2;
        color: #444;
    }
    
    /* Project Cards Grid */
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    
    .project-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        cursor: pointer;
    }
    
    .project-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.3);
    }
    
    .project-image {
        width: 100%;
        height: 220px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .project-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .project-info {
        padding: 25px;
    }
    
    .project-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 10px;
    }
    
    .project-description {
        font-size: 0.95rem;
        color: #666;
        line-height: 1.6;
    }
    
    /* Client Logos Grid */
    .clients-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 25px;
        margin-top: 40px;
    }
    
    .client-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        min-height: 180px;
    }
    
    .client-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }
    
    .client-logo {
        font-size: 3.5rem;
        margin-bottom: 15px;
    }
    
    .client-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
        text-align: center;
    }
    
    .client-type {
        font-size: 0.85rem;
        color: #999;
        margin-top: 5px;
    }
    
    /* Testimonials - Beautiful Cards */
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    
    .testimonial-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        transition: all 0.4s ease;
    }
    
    .testimonial-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.25);
    }
    
    .testimonial-card::before {
        content: '"';
        position: absolute;
        top: 20px;
        left: 30px;
        font-size: 8rem;
        color: #667eea;
        opacity: 0.1;
        font-family: Georgia, serif;
        line-height: 1;
    }
    
    .testimonial-text {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 25px;
        position: relative;
        z-index: 2;
        font-style: italic;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
    }
    
    .testimonial-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .testimonial-author-info {
        flex: 1;
    }
    
    .testimonial-author-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
    }
    
    .testimonial-author-position {
        font-size: 0.9rem;
        color: #667eea;
        font-weight: 600;
    }
    
    .testimonial-stars {
        color: #ffc107;
        font-size: 1.2rem;
        margin-top: 15px;
    }
    
    /* Stats Section */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin: 60px 0;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .stat-number {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 10px;
    }
    
    .stat-label {
        font-size: 1.1rem;
        opacity: 0.9;
        font-weight: 600;
    }
    
    /* Specializations Tags */
    .specializations-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        max-width: 800px;
        margin: 40px auto;
    }
    
    .specialization-tag {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8edff 100%);
        color: #667eea;
        padding: 12px 25px;
        border-radius: 30px;
        font-size: 1rem;
        font-weight: 700;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .specialization-tag:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .profile-hero-section {
            height: 700px;
        }
        
        .profile-particles-area {
            height: 300px;
        }
        
        .profile-photo-container {
            top: 300px;
            width: 350px;
            height: 350px;
        }
        
        .profile-name {
            font-size: 2rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .projects-grid,
        .clients-grid,
        .testimonials-grid {
            grid-template-columns: 1fr;
        }
        
        .about-card {
            padding: 30px;
        }
    }
</style>


<div class="profile-container">
    <!-- Hero Section with Particles (1000px total) -->
    <div class="profile-hero-section">
        <!-- Particles Area - Upper 500px -->
        <div class="profile-particles-area">
            <canvas id="particle-canvas"></canvas>
            
            <!-- Expertise Icon -->
            @php
                $icons = [
                    'atif' => '⚖️',
                    'waseem' => '🎨',
                    'ans' => '💻',
                    'nazim' => '🔧'
                ];
                $firstName = strtolower(explode(' ', $profile->user->name)[0]);
                $icon = $icons[$firstName] ?? '✨';
            @endphp
            <div class="expertise-icons">{{ $icon }}</div>
        </div>
        
        <!-- Profile Photo - Centered, starts at 500px, extends to 1000px -->
        <div class="profile-photo-container">
            <img src="{{ $profile->getBannerImageUrl() ?: 'https://ui-avatars.com/api/?name=' . urlencode($profile->user->name) . '&size=500&background=667eea&color=fff&font-size=0.4' }}" 
                 alt="{{ $profile->user->name }}" 
                 class="profile-photo">
        </div>
    </div>
    
    <!-- Profile Info Section - Overlaps hero -->
    <div class="profile-info-overlay">
        <h1 class="profile-name">{{ $profile->user->name }}</h1>
        <div class="profile-title">
            @if($profile->specializations && count($profile->specializations) > 0)
                {{ $profile->specializations[0] }}
            @else
                Professional Expert
            @endif
        </div>
        <div class="profile-division-badge">{{ $profile->user->division->name }}</div>
        
        @if($whatsappUrl)
            <a href="{{ $whatsappUrl }}" target="_blank" class="whatsapp-contact-btn">
                <span style="font-size: 1.5rem;">💬</span>
                <span>Connect on WhatsApp</span>
            </a>
        @endif
    </div>
    
    <!-- Main Portfolio Content -->
    <div class="portfolio-content">
        
        <!-- About Section -->
        @if($profile->bio)
            <section class="portfolio-section">
                <div class="section-header">
                    <h2 class="section-title">About Me</h2>
                </div>
                <div class="about-card" data-aos="fade-up">
                    <p class="about-text">{{ $profile->bio }}</p>
                </div>
            </section>
        @endif
        
        <!-- Stats Section -->
        <section class="portfolio-section">
            <div class="stats-container" data-aos="fade-up">
                <div class="stat-card">
                    <div class="stat-number">{{ $profile->experience_years }}+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        @php
                            $projectCount = ($profile->specializations ? count($profile->specializations) : 1) * 10;
                            echo $projectCount . '+';
                        @endphp
                    </div>
                    <div class="stat-label">Projects Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        @php
                            $clientCount = $profile->testimonials()->approved()->count();
                            echo $clientCount > 0 ? $clientCount . '+' : '50+';
                        @endphp
                    </div>
                    <div class="stat-label">Happy Clients</div>
                </div>
            </div>
        </section>
        
        <!-- Specializations -->
        @if($profile->specializations && count($profile->specializations) > 0)
            <section class="portfolio-section">
                <div class="section-header">
                    <h2 class="section-title">Expertise</h2>
                    <p class="section-subtitle">Core competencies and specialized skills</p>
                </div>
                <div class="specializations-container" data-aos="fade-up">
                    @foreach($profile->specializations as $spec)
                        <span class="specialization-tag">{{ $spec }}</span>
                    @endforeach
                </div>
            </section>
        @endif
        
        <!-- Projects Portfolio -->
        <section class="portfolio-section">
            <div class="section-header">
                <h2 class="section-title">Featured Projects</h2>
                <p class="section-subtitle">Showcasing excellence in {{ $profile->user->division->name }}</p>
            </div>
            <div class="projects-grid">
                @php
                    // Sample projects for portfolio showcase
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
                    <div class="project-card" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                        <div class="project-image">
                            <span>{{ $icons[$i] }}</span>
                        </div>
                        <div class="project-info">
                            <h3 class="project-title">{{ $titles[$i] }}</h3>
                            <p class="project-description">
                                Professional {{ strtolower($titles[$i]) }} with proven results and client satisfaction.
                            </p>
                        </div>
                    </div>
                @endfor
            </div>
        </section>
        
        <!-- Clients & Partners -->
        <section class="portfolio-section">
            <div class="section-header">
                <h2 class="section-title">Trusted By</h2>
                <p class="section-subtitle">Clients, partners, and organizations we've worked with</p>
            </div>
            <div class="clients-grid">
                @php
                    $clientData = [
                        'atif' => [
                            ['icon' => '🏢', 'name' => 'Corporate Firms', 'type' => 'Tax Advisory'],
                            ['icon' => '🏛️', 'name' => 'FBR Clients', 'type' => 'Compliance'],
                            ['icon' => '🏪', 'name' => 'Small Businesses', 'type' => 'Tax Planning'],
                            ['icon' => '💼', 'name' => 'Startups', 'type' => 'Legal Support'],
                            ['icon' => '🌐', 'name' => 'International Firms', 'type' => 'Tax Consultation'],
                            ['icon' => '🏭', 'name' => 'Manufacturing', 'type' => 'Audit Support']
                        ],
                        'waseem' => [
                            ['icon' => '🎯', 'name' => 'Marketing Agencies', 'type' => 'Digital Solutions'],
                            ['icon' => '🛍️', 'name' => 'E-commerce Brands', 'type' => 'Web Development'],
                            ['icon' => '📱', 'name' => 'Tech Startups', 'type' => 'App Development'],
                            ['icon' => '🎨', 'name' => 'Creative Studios', 'type' => 'Branding'],
                            ['icon' => '🌟', 'name' => 'Enterprises', 'type' => 'AI Solutions'],
                            ['icon' => '🚀', 'name' => 'SaaS Companies', 'type' => 'Full-Stack']
                        ],
                        'ans' => [
                            ['icon' => '💻', 'name' => 'Tech Companies', 'type' => 'Backend Development'],
                            ['icon' => '🏢', 'name' => 'Enterprises', 'type' => 'Laravel Apps'],
                            ['icon' => '🔐', 'name' => 'Security Firms', 'type' => 'Secure Systems'],
                            ['icon' => '📊', 'name' => 'Data Companies', 'type' => 'Database Design'],
                            ['icon' => '🌐', 'name' => 'Web Agencies', 'type' => 'API Development'],
                            ['icon' => '🚀', 'name' => 'Startups', 'type' => 'MVP Development']
                        ],
                        'nazim' => [
                            ['icon' => '🏢', 'name' => 'Corporate Offices', 'type' => 'Security Systems'],
                            ['icon' => '🏭', 'name' => 'Industrial Sites', 'type' => 'Automation'],
                            ['icon' => '🏪', 'name' => 'Retail Chains', 'type' => 'Network Setup'],
                            ['icon' => '🏠', 'name' => 'Residential', 'type' => 'Smart Systems'],
                            ['icon' => '🏫', 'name' => 'Institutions', 'type' => 'Infrastructure'],
                            ['icon' => '🏥', 'name' => 'Healthcare', 'type' => 'IT Support']
                        ]
                    ];
                    $clients = $clientData[$firstName] ?? [
                        ['icon' => '🏢', 'name' => 'Businesses', 'type' => 'Professional Services'],
                        ['icon' => '🌟', 'name' => 'Enterprises', 'type' => 'Consulting'],
                        ['icon' => '🚀', 'name' => 'Startups', 'type' => 'Advisory'],
                        ['icon' => '🏪', 'name' => 'SMEs', 'type' => 'Support'],
                        ['icon' => '🌐', 'name' => 'International', 'type' => 'Collaboration'],
                        ['icon' => '💼', 'name' => 'Corporates', 'type' => 'Partnership']
                    ];
                @endphp
                
                @foreach($clients as $index => $client)
                    <div class="client-card" data-aos="zoom-in" data-aos-delay="{{ $index * 80 }}">
                        <div class="client-logo">{{ $client['icon'] }}</div>
                        <div class="client-name">{{ $client['name'] }}</div>
                        <div class="client-type">{{ $client['type'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>
        
        <!-- Testimonials -->
        @if($profile->testimonials && $profile->testimonials->count() > 0)
            <section class="portfolio-section">
                <div class="section-header">
                    <h2 class="section-title">Client Testimonials</h2>
                    <p class="section-subtitle">What our clients say about working with us</p>
                </div>
                <div class="testimonials-grid">
                    @foreach($profile->testimonials->take(6) as $index => $testimonial)
                        <div class="testimonial-card" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <p class="testimonial-text">{{ $testimonial->content }}</p>
                            <div class="testimonial-stars">★★★★★</div>
                            <div class="testimonial-author">
                                <div class="testimonial-avatar">
                                    {{ substr($testimonial->author_name, 0, 1) }}
                                </div>
                                <div class="testimonial-author-info">
                                    <div class="testimonial-author-name">{{ $testimonial->author_name }}</div>
                                    <div class="testimonial-author-position">
                                        {{ $testimonial->author_organization ?: 'Satisfied Client' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        
        <!-- Achievements & Milestones Section -->
        <section class="portfolio-section" style="background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%); border-top: 4px solid #667eea; margin-top: 40px;">
            <div class="section-header">
                <h2 class="section-title" style="color: #1a1a2e;">🏆 Achievements & Impact</h2>
                <p class="section-subtitle">Key milestones and measurable success in our journey</p>
            </div>
            <div class="achievements-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-top: 30px;">
                @php
                    $achievements = [
                        'atif' => [
                            ['icon' => '📊', 'number' => '500+', 'label' => 'Tax Returns Filed', 'desc' => 'Successfully processed and filed'],
                            ['icon' => '💰', 'number' => 'PKR 50M+', 'label' => 'Tax Savings Generated', 'desc' => 'For clients through optimization'],
                            ['icon' => '🏆', 'number' => '98%', 'label' => 'Compliance Rate', 'desc' => 'FBR compliance success rate'],
                            ['icon' => '🌟', 'number' => '15+', 'label' => 'Years Experience', 'desc' => 'In tax advisory and consulting']
                        ],
                        'waseem' => [
                            ['icon' => '🎨', 'number' => '200+', 'label' => 'Creative Projects', 'desc' => 'Branding and design solutions'],
                            ['icon' => '📈', 'number' => '150%', 'label' => 'Avg ROI Increase', 'desc' => 'For marketing campaigns'],
                            ['icon' => '🚀', 'number' => '50+', 'label' => 'Digital Products', 'desc' => 'Websites and apps launched'],
                            ['icon' => '💡', 'number' => '1000+', 'label' => 'Ideas Implemented', 'desc' => 'Creative concepts brought to life']
                        ],
                        'ans' => [
                            ['icon' => '💻', 'number' => '300+', 'label' => 'Applications Built', 'desc' => 'Web and mobile solutions'],
                            ['icon' => '⚡', 'number' => '99.9%', 'label' => 'Uptime Achieved', 'desc' => 'System reliability record'],
                            ['icon' => '🔒', 'number' => '100%', 'label' => 'Security Compliance', 'desc' => 'Data protection standards'],
                            ['icon' => '🎯', 'number' => '24/7', 'label' => 'Support Available', 'desc' => 'Round-the-clock assistance']
                        ],
                        'nazim' => [
                            ['icon' => '🔧', 'number' => '1000+', 'label' => 'Systems Installed', 'desc' => 'Security and automation setups'],
                            ['icon' => '📡', 'number' => '500+', 'label' => 'Networks Configured', 'desc' => 'Infrastructure solutions'],
                            ['icon' => '⚡', 'number' => '99%', 'label' => 'Issue Resolution', 'desc' => 'First-call resolution rate'],
                            ['icon' => '🏆', 'number' => '10+', 'label' => 'Certifications', 'desc' => 'Industry certifications held']
                        ]
                    ];
                    $userAchievements = $achievements[$firstName] ?? [
                        ['icon' => '🎯', 'number' => '100%', 'label' => 'Client Satisfaction', 'desc' => 'Consistently exceeding expectations'],
                        ['icon' => '⚡', 'number' => '24/7', 'label' => 'Service Availability', 'desc' => 'Always here when you need us'],
                        ['icon' => '🌟', 'number' => '5★', 'label' => 'Quality Rating', 'desc' => 'Excellence in every project'],
                        ['icon' => '🚀', 'number' => '∞', 'label' => 'Growth Potential', 'desc' => 'Limitless possibilities ahead']
                    ];
                @endphp
                
                @foreach($userAchievements as $achievement)
                    <div class="achievement-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.1); text-align: center; transition: all 0.3s ease;" data-aos="fade-up">
                        <div class="achievement-icon" style="font-size: 3rem; margin-bottom: 15px;">{{ $achievement['icon'] }}</div>
                        <div class="achievement-number" style="font-size: 2.5rem; font-weight: 800; color: #667eea; margin-bottom: 8px;">{{ $achievement['number'] }}</div>
                        <div class="achievement-label" style="font-size: 1.1rem; font-weight: 700; color: #1a1a2e; margin-bottom: 8px;">{{ $achievement['label'] }}</div>
                        <div class="achievement-desc" style="font-size: 0.9rem; color: #666; line-height: 1.5;">{{ $achievement['desc'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>
        
        <!-- Call to Action Section -->
        <section class="portfolio-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-top: 40px; text-align: center;">
            <div style="padding: 50px 20px;">
                <h2 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 15px; color: white;">Ready to Work Together?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    Let's discuss how we can help bring your vision to life with our expertise and proven track record.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    @if($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" 
                           style="display: inline-flex; align-items: center; gap: 10px; background: #25D366; color: white; padding: 15px 30px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);">
                            <span style="font-size: 1.5rem;">💬</span>
                            <span>Start a Conversation</span>
                        </a>
                    @endif
                    <a href="mailto:{{ $profile->user->email }}" 
                       style="display: inline-flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.2); color: white; padding: 15px 30px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255, 255, 255, 0.3);">
                        <span style="font-size: 1.5rem;">✉️</span>
                        <span>Send Email</span>
                    </a>
                </div>
            </div>
        </section>
        
        <!-- Edit Options -->
        @if($canEdit || $canDelete)
            <section style="text-align: center; margin-top: 60px;">
                @if($canEdit)
                    <a href="{{ route('profiles.edit', $profile) }}" 
                       style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 35px; border-radius: 50px; font-weight: 700; text-decoration: none; margin: 0 10px;">
                        ✏️ Edit Portfolio
                    </a>
                @endif
                
                @if($canDelete)
                    <form action="{{ route('profiles.destroy', $profile) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this portfolio?');" 
                                style="background: #ef4444; color: white; padding: 15px 35px; border-radius: 50px; border: none; font-weight: 700; cursor: pointer; margin: 0 10px;">
                            🗑️ Delete Portfolio
                        </button>
                    </form>
                @endif
            </section>
        @endif
        
    </div>
</div>
    
    <!-- Portfolio Content Area -->
    <div class="profile-content">
        <div class="portfolio-grid">
            <!-- Left Column - Main Portfolio Content -->
            <div>
                <!-- About Section -->
                @if($profile->bio)
                    <div class="portfolio-section">
                        <h2 class="portfolio-section-title">About</h2>
                        <p style="font-size: 1.05rem; color: #555; line-height: 1.8;">{{ $profile->bio }}</p>
                    </div>
                @endif
                
                <!-- Specializations -->
                @if($profile->specializations && count($profile->specializations) > 0)
                    <div class="portfolio-section">
                        <h2 class="portfolio-section-title">Specializations</h2>
                        <div class="portfolio-tags">
                            @foreach($profile->specializations as $spec)
                                <span class="portfolio-tag">{{ $spec }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Experience Section -->
                @if($profile->experience_years)
                    <div class="portfolio-section">
                        <h2 class="portfolio-section-title">Professional Experience</h2>
                        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                            <div style="font-size: 1.15rem; font-weight: 700; color: #1a1a2e; margin-bottom: 5px;">
                                Senior Professional
                            </div>
                            <div style="color: #667eea; font-weight: 600; margin-bottom: 3px;">
                                {{ $profile->user->division->name }}
                            </div>
                            <div style="font-size: 0.9rem; color: #999; margin-bottom: 10px;">
                                {{ $profile->experience_years }}+ Years of Experience
                            </div>
                            <div style="color: #666; line-height: 1.6;">
                                @if($profile->specializations && count($profile->specializations) > 0)
                                    Specialized in {{ implode(', ', array_slice($profile->specializations, 0, 2)) }}. Brings extensive expertise and proven track record of delivering excellence.
                                @else
                                    Dedicated professional with extensive experience and proven expertise.
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Projects / Works Done -->
                @if($profile->specializations && count($profile->specializations) > 0)
                    <div class="portfolio-section">
                        <h2 class="portfolio-section-title">Notable Projects</h2>
                        <div style="color: #666; line-height: 1.8;">
                            <p>Experienced in delivering projects across:</p>
                            <div class="portfolio-tags" style="margin-top: 12px;">
                                @foreach($profile->specializations as $spec)
                                    <span class="portfolio-tag">{{ $spec }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Clientele / Partners -->
                <div class="portfolio-section">
                    <h2 class="portfolio-section-title">📊 Clientele & Partners</h2>
                    <div style="color: #666; line-height: 1.8;">
                        <p>Trusted by leading organizations and businesses for {{ $profile->experience_years }}+ years of expertise in {{ $profile->user->division->name }}.</p>
                        <p style="margin-top: 10px; font-size: 0.95rem; font-style: italic;">{{ $profile->user->name }} has successfully collaborated with diverse clients ranging from startups to established enterprises.</p>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Sidebar -->
            <div>
                <!-- Testimonials -->
                @if($profile->testimonials && $profile->testimonials->count() > 0)
                    <div class="portfolio-sidebar">
                        <div class="sidebar-heading">⭐ Testimonials</div>
                        <div style="text-align: left;">
                            @foreach($profile->testimonials->take(2) as $testimonial)
                                <div style="margin-bottom: 15px; padding: 12px; background: #f0f4ff; border-left: 3px solid #667eea; border-radius: 6px;">
                                    <div style="color: #555; font-size: 0.9rem; margin-bottom: 8px; line-height: 1.5; font-style: italic;">
                                        "{{ \Str::limit($testimonial->content, 150) }}"
                                    </div>
                                    <div style="color: #667eea; font-weight: 700; font-size: 0.85rem;">
                                        — {{ $testimonial->author_name }}
                                    </div>
                                    @if($testimonial->author_organization)
                                        <div style="color: #999; font-size: 0.8rem;">{{ $testimonial->author_organization }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Happy / Satisfied Customers -->
                <div class="portfolio-sidebar">
                    <div class="sidebar-heading">😊 Satisfied Clients</div>
                    <div style="text-align: center;">
                        <div style="font-size: 2.5rem; font-weight: 800; color: #667eea; margin: 15px 0;">
                            @php
                                $testimonialCount = $profile->testimonials()->approved()->count();
                                echo $testimonialCount > 0 ? $testimonialCount . '+' : '50+';
                            @endphp
                        </div>
                        <div style="color: #666; font-weight: 600;">Happy Clients</div>
                        <p style="color: #999; font-size: 0.9rem; margin-top: 10px;">Consistently delivering excellence and building long-term professional relationships.</p>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="portfolio-sidebar">
                    <div class="sidebar-heading">📞 Contact & Connect</div>
                    <div style="text-align: center;">
                        @if($profile->getWhatsAppNumber())
                            <div style="margin-bottom: 12px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                <div style="font-size: 0.8rem; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                    WhatsApp
                                </div>
                                <div>
                                    <a href="{{ $profile->getWhatsAppUrl() }}" target="_blank" 
                                       style="color: #667eea; text-decoration: none; font-weight: 600;">
                                        {{ $profile->getWhatsAppNumber() }}
                                    </a>
                                </div>
                            </div>
                        @endif
                        
                        <div style="margin-bottom: 12px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                            <div style="font-size: 0.8rem; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                Email
                            </div>
                            <div>
                                <a href="mailto:{{ $profile->user->email }}" style="color: #667eea; text-decoration: none; font-weight: 600;">
                                    {{ $profile->user->email }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Division Team -->
                @php
                    $divisionMembers = $profile->user->division->users()
                        ->with('profile')
                        ->active()
                        ->whereNot('id', $profile->user->id)
                        ->get();
                @endphp
                
                @if($divisionMembers->count() > 0)
                    <div class="portfolio-sidebar">
                        <div class="sidebar-heading">👥 Team Members</div>
                        <div style="text-align: center;">
                            @foreach($divisionMembers as $member)
                                <div style="margin-bottom: 10px;">
                                    <a href="{{ route('profiles.show', $member->profile) }}" 
                                       style="color: #667eea; text-decoration: none; font-weight: 600;">
                                        → {{ $member->name }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Full Width Testimonials Section -->
        @if($profile->testimonials && $profile->testimonials->count() > 0)
            <div class="portfolio-section" style="margin-bottom: 40px;">
                <h2 class="portfolio-section-title">Client Testimonials</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    @foreach($profile->testimonials->take(3) as $testimonial)
                        <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 20px; border-radius: 10px; border-left: 4px solid #667eea;">
                            <div style="color: #555; font-size: 0.95rem; margin-bottom: 12px; line-height: 1.6; font-style: italic;">
                                "{{ $testimonial->content }}"
                            </div>
                            <div style="color: #667eea; font-weight: 700; font-size: 0.9rem;">
                                — {{ $testimonial->author_name }}
                            </div>
                            @if($testimonial->author_organization)
                                <div style="color: #999; font-size: 0.8rem;">{{ $testimonial->author_organization }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Edit/Delete Options -->
        @if($canEdit || $canDelete)
            <div style="text-align: center; padding: 20px; background: white; border-radius: 12px;">
                @if($canEdit)
                    <a href="{{ route('profiles.edit', $profile) }}" class="btn-action btn-edit">
                        📝 Edit Profile Details
                    </a>
                @endif
                
                @if($canDelete)
                    <form action="{{ route('profiles.destroy', $profile) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this profile?');" 
                                style="background: #ef4444; color: white; padding: 10px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-left: 10px;">
                            🗑️ Delete Profile
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Particle Animation Script -->
<script>
    // Initialize particles for hero section
    const canvas = document.getElementById('particle-canvas');
    const ctx = canvas ? canvas.getContext('2d') : null;
    
    if (canvas && ctx) {
        // Get profession-specific colors
        const firstName = ('{{ strtolower(explode(' ', $profile->user->name)[0]) }}').toLowerCase();
        
        const particleColors = {
            'atif': ['#667eea', '#764ba2', '#f093fb'],      // Purple palette for Legal
            'waseem': ['#f093fb', '#667eea', '#ff6b6b'],    // Pink/Purple for Creative
            'ans': ['#667eea', '#20ba5a', '#0066cc'],       // Blue/Green for Backend
            'nazim': ['#764ba2', '#f093fb', '#667eea'],     // Purple/Pink for Systems
        };
        
        const colors = particleColors[firstName] || ['#667eea', '#764ba2', '#f093fb'];
        
        // Particle system
        const particles = [];
        const particleCount = 30;
        
        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.vx = (Math.random() - 0.5) * 2;
                this.vy = (Math.random() - 0.5) * 2;
                this.size = Math.random() * 3 + 1;
                this.color = colors[Math.floor(Math.random() * colors.length)];
                this.opacity = Math.random() * 0.5 + 0.3;
            }
            
            update() {
                this.x += this.vx;
                this.y += this.vy;
                
                // Wrap around edges
                if (this.x < 0) this.x = canvas.width;
                if (this.x > canvas.width) this.x = 0;
                if (this.y < 0) this.y = canvas.height;
                if (this.y > canvas.height) this.y = 0;
                
                // Fade in/out
                this.opacity += (Math.random() - 0.5) * 0.02;
                if (this.opacity > 0.8) this.opacity = 0.8;
                if (this.opacity < 0.1) this.opacity = 0.1;
            }
            
            draw() {
                ctx.fillStyle = this.color;
                ctx.globalAlpha = this.opacity;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        
        // Initialize particles
        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
        }
        
        // Resize canvas
        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // Animation loop
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.globalAlpha = 1;
            
            // Draw connections between nearby particles
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < 100) {
                        ctx.strokeStyle = particles[i].color;
                        ctx.globalAlpha = (1 - distance / 100) * 0.2;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            
            // Update and draw particles
            particles.forEach(particle => {
                particle.update();
                particle.draw();
            });
            
            requestAnimationFrame(animate);
        }
        
        animate();
    }
</script>

@endsection
