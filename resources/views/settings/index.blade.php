@extends('layouts.app')

@section('title', 'Site Settings - Tasmiya Enterprises')

@section('content')
<style>
    .settings-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .settings-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        border-radius: 20px;
        color: white;
        margin-bottom: 40px;
        text-align: center;
    }
    
    .settings-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .settings-header p {
        font-size: 1.1rem;
        opacity: 0.95;
    }
    
    .settings-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    
    .settings-card h2 {
        font-size: 1.8rem;
        color: #1a202c;
        margin-bottom: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        font-size: 1rem;
    }
    
    .form-group input[type="url"] {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    
    .form-group input[type="url"]:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .form-group small {
        display: block;
        margin-top: 6px;
        color: #718096;
        font-size: 0.875rem;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 14px 40px;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        gap: 12px;
        color: #764ba2;
    }
    
    .success-message {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        margin-bottom: 30px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
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
        background: #fee;
        color: #c00;
        padding: 12px 18px;
        border-radius: 8px;
        margin-top: 8px;
        font-size: 0.9rem;
    }
    
    .social-icon-preview {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }
    
    .social-icon-preview a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .social-icon-preview a:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
</style>

<div class="settings-container">
    <a href="{{ route('dashboard') }}" class="btn-back">
        ← Back to Dashboard
    </a>
    
    <div class="settings-header">
        <h1>⚙️ Site Settings</h1>
        <p>Manage your website's configuration and social media links</p>
    </div>
    
    @if(session('success'))
        <div class="success-message">
            ✓ {{ session('success') }}
        </div>
    @endif
    
    {{-- Social Media Settings --}}
    <div class="settings-card">
        <h2>📱 Social Media Links</h2>
        <p style="color: #718096; margin-bottom: 30px;">
            Manage the social media links that appear in your website footer. These links are visible to all visitors.
        </p>
        
        <form method="POST" action="{{ route('settings.social.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>🔵 Facebook</label>
                <input 
                    type="url" 
                    name="facebook" 
                    value="{{ old('facebook', $socialLinks['facebook']) }}" 
                    placeholder="https://facebook.com/yourpage"
                >
                <small>Enter your Facebook page URL</small>
                @error('facebook')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>⚫ Twitter / X</label>
                <input 
                    type="url" 
                    name="twitter" 
                    value="{{ old('twitter', $socialLinks['twitter']) }}" 
                    placeholder="https://twitter.com/yourprofile"
                >
                <small>Enter your Twitter/X profile URL</small>
                @error('twitter')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>💼 LinkedIn</label>
                <input 
                    type="url" 
                    name="linkedin" 
                    value="{{ old('linkedin', $socialLinks['linkedin']) }}" 
                    placeholder="https://linkedin.com/company/yourcompany"
                >
                <small>Enter your LinkedIn company page URL</small>
                @error('linkedin')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>📷 Instagram</label>
                <input 
                    type="url" 
                    name="instagram" 
                    value="{{ old('instagram', $socialLinks['instagram']) }}" 
                    placeholder="https://instagram.com/yourprofile"
                >
                <small>Enter your Instagram profile URL</small>
                @error('instagram')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn-save">
                    💾 Save Social Links
                </button>
            </div>
        </form>
        
        @if($socialLinks['facebook'] || $socialLinks['twitter'] || $socialLinks['linkedin'] || $socialLinks['instagram'])
            <div style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #e2e8f0;">
                <p style="color: #718096; margin-bottom: 15px; font-weight: 600;">Preview (as shown in footer):</p>
                <div class="social-icon-preview">
                    @if($socialLinks['facebook'])
                        <a href="{{ $socialLinks['facebook'] }}" target="_blank" title="Facebook">f</a>
                    @endif
                    @if($socialLinks['twitter'])
                        <a href="{{ $socialLinks['twitter'] }}" target="_blank" title="Twitter">𝕏</a>
                    @endif
                    @if($socialLinks['linkedin'])
                        <a href="{{ $socialLinks['linkedin'] }}" target="_blank" title="LinkedIn">in</a>
                    @endif
                    @if($socialLinks['instagram'])
                        <a href="{{ $socialLinks['instagram'] }}" target="_blank" title="Instagram">📷</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
