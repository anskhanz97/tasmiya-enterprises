@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<style>
    .password-hero {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        padding: 3rem 1rem;
        text-align: center;
        color: white;
        margin-bottom: 3rem;
        border-radius: 0 0 50% 50% / 0 0 20px 20px;
        box-shadow: 0 10px 40px rgba(240, 147, 251, 0.3);
    }
    
    .password-hero h1 {
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .password-form-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        padding: 2.5rem;
        max-width: 600px;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    
    .password-form-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 70px rgba(0,0,0,0.15);
    }
    
    .form-group-pass {
        margin-bottom: 2rem;
        position: relative;
    }
    
    .form-group-pass label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    
    .form-group-pass input {
        width: 100%;
        padding: 1rem 3rem 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    
    .form-group-pass input:focus {
        outline: none;
        border-color: #f093fb;
        background: white;
        box-shadow: 0 0 0 4px rgba(240, 147, 251, 0.1);
        transform: translateY(-2px);
    }
    
    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 2.75rem;
        cursor: pointer;
        font-size: 1.25rem;
        color: #6b7280;
        transition: color 0.3s ease;
    }
    
    .password-toggle:hover {
        color: #374151;
    }
    
    .submit-btn-pass {
        width: 100%;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 1.25rem;
        border: none;
        border-radius: 12px;
        font-size: 1.125rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
    }
    
    .submit-btn-pass:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(240, 147, 251, 0.4);
    }
    
    .success-alert {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        color: #065f46;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 600;
        text-align: center;
        animation: slideInDown 0.5s ease;
    }
    
    .error-alert {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        animation: shake 0.5s ease;
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
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .password-requirements {
        background: #f0fdf4;
        border-left: 4px solid #10b981;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1rem;
        font-size: 0.875rem;
    }
    
    .password-requirements ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.25rem;
        color: #047857;
    }
</style>

<div class="password-hero">
    <h1>🔒 Change Password</h1>
    <p>Keep your account secure</p>
</div>

<div class="container mx-auto px-4 pb-12">
    <div class="password-form-container">
        @if (session('success'))
            <div class="success-alert">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-alert">
                <strong>⚠️ Error:</strong>
                <ul style="margin: 0.5rem 0 0 0; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div class="form-group-pass">
                <label for="current_password">
                    🔑 Current Password
                </label>
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password" 
                    required
                    placeholder="Enter your current password"
                >
                <span class="password-toggle" onclick="togglePassword('current_password')">👁️</span>
                @error('current_password')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="form-group-pass">
                <label for="new_password">
                    🆕 New Password
                </label>
                <input 
                    type="password" 
                    id="new_password" 
                    name="new_password" 
                    required
                    placeholder="Enter your new password"
                >
                <span class="password-toggle" onclick="togglePassword('new_password')">👁️</span>
                @error('new_password')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div class="form-group-pass">
                <label for="new_password_confirmation">
                    ✅ Confirm New Password
                </label>
                <input 
                    type="password" 
                    id="new_password_confirmation" 
                    name="new_password_confirmation" 
                    required
                    placeholder="Confirm your new password"
                >
                <span class="password-toggle" onclick="togglePassword('new_password_confirmation')">👁️</span>
            </div>

            <div class="password-requirements">
                <strong>📋 Password Requirements:</strong>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Mix of uppercase and lowercase letters recommended</li>
                    <li>Include numbers and special characters for stronger security</li>
                </ul>
            </div>

            <button type="submit" class="submit-btn-pass" style="margin-top: 1.5rem;">
                🔒 Update Password
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ route('dashboard') }}" style="color: #6b7280; text-decoration: none; font-weight: 600;">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const toggle = field.nextElementSibling;
        
        if (field.type === 'password') {
            field.type = 'text';
            toggle.textContent = '🙈';
        } else {
            field.type = 'password';
            toggle.textContent = '👁️';
        }
    }
</script>
@endsection
