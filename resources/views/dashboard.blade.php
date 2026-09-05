@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .dashboard-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 3rem 1rem;
        text-align: center;
        color: white;
        margin-bottom: 3rem;
        border-radius: 0 0 50% 50% / 0 0 20px 20px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .dashboard-hero h1 {
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
    }
    
    .dashboard-hero p {
        font-size: 1.25rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }
    
    .card-modern {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    }
    
    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 50px rgba(0,0,0,0.12);
    }
    
    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .info-item {
        padding: 1rem;
        background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%);
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .info-item:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .info-value {
        font-size: 1.125rem;
        color: #1f2937;
        font-weight: 700;
    }
    
    .badge-modern {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }
    
    .badge-green {
        background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        color: white;
    }
    
    .badge-yellow {
        background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        color: white;
    }
    
    .quick-action-btn {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .quick-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .btn-primary-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }
    
    .btn-secondary-gradient {
        background: linear-gradient(135deg, #6b7280 0%, #374151 100%);
        color: white;
    }
    
    .btn-purple-gradient {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
    }
    
    .btn-red-gradient {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .session-info-card {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #3b82f6;
        padding: 1.5rem;
        border-radius: 12px;
    }
    
    .session-info-card h3 {
        color: #1e3a8a;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.125rem;
    }
    
    .session-info-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .session-info-card ul li {
        color: #1e40af;
        padding: 0.5rem 0;
        font-size: 0.95rem;
        border-bottom: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    .session-info-card ul li:last-child {
        border-bottom: none;
    }
    
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
</style>

<div class="dashboard-hero">
    <h1>👋 Welcome Back!</h1>
    <p>{{ auth()->user()->name }}</p>
</div>

<div class="container mx-auto px-4 pb-12">
    <div class="max-w-5xl mx-auto">

        {{-- User Profile Card --}}
        <div class="card-modern">
            <h2 class="card-title">
                <span>👤</span> Your Profile
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <p class="info-label">Full Name</p>
                    <p class="info-value">{{ auth()->user()->name }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Email Address</p>
                    <p class="info-value" style="font-size: 1rem; word-break: break-all;">{{ auth()->user()->email }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Role</p>
                    <p class="info-value">
                        <span class="badge-modern badge-blue">
                            {{ auth()->user()->roleName }}
                        </span>
                    </p>
                </div>
                <div class="info-item">
                    <p class="info-label">Division</p>
                    <p class="info-value">
                        {{ auth()->user()->division->name ?? 'Not Assigned' }}
                    </p>
                </div>
                <div class="info-item">
                    <p class="info-label">Account Status</p>
                    <p class="info-value">
                        @if(auth()->user()->is_active)
                            <span class="badge-modern badge-green">
                                ✓ Active
                            </span>
                        @else
                            <span class="badge-modern badge-yellow">
                                ⚠ Inactive
                            </span>
                        @endif
                    </p>
                </div>
                <div class="info-item">
                    <p class="info-label">Last Login</p>
                    <p class="info-value" style="font-size: 0.95rem;">
                        {{ auth()->user()->last_login_at?->diffForHumans() ?? 'First login' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card-modern">
            <h2 class="card-title">
                <span>⚡</span> Quick Actions
            </h2>
            <div class="quick-actions-grid">
                <a href="{{ route('profiles.edit', auth()->user()->profile) }}" class="quick-action-btn btn-primary-gradient">
                    <span>✏️</span> Edit Profile
                </a>
                <a href="{{ route('password.change') }}" class="quick-action-btn btn-secondary-gradient">
                    <span>🔒</span> Change Password
                </a>
                <a href="{{ route('settings.index') }}" class="quick-action-btn btn-purple-gradient">
                    <span>⚙️</span> Site Settings
                </a>
                <a href="{{ route('payments.index') }}" class="quick-action-btn btn-primary-gradient">
                    <span>💳</span> View Payments
                </a>
                <a href="{{ route('contact.create') }}" class="quick-action-btn btn-secondary-gradient">
                    <span>📧</span> Contact Support
                </a>
                <a href="{{ route('home') }}" class="quick-action-btn btn-secondary-gradient">
                    <span>🏠</span> View Homepage
                </a>
            </div>
        </div>

        {{-- Last Login Info --}}
        <div class="session-info-card">
            <h3>🕐 Last Login</h3>
            <p style="color: #1e40af; font-size: 1.125rem; margin: 0;">
                {{ auth()->user()->last_login_at?->format('F j, Y - g:i A') ?? 'First login' }}
            </p>
        </div>

        {{-- Logout Form --}}
        <div style="margin-top: 2rem; text-align: center;">
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="quick-action-btn btn-red-gradient">
                    <span>🚪</span> Sign Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
