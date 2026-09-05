@extends('layouts.app')

@section('content')
<style>
    .contact-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 4rem 1rem;
        text-align: center;
        color: white;
        margin-bottom: 3rem;
        border-radius: 0 0 50% 50% / 0 0 20px 20px;
        position: relative;
        overflow: hidden;
    }
    
    .contact-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.3;
    }
    
    .contact-hero h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .contact-hero p {
        font-size: 1.1rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }

    .contact-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2.5rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .form-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        padding: 2rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: fit-content;
    }
    
    .form-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 70px rgba(0,0,0,0.15);
    }
    
    .form-input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .form-input-group label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        transition: color 0.3s ease;
    }
    
    .form-input-group input,
    .form-input-group textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    
    .form-input-group input:focus,
    .form-input-group textarea:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }
    
    .form-input-group.error input,
    .form-input-group.error textarea {
        border-color: #ef4444;
    }
    
    .submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .submit-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .submit-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }
    
    .submit-btn:active {
        transform: translateY(-1px);
    }
    
    .contact-sidebar {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .contact-info-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 1.5rem;
        border-radius: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .contact-info-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
        transform: rotate(45deg);
        transition: all 0.5s ease;
    }
    
    .contact-info-card:hover::before {
        left: 100%;
    }
    
    .contact-info-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }
    
    .contact-info-card .icon {
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .contact-info-card .icon svg {
        width: 24px;
        height: 24px;
        color: #667eea;
    }
    
    .contact-info-card .info {
        flex: 1;
    }
    
    .contact-info-card .info .label {
        font-size: 0.8rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    
    .contact-info-card .info .value {
        font-weight: 700;
        color: #1f2937;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .map-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        height: 350px;
    }
    
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    
    .char-counter {
        text-align: right;
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }
    
    .success-message {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        color: #065f46;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(132, 250, 176, 0.3);
        animation: slideInDown 0.5s ease;
        grid-column: 1 / -1;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .error-message {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        animation: shake 0.5s ease;
        grid-column: 1 / -1;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    @media (max-width: 968px) {
        .contact-layout {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .contact-hero h1 {
            font-size: 2rem;
        }
    }
</style>

<div class="contact-hero">
    <h1>✨ Get in Touch</h1>
    <p>We'd love to hear from you! Let's start a conversation.</p>
</div>

<div class="container mx-auto px-4 pb-12">
    <div class="contact-layout">

        @if (session('success'))
            <div class="success-message">
                <p style="margin: 0; text-align: center;">✓ {{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="error-message">
                <h3 style="font-weight: 700; margin-bottom: 0.5rem;">⚠️ Please fix the following errors:</h3>
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Contact Form --}}
        <form action="{{ route('contact.store') }}" method="POST" class="form-container">
            @csrf

            <!-- Name -->
            <div class="form-input-group @error('name') error @enderror">
                <label for="name">
                    👤 Full Name <span style="color: #ef4444;">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    placeholder="Enter your full name"
                >
                @error('name')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-input-group @error('email') error @enderror">
                <label for="email">
                    📧 Email Address <span style="color: #ef4444;">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div class="form-input-group @error('phone') error @enderror">
                <label for="phone">
                    📱 Phone Number <span style="color: #ef4444;">*</span>
                </label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone') }}"
                    placeholder="+92-312-4246916"
                >
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">Include country code (e.g., +92-312-4246916)</p>
                @error('phone')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject -->
            <div class="form-input-group @error('subject') error @enderror">
                <label for="subject">
                    💼 Subject <span style="color: #ef4444;">*</span>
                </label>
                <input 
                    type="text" 
                    id="subject" 
                    name="subject" 
                    value="{{ old('subject') }}"
                    placeholder="What is your inquiry about?"
                >
                @error('subject')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div class="form-input-group @error('message') error @enderror">
                <label for="message">
                    💬 Message <span style="color: #ef4444;">*</span>
                </label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="6" 
                    placeholder="Tell us more about your inquiry..."
                >{{ old('message') }}</textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 5000 characters
                </div>
                @error('message')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn">
                <span style="position: relative; z-index: 1;">📨 Send Inquiry</span>
            </button>
        </form>

        {{-- Contact Info & Map --}}
        <div class="contact-sidebar">
            <!-- Contact Info Cards -->
            <div class="contact-info-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <div class="info">
                    <div class="label">Email Us</div>
                    <div class="value">contact@tasmiya.com</div>
                </div>
            </div>

            <div class="contact-info-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <div class="info">
                    <div class="label">WhatsApp</div>
                    <div class="value">+92-312-4246916</div>
                </div>
            </div>

            <div class="contact-info-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
                <div class="info">
                    <div class="label">Visit Us</div>
                    <div class="value">Office No.5, First Floor, Mozang Heights, 43 Mozang Rd, Lahore, Pakistan</div>
                </div>
            </div>

            <!-- Google Map -->
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3401.672!2d74.31735!3d31.55728!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39190457bfc00000%3A0x0!2sH848%2BXW%20Lahore%2C%20Pakistan!5e0!3m2!1sen!2s!4v1707667890!5m2!1sen!2s" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</div>

<script>
    // Character counter
    const messageInput = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    messageInput.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Initialize
    if (messageInput.value) {
        charCount.textContent = messageInput.value.length;
    }
</script>
@endsection
