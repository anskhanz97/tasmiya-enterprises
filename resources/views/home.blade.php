@extends('layouts.app')

@section('title', 'Tasmiya Enterprises - Business Solutions')

@section('content')
<style>
    /* ===== 3D ANIMATED HERO SECTION ===== */
    .hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        color: white;
        padding: 140px 20px 160px;
        text-align: center;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Animated Background Particles */
    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.25) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.25) 0%, transparent 50%),
            radial-gradient(circle at 50% 20%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
        animation: gradientShift 15s ease infinite;
    }
    
    @keyframes gradientShift {
        0%, 100% { transform: translateX(0) translateY(0) scale(1); opacity: 1; }
        33% { transform: translateX(30px) translateY(-20px) scale(1.05); opacity: 0.8; }
        66% { transform: translateX(-20px) translateY(30px) scale(0.95); opacity: 0.9; }
    }
    
    /* Floating Geometric Shapes */
    .hero::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-image: 
            url('data:image/svg+xml,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="white" opacity="0.3"/></svg>'),
            url('data:image/svg+xml,<svg width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg"><polygon points="75,30 100,70 50,70" fill="rgba(59,130,246,0.1)"/></svg>'),
            url('data:image/svg+xml,<svg width="120" height="120" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg"><rect x="40" y="40" width="40" height="40" fill="rgba(168,85,247,0.1)" transform="rotate(45 60 60)"/></svg>');
        background-size: 100px 100px, 150px 150px, 120px 120px;
        background-position: 0% 0%, 50% 50%, 100% 100%;
        animation: floatShapes 30s linear infinite;
        pointer-events: none;
    }
    
    @keyframes floatShapes {
        0% { transform: translateY(0) rotate(0deg); }
        100% { transform: translateY(-100px) rotate(360deg); }
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 950px;
        margin: 0 auto;
        animation: heroFadeIn 1.2s cubic-bezier(0.4, 0, 0.2, 1) backwards;
    }
    
    @keyframes heroFadeIn {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .hero h1 {
        font-size: clamp(3rem, 8vw, 5.5rem);
        font-weight: 900;
        margin-bottom: 2rem;
        line-height: 1.1;
        letter-spacing: -3px;
        background: linear-gradient(135deg, #ffffff 0%, #93c5fd 50%, #c4b5fd 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: textShimmer 3s ease-in-out infinite;
        text-shadow: 0 0 80px rgba(59, 130, 246, 0.5);
    }
    
    @keyframes textShimmer {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .hero .subtitle {
        font-size: clamp(1.1rem, 2vw, 1.4rem);
        opacity: 0.95;
        margin-bottom: 3rem;
        line-height: 1.8;
        animation: heroFadeIn 1.2s cubic-bezier(0.4, 0, 0.2, 1) 0.2s backwards;
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        font-weight: 300;
        letter-spacing: 0.5px;
    }
    
    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 80px;
        margin: 50px 0;
        flex-wrap: wrap;
    }
    
    .stat-item {
        text-align: center;
        animation: heroFadeIn 1.2s cubic-bezier(0.4, 0, 0.2, 1) backwards;
    }
    
    .stat-item:nth-child(1) { animation-delay: 0.3s; }
    .stat-item:nth-child(2) { animation-delay: 0.4s; }
    .stat-item:nth-child(3) { animation-delay: 0.5s; }
    
    .stat-number {
        font-size: 4rem;
        font-weight: 900;
        display: block;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
    }

    
    .hero-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: heroFadeIn 1.2s cubic-bezier(0.4, 0, 0.2, 1) 0.6s backwards;
    }
    
    .btn-hero {
        padding: 18px 45px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
        position: relative;
        overflow: hidden;
        border: none;
    }
    
    .btn-hero::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-hero:hover::before {
        width: 400px;
        height: 400px;
    }
    
    .btn-primary-hero {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        box-shadow: 0 10px 40px rgba(59, 130, 246, 0.4);
    }
    
    .btn-primary-hero:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 20px 60px rgba(59, 130, 246, 0.5);
    }
    
    .btn-secondary-hero {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }
    
    .btn-secondary-hero:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: white;
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 20px 60px rgba(255, 255, 255, 0.2);
    }
    
    /* Scroll Indicator */
    .scroll-indicator {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        animation: bounce 2s infinite;
        z-index: 10;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
        40% { transform: translateX(-50%) translateY(-20px); }
        60% { transform: translateX(-50%) translateY(-10px); }
    }
    
    .scroll-indicator svg {
        width: 30px;
        height: 30px;
        opacity: 0.7;
    }

    
    /* Content Wrapper */
    .content-hub {
        max-width: 1300px;
        margin: 0 auto;
        padding: 100px 20px;
        position: relative;
    }
    
    /* Section */
    .section {
        margin-bottom: 140px;
        opacity: 0;
        transform: translateY(50px);
        animation: fadeInSection 0.8s ease-out forwards;
    }
    
    @keyframes fadeInSection {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 70px;
        position: relative;
    }
    
    .section-header::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 6px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        border-radius: 3px;
    }
    
    .section-header h2 {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 1.5rem;
        letter-spacing: -2px;
        position: relative;
        display: inline-block;
    }
    
    /* Team Section - Dark Gradient Background */
    .team-section {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e293b 100%);
        padding: 100px 20px;
        margin-left: -20px;
        margin-right: -20px;
        position: relative;
        overflow: hidden;
    }
    
    .team-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 85% 30%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 50% 80%, rgba(168, 85, 247, 0.1) 0%, transparent 50%);
        animation: teamGradientShift 20s ease infinite;
        pointer-events: none;
    }
    
    @keyframes teamGradientShift {
        0%, 100% { opacity: 1; transform: scale(1) rotate(0deg); }
        50% { opacity: 0.8; transform: scale(1.1) rotate(5deg); }
    }
    
    .team-section::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: teamDotPattern 30s linear infinite;
        pointer-events: none;
    }
    
    @keyframes teamDotPattern {
        0% { background-position: 0 0; }
        100% { background-position: 50px 50px; }
    }
    
    .team-section .section-header h2 {
        color: white;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }
    
    .team-section .section-header p {
        color: #cbd5e1;
    }
    
    .team-section .section-header::before {
        background: linear-gradient(90deg, #60a5fa, #a78bfa);
        box-shadow: 0 0 20px rgba(96, 165, 250, 0.5);
    }
    
    .section-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, transparent);
        border-radius: 2px;
    }
    
    .section-header p {
        font-size: 1.2rem;
        color: #64748b;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.8;
    }

    
    /* Why Choose Grid - 3D Cards */
    .why-choose-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 40px;
        margin-bottom: 60px;
    }
    
    .why-card {
        background: white;
        padding: 50px 40px;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        text-align: center;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .why-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .why-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.2);
    }
    
    .why-card:hover::before {
        opacity: 1;
    }
    
    .why-card-icon {
        font-size: 4rem;
        margin-bottom: 2rem;
        display: inline-block;
        transition: transform 0.4s ease;
        position: relative;
        z-index: 2;
    }
    
    .why-card:hover .why-card-icon {
        transform: scale(1.15);
    }
    
    .why-card h3 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1.2rem;
        position: relative;
        z-index: 2;
        transition: color 0.3s ease;
    }
    
    .why-card:hover h3 {
        color: #3b82f6;
    }
    
    .why-card p {
        color: #64748b;
        line-height: 1.8;
        margin: 0;
        font-size: 1.05rem;
        position: relative;
        z-index: 2;
        transition: color 0.3s ease;
    }
    
    .why-card:hover p {
        color: #475569;
    }

    
    .learn-more {
        display: inline-block;
        margin-top: 2rem;
        padding: 12px 28px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #3b82f6;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 2;
        overflow: hidden;
    }
    
    .learn-more::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .learn-more:hover::before {
        left: 100%;
    }
    
    .learn-more:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }
    
    /* Featured Items Grid - 3D Perspective Cards */
    .featured-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 35px;
    }
    
    .featured-item {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    
    .featured-item::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .featured-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.25);
    }
    
    .featured-item:hover::after {
        opacity: 1;
    }
    
    .featured-item-header {
        height: 180px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .featured-item-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
        animation: rotate 10s linear infinite;
    }
    
    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .featured-item:hover .featured-item-header {
        transform: scale(1.05);
    }
    
    .featured-item-body {
        padding: 30px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 2;
    }
    
    .featured-item h3 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1rem;
        transition: color 0.3s ease;
    }
    
    .featured-item:hover h3 {
        color: #3b82f6;
    }
    
    .featured-item p {
        color: #64748b;
        font-size: 1rem;
        line-height: 1.7;
        flex-grow: 1;
        margin: 0;
    }
    
    /* Team Member Cards - Special Styling */
    .team-member-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        position: relative;
    }
    
    .team-member-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(59, 130, 246, 0.3);
        text-decoration: none;
    }
    
    .team-image-wrapper {
        position: relative;
        height: 280px;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .team-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .team-member-card:hover .team-image-wrapper img {
        transform: scale(1.1);
    }
    
    .team-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .team-info {
        padding: 30px;
        text-align: center;
    }
    
    .team-info h3 {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }
    
    .team-info .role {
        font-size: 0.95rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .team-info .division-name {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.5rem;
    }
    
    .team-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        font-weight: 900;
        color: white;
    }

    /* ===== 3D CYLINDRICAL CAROUSEL ===== */
    .team-carousel-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 30px;
        padding: 60px 20px;
        perspective: 1500px;
        z-index: 2;
    }

    .carousel-container {
        position: relative;
        width: 100%;
        max-width: 900px;
        height: 550px;
        perspective: 1200px;
        z-index: 10;
    }

    .carousel-viewport {
        width: 100%;
        height: 100%;
        overflow: visible;
        border-radius: 24px;
    }

    .carousel-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .carousel-item {
        position: absolute;
        width: 100%;
        max-width: 350px;
        height: 480px;
        left: 50%;
        top: 50%;
        transform-style: preserve-3d;
        opacity: 0;
        pointer-events: none;
        transition: all 0.8s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .carousel-item.active {
        opacity: 1;
        pointer-events: auto;
        transform: translate(-50%, -50%) translateZ(200px) rotateY(0deg) scale(1);
        z-index: 10;
    }

    .carousel-item.prev {
        opacity: 0.7;
        pointer-events: auto;
        transform: translate(-50%, -50%) translateX(-250px) translateZ(-50px) rotateY(35deg) scale(0.85);
        z-index: 5;
    }

    .carousel-item.next {
        opacity: 0.7;
        pointer-events: auto;
        transform: translate(-50%, -50%) translateX(250px) translateZ(-50px) rotateY(-35deg) scale(0.85);
        z-index: 5;
    }

    .carousel-item.hidden {
        transform: translate(-50%, -50%) translateZ(-300px) scale(0.5);
        z-index: 0;
        opacity: 0;
    }

    .carousel-card {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        transform-style: preserve-3d;
        position: relative;
    }

    /* Team member card enhancements */
    .team-member-card {
        position: relative;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
    }

    /* Navigation Buttons */
    .carousel-btn {
        position: relative;
        width: 55px;
        height: 55px;
        border: 2px solid #e2e8f0;
        background: white;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 1.5rem;
        transition: all 0.3s ease;
        z-index: 20;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .carousel-btn:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        border-color: #3b82f6;
        transform: scale(1.1);
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
    }

    .carousel-btn:active {
        transform: scale(0.95);
    }

    .carousel-btn-left,
    .carousel-btn-right {
        animation: pulseButton 2s ease-in-out infinite;
    }

    @keyframes pulseButton {
        0%, 100% { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        50% { box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3); }
    }

    /* Flashy Visual Effects */
    .carousel-item.active .team-member-card {
        box-shadow: 
            0 0 60px rgba(59, 130, 246, 0.4),
            0 0 100px rgba(139, 92, 246, 0.2),
            0 20px 60px rgba(0, 0, 0, 0.3);
        animation: cardGlow 3s ease-in-out infinite;
    }

    @keyframes cardGlow {
        0%, 100% {
            box-shadow: 
                0 0 60px rgba(59, 130, 246, 0.4),
                0 0 100px rgba(139, 92, 246, 0.2),
                0 20px 60px rgba(0, 0, 0, 0.3);
        }
        50% {
            box-shadow: 
                0 0 80px rgba(59, 130, 246, 0.6),
                0 0 130px rgba(139, 92, 246, 0.4),
                0 25px 70px rgba(0, 0, 0, 0.4);
        }
    }

    .carousel-item.active .team-member-card::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899, #f59e0b);
        border-radius: 24px;
        z-index: -1;
        opacity: 0;
        filter: blur(20px);
        animation: borderGlow 3s ease-in-out infinite;
    }

    @keyframes borderGlow {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.6; }
    }

    /* Sparkle Effect on Active Card */
    .carousel-item.active .team-image-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(circle, rgba(255, 255, 255, 0.8) 1px, transparent 1px),
            radial-gradient(circle, rgba(255, 255, 255, 0.5) 1px, transparent 1px);
        background-size: 50px 50px, 80px 80px;
        background-position: 0 0, 40px 40px;
        animation: sparkle 4s linear infinite;
        pointer-events: none;
    }

    @keyframes sparkle {
        0% { opacity: 0; transform: scale(1); }
        50% { opacity: 1; }
        100% { opacity: 0; transform: scale(1.2); }
    }

    /* Floating particles background */
    .team-carousel-wrapper::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle, rgba(59, 130, 246, 0.3) 2px, transparent 2px),
            radial-gradient(circle, rgba(139, 92, 246, 0.2) 1px, transparent 1px),
            radial-gradient(circle, rgba(168, 85, 247, 0.2) 1.5px, transparent 1.5px);
        background-size: 100px 100px, 150px 150px, 80px 80px;
        background-position: 0 0, 50px 50px, 25px 25px;
        animation: floatingParticles 20s linear infinite;
        opacity: 0.4;
        pointer-events: none;
        z-index: 1;
    }

    @keyframes floatingParticles {
        0% { transform: translateY(0); }
        100% { transform: translateY(-100px); }
    }

    /* Enhanced hover effect on side cards */
    .carousel-item.prev .team-member-card,
    .carousel-item.next .team-member-card {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .carousel-item.prev .team-member-card:hover,
    .carousel-item.next .team-member-card:hover {
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.3);
        transform: scale(1.05);
    }

    /* Glow effect on buttons */
    .carousel-btn::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        opacity: 0;
        filter: blur(15px);
        transition: opacity 0.3s ease;
        z-index: -1;
    }

    .carousel-btn:hover::before {
        opacity: 0.6;
        animation: buttonGlow 1.5s ease-in-out infinite;
    }

    @keyframes buttonGlow {
        0%, 100% { transform: scale(1); opacity: 0.6; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    @media (max-width: 768px) {
        .team-carousel-wrapper {
            gap: 15px;
            padding: 40px 10px;
        }

        .carousel-container {
            max-width: 100%;
            height: 450px;
        }

        .carousel-item {
            max-width: 280px;
            height: 400px;
        }

        .carousel-btn {
            width: 45px;
            height: 45px;
        }

        .carousel-item.active {
            transform: translate(-50%, -50%) translateZ(150px) rotateY(0deg) scale(1);
        }

        .carousel-item.prev {
            transform: translate(-50%, -50%) translateX(-180px) translateZ(-80px) rotateY(40deg) scale(0.75);
            opacity: 0.5;
        }

        .carousel-item.next {
            transform: translate(-50%, -50%) translateX(180px) translateZ(-80px) rotateY(-40deg) scale(0.75);
            opacity: 0.5;
        }
    }

    @media (max-width: 480px) {
        .carousel-container {
            height: 400px;
        }

        .carousel-item {
            max-width: 260px;
            height: 380px;
        }

        .carousel-item.prev,
        .carousel-item.next {
            opacity: 0.3;
            transform: translate(-50%, -50%) translateX(-150px) translateZ(-100px) scale(0.7);
        }

        .carousel-item.next {
            transform: translate(-50%, -50%) translateX(150px) translateZ(-100px) scale(0.7);
        }
    }
    
    .view-all {
        display: inline-block;
        margin-top: 1.2rem;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 2;
    }
    
    .view-all::after {
        content: '→';
        display: inline-block;
        margin-left: 5px;
        transition: transform 0.3s ease;
    }
    
    .view-all:hover {
        color: #1d4ed8;
    }
    
    .view-all:hover::after {
        transform: translateX(5px);
    }
    
    /* CTA Section - Amazing Gradient Animation */
    .cta-large {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        background-size: 200% 200%;
        color: white;
        padding: 100px 40px;
        border-radius: 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: gradientMove 10s ease infinite;
    }
    
    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .cta-large::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        animation: rotateGlow 15s linear infinite;
    }
    
    @keyframes rotateGlow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .cta-large > * {
        position: relative;
        z-index: 2;
    }
    
    .cta-large h2 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 900;
        margin-bottom: 1.5rem;
        letter-spacing: -1px;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    
    .cta-large p {
        font-size: 1.2rem;
        opacity: 0.95;
        margin-bottom: 2.5rem;
        line-height: 1.7;
    }
    
    .cta-button {
        display: inline-block;
        padding: 18px 50px;
        background: white;
        color: #667eea;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .cta-button::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
    }
    
    .cta-button:hover::before {
        width: 400px;
        height: 400px;
    }
    
    .cta-button span {
        position: relative;
        z-index: 2;
        transition: color 0.3s ease;
    }
    
    .cta-button:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    .cta-button:hover span {
        color: white;
    }

    
    /* Responsive Design */
    @media (max-width: 768px) {
        .hero {
            padding: 100px 20px 120px;
            min-height: 90vh;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            letter-spacing: -1px;
        }
        
        .hero .subtitle {
            font-size: 1.1rem;
        }
        
        .hero-stats {
            gap: 40px;
        }
        
        .stat-number {
            font-size: 3rem;
        }
        
        .hero-buttons {
            flex-direction: column;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .btn-hero {
            width: 100%;
        }
        
        .why-choose-grid {
            grid-template-columns: 1fr;
        }
        
        .featured-grid {
            grid-template-columns: 1fr;
        }
        
        .cta-large {
            padding: 60px 30px;
        }
        
        .section {
            margin-bottom: 80px;
        }
    }
    
    @media (max-width: 480px) {
        .hero-stats {
            gap: 30px;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }
    }
    
    /* Glassmorphism Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    /* Pulse Animation for Important Elements */
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }
        50% {
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.8);
        }
    }
    
    /* Mouse Cursor Effect */
    .cursor-dot {
        position: fixed;
        width: 8px;
        height: 8px;
        background: #3b82f6;
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        transition: transform 0.2s ease;
        mix-blend-mode: difference;
    }
    
    .cursor-outline {
        position: fixed;
        width: 40px;
        height: 40px;
        border: 2px solid #3b82f6;
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        transition: all 0.15s ease-out;
        mix-blend-mode: difference;
    }
    
    /* Floating Animation for Icons */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }
    
    .why-card-icon {
        animation: float 3s ease-in-out infinite;
    }
    
    .why-card:nth-child(1) .why-card-icon { animation-delay: 0s; }
    .why-card:nth-child(2) .why-card-icon { animation-delay: 0.3s; }
    .why-card:nth-child(3) .why-card-icon { animation-delay: 0.6s; }
    
    /* ===== CLIENTS SECTION - ANIMATED SLIDER ===== */
    .clients-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 30px;
        padding: 80px 20px;
        position: relative;
        overflow: hidden;
    }
    
    .clients-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 50%, rgba(168, 85, 247, 0.05) 0%, transparent 50%);
        pointer-events: none;
    }
    
    .clients-slider-wrapper {
        overflow: hidden;
        position: relative;
        margin-top: 60px;
    }
    
    .clients-slider {
        display: flex;
        animation: slideClients 22s linear infinite;
        gap: 60px;
        align-items: center;
        width: max-content;
        will-change: transform;
    }
    
    .clients-slider:hover {
        animation-play-state: paused;
    }
    
    @keyframes slideClients {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }
    
    .client-logo {
        flex-shrink: 0;
        width: 200px;
        height: 120px;
        background: white;
        border-radius: 20px;
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
    }
    
    .client-logo::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
        transition: transform 0.6s ease;
    }
    
    .client-logo:hover::before {
        transform: translateX(100%) translateY(100%) rotate(45deg);
    }
    
    .client-logo:hover {
        transform: translateY(-10px) scale(1.08);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.3);
    }
    
    .client-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(100%);
        transition: filter 0.4s ease, transform 0.4s ease;
        position: relative;
        z-index: 2;
    }
    
    .client-logo:hover img {
        filter: grayscale(0%);
        transform: scale(1.1);
    }
    
    /* Duplicate clients for infinite scroll */
    @media (min-width: 768px) {
        .clients-slider::after {
            content: '';
            position: absolute;
            left: 100%;
            top: 0;
            width: 100%;
            height: 100%;
        }
    }
    
    /* Mobile - Stack instead of infinite scroll */
    @media (max-width: 767px) {
        .clients-slider {
            flex-wrap: wrap;
            justify-content: center;
            animation: none;
            gap: 30px;
        }
        
        .client-logo {
            width: 160px;
            height: 100px;
        }
    }

