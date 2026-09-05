@extends('layouts.auth-layout')

@section('title', 'Login')

@section('auth-title', 'Welcome Back')
@section('auth-subtitle', 'Sign in to your account')

@section('content')
    <style>
        .password-field-wrapper {
            position: relative;
        }
        
        .password-toggle-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            font-size: 1.25rem;
            color: #6b7280;
            transition: color 0.3s ease;
            padding: 0.5rem;
            border-radius: 4px;
        }
        
        .password-toggle-icon:hover {
            color: #374151;
            background: rgba(0, 0, 0, 0.05);
        }
        
        .password-toggle-icon:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .success-message {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: #065f46;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-align: center;
            animation: slideInDown 0.5s ease;
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
    </style>

    {{-- Success Message (e.g., after logout) --}}
    @if (session('status'))
        <div class="success-message">
            ✓ {{ session('status') }}
        </div>
    @endif

    {{-- Login Form --}}
    <form action="{{ route('login.store') }}" method="POST" novalidate>
        @csrf

        {{-- Email Input --}}
        <div class="form-group @error('email') has-error @enderror">
            <label for="email">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="you@example.com"
                required
                autofocus
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password Input --}}
        <div class="form-group @error('password') has-error @enderror">
            <label for="password">Password</label>
            <div class="password-field-wrapper">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                    style="padding-right: 3rem;"
                >
                <span class="password-toggle-icon" onclick="togglePasswordVisibility()" title="Toggle password visibility">
                    👁️
                </span>
            </div>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember Me Checkbox --}}
        <div class="checkbox-group">
            <input 
                type="checkbox" 
                id="remember" 
                name="remember" 
                value="1"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember">Keep me signed in</label>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn btn-primary">
            Sign In
        </button>
    </form>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈'; // Eye with line through it (hide)
                toggleIcon.title = 'Hide password';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️'; // Eye (show)
                toggleIcon.title = 'Show password';
            }
        }
        
        // Keyboard accessibility: Toggle with Enter or Space when focused
        document.querySelector('.password-toggle-icon').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                togglePasswordVisibility();
            }
        });
    </script>
@endsection

@section('auth-footer')
    <div class="auth-footer">
        Don't have an account? 
        <br><br>
        <p style="font-size: 12px; color: #999;">
            Registration is by invitation only. 
            <br>Contact your administrator for access.
        </p>
    </div>
@endsection
