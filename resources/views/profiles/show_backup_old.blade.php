@extends('layouts.app')

@section('title', $profile->user->name . ' - Profile')

@section('content')

@php
    $firstName = strtolower(explode(' ', $profile->user->name)[0] ?? 'user');
    $imagePath = "/images/profiles/{$firstName}/banner.png";
@endphp

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --primary-color: #667eea;
        --secondary-color: #764ba2;
    }
    
    /* ===== FULL-WIDTH HERO WITH OVERLAY ===== */
    .profile-hero {
        position: relative;
        min-height: 700px;
        overflow: hidden;
        background: #000;
    }
    
    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .hero-background img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(102,126,234,0.5) 100%);
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        padding: 60px 20px;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 900;
        margin: 0 0 15px 0;
        letter-spacing: -2px;
        line-height: 1.1;
        text-shadow: 0 4px 20px rgba(0,0,0,0.5);
    }
    
    .hero-role {
        font-size: 1.2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 50px;
        opacity: 0.95;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    
    .hero-stats {
        display: flex;
        gap: 60px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .hero-stat-item {
        text-align: center;
    }
    
    .hero-stat-value {
        font-size: 2.8rem;
        font-weight: 900;
        display: block;
        margin-bottom: 8px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    
    .hero-stat-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        opacity: 0.9;
    }
    
    /* ===== INFO SECTION ===== */
    .info-section {
        background: #f8f9ff;
        padding: 80px 20px;
        position: relative;
        z-index: 5;
    }
    
    .info-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .bio-card {
        background: white;
        border-radius: 20px;
        padding: 60px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        margin-bottom: 80px;
        border-left: 5px solid var(--primary-color);
    }
    
    .bio-card h2 {
        font-size: 2rem;
        font-weight: 900;
        color: #1a1a2e;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    .bio-text {
        font-size: 1.1rem;
        line-height: 1.9;
        color: #4a5568;
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
    }
    
    /* ===== SKILLS SECTION ===== */
    .skills-section {
        margin-bottom: 80px;
    }
    
    .section-title {
        font-size: 2rem;
        font-weight: 900;
        text-align: center;
        margin-bottom: 60px;
        color: #1a1a2e;
    }
    
    .skills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1100px;
        margin: 0 auto;
    }
    
    .skill-item {
        background: white;
        border-radius: 16px;
        padding: 35px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border-top: 4px solid var(--primary-color);
    }
    
    .skill-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(102,126,234,0.15);
    }
    
    .skill-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .skill-percentage {
        color: var(--primary-color);
        font-size: 1rem;
        font-weight: 700;
    }
    
    .skill-bar-bg {
        height: 8px;
        background: #e8ecf1;
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
    
    /* ===== PROJECTS SECTION ===== */
    .projects-section {
        background: white;
        padding: 80px 20px;
        position: relative;
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
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 5px solid var(--primary-color);
    }
    
    .project-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 20px 50px rgba(102,126,234,0.2);
    }
    
    .project-icon {
        font-size: 3.5rem;
        padding: 40px 20px 0;
        text-align: center;
    }
    
    .project-content {
        padding: 20px 30px 30px;
    }
    
    .project-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 12px;
    }
    
    .project-description {
        font-size: 0.95rem;
        color: #4a5568;
        line-height: 1.6;
    }
    
    .slider-controls {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 40px;
    }
    
    .slider-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--primary-color);
        background: white;
        color: var(--primary-color);
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 900;
    }
    
    .slider-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }
    
    /* ===== SERVICES SECTION ===== */
    .services-section {
        background: #f8f9ff;
        padding: 80px 20px;
    }
    
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 40px;
        max-width: 1100px;
        margin: 0 auto;
    }
    
    .service-card {
        background: white;
        border-radius: 16px;
        padding: 50px 35px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }
    
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(102,126,234,0.2);
    }
    
    .service-icon {
        font-size: 3rem;
        margin-bottom: 25px;
    }
    
    .service-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 15px;
    }
    
    .service-description {
        color: #64748b;
        line-height: 1.8;
    }
    
    /* ===== TESTIMONIALS CAROUSEL ===== */
    .testimonials-section {
        background: white;
        padding: 80px 20px;
    }
    
    .testimonials-carousel {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        height: 400px;
    }
    
    .testimonial-card {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 20px;
        padding: 50px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.5s ease;
        pointer-events: none;
        border-left: 5px solid var(--primary-color);
    }
    
    .testimonial-card.active {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
    }
    
    .testimonial-text {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #4a5568;
        font-style: italic;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .testimonial-author {
        text-align: center;
        font-size: 1.05rem;
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
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 15px;
    }
    
    .cta-description {
        font-size: 1.15rem;
        margin-bottom: 40px;
        opacity: 0.95;
    }
    
    .cta-button {
        display: inline-block;
        padding: 18px 50px;
        background: white;
        color: var(--primary-color);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 900;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .profile-hero {
            min-height: 600px;
        }
        
        .hero-title {
            font-size: 2.8rem;
        }
        
        .hero-role {
            font-size: 1rem;
            margin-bottom: 40px;
        }
        
        .hero-stats {
            gap: 40px;
        }
        
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 50px;
        }
        
        .bio-card {
            padding: 40px;
        }
        
        .skills-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .project-card {
            flex: 0 0 300px;
        }
        
        .services-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
    
    @media (max-width: 768px) {
        .profile-hero {
            min-height: 500px;
        }
        
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-role {
            font-size: 0.9rem;
            margin-bottom: 30px;
        }
        
        .hero-stats {
            gap: 30px;
            flex-direction: column;
        }
        
        .hero-stat-value {
            font-size: 2rem;
        }
        
        .hero-stat-label {
            font-size: 0.8rem;
        }
        
        .info-section {
            padding: 60px 15px;
        }
        
        .bio-card {
            padding: 30px;
            margin-bottom: 60px;
        }
        
        .bio-card h2 {
            font-size: 1.5rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 40px;
        }
        
        .skills-grid {
            grid-template-columns: 1fr;
        }
        
        .project-card {
            flex: 0 0 280px;
        }
        
        .project-icon {
            font-size: 2.5rem;
        }
        
        .cta-title {
            font-size: 1.8rem;
        }
        
        .cta-description {
            font-size: 1rem;
        }
    }
</style>
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .hero-image-section {
            height: 400px;
        }
        
        .hero-content-section {
            padding: 40px;
        }
        
        .hero-title {
            font-size: 2.2rem;
        }
        
        .hero-stats-row {
            gap: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .profile-hero {
            padding: 50px 15px;
        }
        
        .hero-container {
            max-width: 100%;
        }
        
        .hero-image-section {
            height: 350px;
            margin-bottom: 40px;
            border-radius: 16px;
        }
        
        .hero-content-section {
            padding: 30px;
            border-radius: 16px;
        }
        
        .hero-title {
            font-size: 1.8rem;
        }
        
        .hero-role {
            font-size: 0.9rem;
            padding: 6px 15px;
        }
        
        .hero-bio-preview {
            font-size: 0.95rem;
        }
        
        .hero-stats-row {
            grid-template-columns: 1fr;
            gap: 15px;
            padding-top: 20px;
        }
        
        .hero-stat-number {
            font-size: 1.6rem;
        }
        
        .hero-stat-label {
            font-size: 0.75rem;
        }
    }
    
    .info-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .bio-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        margin-bottom: 50px;
        border: 1px solid rgba(102, 126, 234, 0.1);
    }
    
    .bio-card h2 {
        font-size: 1.8rem;
        font-weight: 900;
        color: #1a1a2e;
        margin-bottom: 1.2rem;
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
        font-size: 1.8rem;
        font-weight: 900;
        text-align: center;
        margin-bottom: 2.5rem;
        color: #1a1a2e;
    }\n    \n    .skills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .skill-item {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .skill-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2);
    }
    
    .skill-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .skill-percentage {
        color: var(--primary-color);
        font-size: 1rem;
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
        padding: 70px 20px;
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
        border-radius: 20px;
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
        font-size: 1.2rem;
        font-weight: 900;
        margin-bottom: 0.8rem;
    }
    
    .project-description {
        color: #64748b;
        line-height: 1.8;
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
        padding: 70px 20px;
        background: #f8f9fa;
    }
    
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .service-card {
        background: white;
        border-radius: 20px;
        padding: 35px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
    }
    
    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: var(--primary-gradient);
        transform: scaleX(0);
        transition: transform 0.5s ease;
    }
    
    .service-card:hover::before {
        transform: scaleX(1);
    }
    
    .service-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 25px 60px rgba(102, 126, 234, 0.25);
    }
    
    .service-icon {
        font-size: 3rem;
        margin-bottom: 1.2rem;
        filter: drop-shadow(0 4px 10px rgba(102, 126, 234, 0.3));
    }
    
    .service-name {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1a2e;
        margin-bottom: 0.8rem;
    }
    
    .service-description {
        color: #64748b;
        line-height: 1.8;
    }
    
    /* ===== TESTIMONIALS CAROUSEL ===== */
    .testimonials-section {
        background: white;
        padding: 70px 20px;
    }
    
    .testimonials-carousel {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        height: 400px;
    }
    
    .testimonial-card {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 24px;
        padding: 45px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.5s ease;
        pointer-events: none;
    }
    
    .testimonial-card.active {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
    }
    
    .testimonial-text {
        font-size: 1.1rem;
        line-height: 1.7;
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
        padding: 70px 20px;
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
        font-size: 2.2rem;
        font-weight: 900;
        margin-bottom: 1.2rem;
    }
    
    .cta-description {
        font-size: 1.1rem;
        margin-bottom: 2.5rem;
        opacity: 0.95;
    }
    
    .cta-button {
        display: inline-block;
        padding: 16px 45px;
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
    @media (max-width: 992px) {
        .profile-hero {
            grid-template-columns: 1fr;
            min-height: auto;
        }
        
        .hero-image-section {
            height: 400px;
        }
        
        .hero-content-section {
            padding: 50px 30px;
        }
        
        .hero-title {
            font-size: 2.2rem;
        }
        
        .hero-stats-row {
            gap: 30px;
        }
    }
    
    @media (max-width: 768px) {
        .profile-hero {
            grid-template-columns: 1fr;
        }
        
        .hero-image-section {
            height: 350px;
            order: -1;
        }
        
        .hero-content-section {
            padding: 40px 20px;
        }
        
        .hero-content-section::before {
            display: none;
        }
        
        .hero-title {
            font-size: 1.8rem;
        }
        
        .hero-role {
            font-size: 0.9rem;
            padding: 6px 15px;
        }
        
        .hero-bio-preview {
            font-size: 0.95rem;
        }
        
        .hero-stats-row {
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .hero-stat-number {
            font-size: 1.6rem;
        }
</style>

<!-- HERO SECTION -->
<div class="profile-hero">
    <div class="hero-background">
        <img src="{{ asset($imagePath) }}" alt="{{ $profile->user->name }}">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">{{ $profile->user->name }}</h1>
        <p class="hero-role">{{ $profile->title ?? 'Professional' }}</p>
        <div class="hero-stats">
            <div class="hero-stat-item">
                <span class="hero-stat-value">{{ $profile->experience_years ?? '10+' }}</span>
                <span class="hero-stat-label">Years Exp.</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-value">6</span>
                <span class="hero-stat-label">Projects</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-value">50+</span>
                <span class="hero-stat-label">Clients</span>
            </div>
        </div>
    </div>
</div>

<!-- INFO SECTION -->
<div class="info-section">
    <div class="info-container">
        <!-- Bio -->
        <div class="bio-card">
            <h2>About Me</h2>
            <p class="bio-text">
                {{ $profile->bio ?? 'Dedicated professional with extensive experience in delivering exceptional results and driving business success. Committed to excellence and continuous improvement in every project.' }}
            </p>
        </div>
        
        <!-- Skills -->
        <div class="skills-section">
            <h2 class="section-title">Expertise & Skills</h2>
            <div class="skills-grid">
                @php
                    $skills = explode(',', $profile->expertise_areas ?? 'Leadership,Strategy,Communication,Problem Solving,Team Management,Innovation');
                    $levels = [95, 90, 88, 92, 85, 87];
                @endphp
                @foreach($skills as $index => $skill)
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>{{ trim($skill) }}</span>
                            <span class="skill-percentage">{{ $levels[$index] ?? 90 }}%</span>
                        </div>
                        <div class="skill-bar-bg">
                            <div class="skill-bar-fill" style="--skill-width: {{ $levels[$index] ?? 90 }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- PROJECTS SLIDER -->
<div class="projects-section">
    <div class="projects-container">
        <h2 class="section-title" style="color: white;">Featured Projects</h2>
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
        <div class="slider-controls">
            <button class="slider-btn" onclick="scrollProjects('left')">‹</button>
            <button class="slider-btn" onclick="scrollProjects('right')">›</button>
        </div>
    </div>
</div>

<!-- SERVICES -->
<div class="services-section">
    <h2 class="section-title">Services I Offer</h2>
    <div class="services-grid">
        @php
            $serviceIcons = ['💼', '📊', '🎯', '🚀'];
            $serviceNames = ['Consultation', 'Strategy & Planning', 'Implementation', 'Support & Training'];
        @endphp
        @foreach($serviceNames as $index => $serviceName)
            <div class="service-card">
                <div class="service-icon">{{ $serviceIcons[$index] }}</div>
                <h3 class="service-name">{{ $serviceName }}</h3>
                <p class="service-description">
                    Expert {{ strtolower($serviceName) }} tailored to your specific needs and business goals.
                </p>
            </div>
        @endforeach
    </div>
</div>

<!-- TESTIMONIALS -->
<div class="testimonials-section">
    <h2 class="section-title">What Clients Say</h2>
    <div class="testimonials-carousel">
        @php
            $testimonials = [
                ["text" => "Outstanding professional who delivered exceptional results. Highly recommend for any project!", "author" => "— Sarah Johnson, CEO"],
                ["text" => "The expertise and dedication shown throughout our collaboration was truly remarkable.", "author" => "— Michael Chen, Director"],
                ["text" => "Transformed our business with innovative solutions and strategic insights.", "author" => "— Emily Parker, Founder"]
            ];
        @endphp
        @foreach($testimonials as $index => $testimonial)
            <div class="testimonial-card {{ $index === 0 ? 'active' : '' }}">
                <p class="testimonial-text">"{{ $testimonial['text'] }}"</p>
                <div class="testimonial-author">{{ $testimonial['author'] }}</div>
            </div>
        @endforeach
    </div>
    <div class="testimonial-dots">
        @foreach($testimonials as $index => $t)
            <span class="dot {{ $index === 0 ? 'active' : '' }}" onclick="showTestimonial({{ $index }})"></span>
        @endforeach
    </div>
</div>

<!-- CTA -->
<div class="cta-section">
    <div class="cta-content">
        <h2 class="cta-title">Let's Work Together</h2>
        <p class="cta-description">Ready to start your next project? Get in touch today!</p>
        <a href="{{ route('contact.create') }}" class="cta-button">Contact Me</a>
    </div>
</div>

<script>
    // Parallax effect (only for landscape banners)
    document.addEventListener('mousemove', (e) => {
        const banner = document.querySelector('.hero-banner');
        if (banner) {
            const xAxis = (window.innerWidth / 2 - e.pageX) / 50;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 50;
            banner.style.transform = `translate(${xAxis}px, ${yAxis}px) scale(1.1)`;
        }
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
        const scrollAmount = 380;
        slider.scrollBy({
            left: direction === 'left' ? -scrollAmount : scrollAmount,
            behavior: 'smooth'
        });
    }
    
    // Testimonials carousel
    let currentTestimonial = 0;
    const testimonials = document.querySelectorAll('.testimonial-card');
    const dots = document.querySelectorAll('.dot');
    
    function showTestimonial(index) {
        testimonials[currentTestimonial].classList.remove('active');
        dots[currentTestimonial].classList.remove('active');
        
        currentTestimonial = index;
        
        testimonials[currentTestimonial].classList.add('active');
        dots[currentTestimonial].classList.add('active');
    }
    
    // Auto-advance testimonials
    setInterval(() => {
        const next = (currentTestimonial + 1) % testimonials.length;
        showTestimonial(next);
    }, 5000);
</script>

@endsection
