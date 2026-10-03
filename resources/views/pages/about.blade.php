@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<style>
    .about-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 100px 20px;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.15) 0%, transparent 50%);
    }
    
    .about-hero-content {
        position: relative;
        z-index: 1;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .about-hero h1 {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 900;
        margin-bottom: 1rem;
        line-height: 1.1;
    }
    
    .about-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
        line-height: 1.6;
    }
    
    .about-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        padding: 80px 20px;
    }
    
    .story-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        margin-bottom: 100px;
    }
    
    .story-text h2 {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }
    
    .story-text p {
        font-size: 1.05rem;
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 1.5rem;
    }
    
    .story-image {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        color: white;
    }
    
    .story-image-icon {
        font-size: 5rem;
        opacity: 0.8;
        animation: float 3s ease-in-out infinite;
    }
    .story-image { min-height: 310px; display: flex; flex-direction: column; align-items: stretch; justify-content: center; gap: 12px; background: linear-gradient(145deg, #112b4b, #235e91); box-shadow: 0 20px 45px rgba(11,42,75,.15); }
    .story-discipline { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 17px 21px; border: 1px solid rgba(255,255,255,.24); background: rgba(255,255,255,.1); border-radius: 11px; color: #fff; font-size: 1rem; font-weight: 700; text-align: left; }
    .story-discipline span { color: #c5dff4; font-size: .82rem; font-weight: 500; }
    .about-mission { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; background: linear-gradient(135deg, #f8fafc, #eef4f9); padding: 52px; border-radius: 16px; margin-bottom: 85px; }
    .about-mission h3 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; }
    .about-mission p { color: #64748b; line-height: 1.8; }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 60px;
    }
    
    .value-card {
        background: white;
        padding: 40px;
        border-radius: 16px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .value-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.1);
        transform: translateY(-5px);
    }
    
    .value-icon {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
    }
    
    .value-card h3 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1rem;
    }
    
    .value-card p {
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }
    
    .cta-section {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        padding: 80px 20px;
        text-align: center;
        color: white;
    }

    .cta-section-content {
        max-width: 1000px;
        margin-inline: auto;
    }
    
    .cta-section h2 {
        font-size: 2rem;
        font-weight: 900;
        margin-bottom: 1rem;
    }
    
    .cta-section p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }
    
    .cta-button {
        display: inline-block;
        padding: 14px 40px;
        background: white;
        color: #3b82f6;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    
    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    @media (max-width: 768px) {
        .story-section {
            grid-template-columns: 1fr;
        }
        .story-section { gap: 30px; margin-bottom: 65px; }
        .about-mission { grid-template-columns: 1fr; gap: 28px; padding: 30px; }
        
        .values-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (prefers-reduced-motion: reduce) { .story-image-icon { animation: none; } .value-card, .cta-button { transition: none; } }
</style>

<!-- Hero Section -->
<div class="about-hero">
    <div class="about-hero-content">
        <h1>About Tasmiya Enterprises</h1>
        <p>Transforming businesses through specialized expertise, innovation, and professional excellence</p>
    </div>
</div>

<!-- Main Content -->
<div class="about-wrapper">
    <!-- Our Story -->
    <div class="story-section">
        <div class="story-text">
            <h2>Our Story</h2>
            <p>
                Tasmiya Enterprises was founded with a clear vision: to become a trusted partner for businesses seeking 
                specialized expertise across multiple domains. We understood that one-size-fits-all solutions don't work—
                every business has unique challenges requiring deep domain knowledge.
            </p>
            <p>
                Today, through our three specialized divisions, we serve clients with excellence in taxation, technology, 
                and technical support. Our team of certified professionals brings years of experience and a commitment to 
                delivering results that exceed expectations.
            </p>
        </div>
        <div class="story-image" aria-label="Our three areas of work">
            <div class="story-discipline">Taxation <span>Clarity and compliance</span></div>
            <div class="story-discipline">Technology <span>Practical digital work</span></div>
            <div class="story-discipline">Technical support <span>Reliable assistance</span></div>
        </div>
    </div>
    
    <!-- Mission & Vision -->
    <div class="about-mission">
            <div>
                <h3>Our Mission</h3>
                <p>
                    To empower businesses of all sizes with cutting-edge solutions and professional expertise that drive 
                    growth, ensure compliance, and unlock competitive advantage across taxation, digital transformation, 
                    and technical infrastructure.
                </p>
            </div>
            <div>
                <h3>Our Vision</h3>
                <p>
                    To be the leading multi-sector solutions provider, recognized for deep specialization, consistent 
                    reliability, and transformative impact. We envision a future where every business has access to 
                    world-class expertise tailored to their unique needs.
                </p>
            </div>
    </div>
    
    <!-- Core Values -->
    <div>
        <h2 style="font-size: 2.2rem; font-weight: 900; color: #0f172a; margin-bottom: 3rem; text-align: center;">Our Core Values</h2>
        
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🎯</div>
                <h3>Excellence</h3>
                <p>We pursue excellence in every project, delivering quality that exceeds expectations and sets industry standards.</p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>Integrity</h3>
                <p>We operate with honesty and transparency, building trust through ethical practices and reliable partnerships.</p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">💡</div>
                <h3>Innovation</h3>
                <p>We embrace new technologies and creative approaches to solve complex challenges and unlock opportunities.</p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">🌟</div>
                <h3>Client Success</h3>
                <p>Your success is our success. We align our efforts with your goals and deliver solutions that drive results.</p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">🚀</div>
                <h3>Growth</h3>
                <p>We believe in continuous improvement and scaling—for both our expertise and your business potential.</p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">⚡</div>
                <h3>Reliability</h3>
                <p>We deliver on our promises consistently, providing dependable support you can trust every single time.</p>
            </div>
        </div>
    </div>
    
</div>

<!-- CTA Section -->
<div class="cta-section">
    <div class="cta-section-content">
        <h2>Discover What We Can Do For You</h2>
        <p>Explore our services and meet our team of expert professionals</p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 2rem;">
            <a href="{{ route('services.index') }}" class="cta-button" style="background: white; color: #3b82f6;">Explore Services</a>
            <a href="{{ route('team.index') }}" class="cta-button" style="background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid white;">Meet the Team</a>
        </div>
    </div>
</div>

@endsection
