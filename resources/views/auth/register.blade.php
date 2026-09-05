@extends('layouts.auth-layout')

@section('title', 'Register')

@section('auth-title', 'Create Account')
@section('auth-subtitle', 'Join the Tasmiya Enterprises team')

@section('content')
    {{-- Registration Form --}}
    <form action="{{ route('register.store') }}" method="POST" novalidate>
        @csrf

        {{-- Full Name Input --}}
        <div class="form-group @error('name') has-error @enderror">
            <label for="name">Full Name</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                placeholder="Your Full Name"
                required
                autofocus
            >
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

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
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Division Selection --}}
        <div class="form-group @error('division_id') has-error @enderror">
            <label for="division_id">Division</label>
            <select id="division_id" name="division_id" required>
                <option value="">-- Select Your Division --</option>
                <option value="1" {{ old('division_id') == 1 ? 'selected' : '' }}>
                    FBR Taxation Services
                </option>
                <option value="2" {{ old('division_id') == 2 ? 'selected' : '' }}>
                    IT & Digital Services
                </option>
                <option value="3" {{ old('division_id') == 3 ? 'selected' : '' }}>
                    Technical Support & Installation
                </option>
            </select>
            @error('division_id')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Role Selection --}}
        <div class="form-group @error('role') has-error @enderror">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">-- Select Your Role --</option>
                <option value="team_member" {{ old('role') == 'team_member' ? 'selected' : '' }}>
                    Team Member
                </option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                    Administrator
                </option>
            </select>
            <small style="display: block; margin-top: 4px; color: #666;">
                Only registered admins can select admin role. Others should select Team Member.
            </small>
            @error('role')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password Input --}}
        <div class="form-group @error('password') has-error @enderror">
            <label for="password">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="••••••••"
                required
            >
            <small style="display: block; margin-top: 4px; color: #666;">
                Minimum 8 characters. Use a mix of letters and numbers for security.
            </small>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password Input --}}
        <div class="form-group @error('password_confirmation') has-error @enderror">
            <label for="password_confirmation">Confirm Password</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                placeholder="••••••••"
                required
            >
            <small style="display: block; margin-top: 4px; color: #666;">
                Re-enter your password to confirm.
            </small>
            @error('password_confirmation')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn btn-primary">
            Create Account
        </button>
    </form>
@endsection

@section('auth-footer')
    <div class="auth-footer">
        Already have an account? 
        <a href="{{ route('login') }}">Sign in here</a>
    </div>
@endsection