</style>

<!-- HERO SECTION -->
<div class="hero">
    <!-- Custom Cursor -->
    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>
    
    <div class="hero-content">
        <h1>Transform Your Business</h1>
        <p class="subtitle">Expert solutions across taxation, technology, and technical support. We empower businesses of all sizes to achieve their full potential with innovative strategies and dedicated service.</p>
        
        <!-- Hero Stats -->
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number" data-target="15">0</span>
                <span class="stat-label">Years Experience</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="500">0</span>
                <span class="stat-label">Happy Clients</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="3">0</span>
                <span class="stat-label">Divisions</span>
            </div>
        </div>
        
        <div class="hero-buttons">
            <a href="{{ route('services.index') }}" class="btn-hero btn-primary-hero">Explore Services</a>
            <a href="{{ route('contact.create') }}" class="btn-hero btn-secondary-hero">Get in Touch</a>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="scroll-indicator">
        <svg fill="white" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
        </svg>
    </div>
</div>

<!-- CONTENT HUB -->
<div class="content-hub">
    <!-- WHY CHOOSE TASMIYA -->
    <div class="section">
        <div class="section-header">
            <h2>Why Choose Tasmiya Enterprises?</h2>
            <p>Three specialized divisions working together to deliver comprehensive solutions</p>
        </div>
        
        <div class="why-choose-grid">
            @php
                $divisions = \App\Models\Division::all();
            @endphp
            
            @foreach($divisions as $division)
                @php
                    $themeColors = $division->getThemeColors();
                @endphp
                <div class="why-card" style="border-top: 4px solid {{ $themeColors['primary'] }};">
                    <div class="why-card-icon">{{ $division->icon_path }}</div>
                    <h3 style="color: {{ $themeColors['primary'] }};">{{ $division->name }}</h3>
                    <p>{{ $division->description }}</p>
                    <a href="{{ route('team.index') }}" class="learn-more">Meet the Team →</a>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- FEATURED SERVICES -->
    <div class="section">
        <div class="section-header">
            <h2>Our Services</h2>
            <p>Discover the range of professional services we offer</p>
        </div>
        
        <div class="featured-grid">
            @php
                // Get 1 service from FBR Taxation
                $fbrService = \App\Models\Service::with('division')
                    ->whereHas('division', fn($q) => $q->where('slug', 'fbr-taxation'))
                    ->inRandomOrder()
                    ->first();
                
                // Get 1 service from Technical Support
                $techService = \App\Models\Service::with('division')
                    ->whereHas('division', fn($q) => $q->where('slug', 'tech-support'))
                    ->inRandomOrder()
                    ->first();
                
                // Get 2 services from IT & Digital
                $itServices = \App\Models\Service::with('division')
                    ->whereHas('division', fn($q) => $q->where('slug', 'it-digital'))
                    ->inRandomOrder()
                    ->limit(2)
                    ->get();
                
                // Combine all services
                $featuredServices = collect([$fbrService, $techService])
                    ->merge($itServices)
                    ->filter(); // Remove nulls if any division has no services
            @endphp
            
            @forelse($featuredServices as $service)
                <div class="featured-item">
                    <div class="featured-item-header">💼</div>
                    <div class="featured-item-body">
                        <h3>{{ $service->name }}</h3>
                        <p>{{ Str::limit($service->description, 80) }}</p>
                        <a href="{{ route('services.show', $service) }}" class="view-all">View Details →</a>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center col-12">Services coming soon</p>
            @endforelse
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('services.index') }}" style="display: inline-block; padding: 12px 30px; background: #f1f5f9; color: #3b82f6; text-decoration: none; border-radius: 8px; font-weight: 600;">View All Services →</a>
        </div>
    </div>
    
    <!-- MEET THE TEAM -->
    <div class="section team-section">
        <div class="section-header">
            <h2>Meet Our Team</h2>
            <p>Expert professionals dedicated to your success</p>
        </div>
        
        <!-- 3D CYLINDRICAL CAROUSEL -->
        <div class="team-carousel-wrapper">
            <button class="carousel-btn carousel-btn-left" onclick="rotateCarousel(-1)" aria-label="Previous team member">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            
            <div class="carousel-container" id="teamCarousel">
                <div class="carousel-viewport">
                    <div class="carousel-inner" id="carouselInner">
                        @php
                            $featuredTeam = \App\Models\Profile::with('user.division')
                                ->visible()
                                ->limit(6)
                                ->get();
                        @endphp
                        
                        @forelse($featuredTeam as $index => $profile)
                            @php
                                $divisionColors = $profile->user->division->getThemeColors();
                                $imageUrl = $profile->getImageUrl();
                                $hasRealImage = $profile->hasImage() || file_exists(public_path("/images/profiles/" . strtolower(explode(' ', $profile->user->name)[0]) . "/image.png"));
                            @endphp
                            <div class="carousel-item" data-index="{{ $index }}" style="--item-index: {{ $index }};">
                                <a href="{{ route('profiles.show', $profile) }}" class="team-member-card carousel-card">
                                    <div class="team-image-wrapper" style="background: {{ $divisionColors['gradient'] }};">
                                        @if($hasRealImage)
                                            <img src="{{ $imageUrl }}" alt="{{ $profile->user->name }}">
                                        @else
                                            <div class="team-placeholder">
                                                {{ strtoupper(substr($profile->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="team-info">
                                        <h3>{{ $profile->user->name }}</h3>
                                        <p class="role" style="display: inline-block; background: {{ $divisionColors['primary'] }}15; color: {{ $divisionColors['primary'] }}; font-weight: 700; padding: 8px 20px; border-radius: 20px; border: 2px solid {{ $divisionColors['primary'] }}30;">{{ $profile->user->division->name }}</p>
                                        <div class="division-name" style="color: {{ $divisionColors['primary'] }};">
                                            View Profile →
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="text-muted text-center col-12">Team profiles coming soon</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <button class="carousel-btn carousel-btn-right" onclick="rotateCarousel(1)" aria-label="Next team member">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- OUR CLIENTS -->
    <div class="section clients-section">
        <div class="section-header">
            <h2>Our Clients</h2>
            <p>Trusted by leading businesses and organizations</p>
        </div>
        
        <div class="clients-slider-wrapper">
            <div class="clients-slider">
                @php
                    $clients = [
                        ['name' => 'IT Lifee', 'logo' => '/images/clients/itlifee.svg', 'url' => 'https://itlifee.net'],
                        ['name' => 'Bang.pk', 'logo' => '/images/clients/bang.svg', 'url' => 'https://bang.pk'],
                        ['name' => 'Masalaa Magic', 'logo' => '/images/clients/masalaa-magic.svg', 'url' => 'https://masalaamagic.com'],
                        ['name' => 'Allied Bahrain', 'logo' => '/images/clients/allied-bahrain.svg', 'url' => 'https://alliedbahrain.com/'],
                        ['name' => 'Techno One', 'logo' => '/images/clients/techno-one.svg', 'url' => 'https://www.technoone.pk/'],
                        ['name' => 'MSL', 'logo' => '/images/clients/msl.svg', 'url' => null],
                        ['name' => 'Fast Security', 'logo' => '/images/clients/fast-security.svg', 'url' => 'https://fastsec.itlifee.net'],
                        ['name' => 'DeSOM', 'logo' => '/images/clients/desom.svg', 'url' => null],
                        ['name' => 'Shalimar Plastic', 'logo' => '/images/clients/shalimar-plastic.svg', 'url' => 'https://erp.itlifee.net'],
                    ];

                    $clients = array_slice($clients, 0, 9);
                @endphp
                
                {{-- Render clients twice for seamless infinite scroll --}}
                @for($i = 0; $i < 2; $i++)
                    @foreach($clients as $client)
                        @if($client['url'])
                            <a href="{{ $client['url'] }}" target="_blank" class="client-logo" title="{{ $client['name'] }}">
                                <img src="{{ $client['logo'] }}" alt="{{ $client['name'] }}" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22100%22%3E%3Crect fill=%22%23f1f5f9%22 width=%22200%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%2394a3b8%22%3E{{ $client['name'] }}%3C/text%3E%3C/svg%3E';">
                            </a>
                        @else
                            <div class="client-logo" title="{{ $client['name'] }}">
                                <img src="{{ $client['logo'] }}" alt="{{ $client['name'] }}" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22100%22%3E%3Crect fill=%22%23f1f5f9%22 width=%22200%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%2394a3b8%22%3E{{ $client['name'] }}%3C/text%3E%3C/svg%3E';">
                            </div>
                        @endif
                    @endforeach
                @endfor
            </div>
        </div>
    </div>
    
    <!-- CTA SECTION -->
    <div class="section">
        <div class="cta-large">
            <h2>Ready to Transform Your Business?</h2>
            <p>Let's discuss how our expertise can help you achieve your goals</p>
            <a href="{{ route('contact.create') }}" class="cta-button"><span>Start Your Journey</span></a>
        </div>
    </div>
</div>

<script>
// Animated Counter for Hero Stats
document.addEventListener('DOMContentLoaded', function() {
    const stats = document.querySelectorAll('.stat-number');
    
    // Custom Cursor Effect
    const cursorDot = document.querySelector('.cursor-dot');
    const cursorOutline = document.querySelector('.cursor-outline');
    
    if (cursorDot && cursorOutline) {
        let mouseX = 0, mouseY = 0;
        let outlineX = 0, outlineY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            
            cursorDot.style.left = mouseX + 'px';
            cursorDot.style.top = mouseY + 'px';
        });
        
        // Smooth follow effect for outline
        function animateOutline() {
            outlineX += (mouseX - outlineX) * 0.15;
            outlineY += (mouseY - outlineY) * 0.15;
            
            cursorOutline.style.left = outlineX + 'px';
            cursorOutline.style.top = outlineY + 'px';
            
            requestAnimationFrame(animateOutline);
        }
        animateOutline();
        
        // Scale up on hover
        const interactiveElements = document.querySelectorAll('a, button, .btn-hero, .why-card, .featured-item');
        interactiveElements.forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursorDot.style.transform = 'scale(2)';
                cursorOutline.style.transform = 'scale(1.5)';
            });
            el.addEventListener('mouseleave', () => {
                cursorDot.style.transform = 'scale(1)';
                cursorOutline.style.transform = 'scale(1)';
            });
        });
    }
    
    const animateCounter = (element) => {
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current) + '+';
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target + '+';
            }
        };
        
        updateCounter();
    };
    
    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.classList.contains('stat-number') && entry.target.textContent === '0') {
                    animateCounter(entry.target);
                }
                if (entry.target.classList.contains('section')) {
                    entry.target.style.animation = 'fadeInSection 0.8s ease-out forwards';
                }
            }
        });
    }, { threshold: 0.1 });
    
    // Observe stat numbers
    stats.forEach(stat => observer.observe(stat));
    
    // Observe sections for scroll animations
    document.querySelectorAll('.section').forEach((section, index) => {
        section.style.animationDelay = `${index * 0.1}s`;
        observer.observe(section);
    });
    
    // Parallax effect for hero (optimized with requestAnimationFrame)
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('.hero-content');
                if (hero && scrolled < window.innerHeight) {
                    hero.style.transform = `translateY(${scrolled * 0.5}px)`;
                    hero.style.opacity = 1 - (scrolled / 600);
                }
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // 3D Cylindrical Carousel for Team Section - GLOBAL FUNCTIONS
    window.currentTeamIndex = 0;
    window.teamCarouselAutoPlay = true;
    window.teamAutoPlayInterval = null;

    window.updateTeamCarouselPosition = function() {
        const carousel = document.getElementById('carouselInner');
        if (!carousel) return;
        
        const items = carousel.querySelectorAll('.carousel-item');
        const totalItems = items.length;

        if (totalItems === 0) return;

        items.forEach((item, index) => {
            item.classList.remove('active', 'prev', 'next', 'hidden');
            
            let distance = (index - window.currentTeamIndex + totalItems) % totalItems;
            
            if (distance === 0) {
                item.classList.add('active');
            } else if (distance === 1 || distance === -(totalItems - 1)) {
                item.classList.add('next');
            } else if (distance === totalItems - 1 || distance === -1) {
                item.classList.add('prev');
            } else {
                item.classList.add('hidden');
            }
        });
    };

    window.rotateCarousel = function(direction) {
        const carousel = document.getElementById('carouselInner');
        if (!carousel) return;
        
        const totalItems = carousel.querySelectorAll('.carousel-item').length;
        if (totalItems === 0) return;
        
        window.currentTeamIndex = (window.currentTeamIndex + direction + totalItems) % totalItems;
        window.updateTeamCarouselPosition();
        
        // Add click animation to active button
        const buttons = document.querySelectorAll('.carousel-btn');
        buttons.forEach(btn => {
            btn.style.transform = direction > 0 ? 'scale(0.9)' : 'scale(0.9)';
            setTimeout(() => btn.style.transform = '', 150);
        });
        
        // Reset auto-play timer
        clearInterval(window.teamAutoPlayInterval);
        if (window.teamCarouselAutoPlay) {
            window.startTeamAutoPlay();
        }
    };

    window.startTeamAutoPlay = function() {
        window.teamAutoPlayInterval = setInterval(() => {
            window.rotateCarousel(1);
        }, 5000);
    };

    // Initialize carousel on page load
    const carouselContainer = document.getElementById('teamCarousel');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', () => {
            clearInterval(window.teamAutoPlayInterval);
        });

        carouselContainer.addEventListener('mouseleave', () => {
            if (window.teamCarouselAutoPlay) {
                window.startTeamAutoPlay();
            }
        });

        // Initialize carousel
        window.updateTeamCarouselPosition();
        window.startTeamAutoPlay();
    }
});
</script>

@endsection
