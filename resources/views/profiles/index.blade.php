@extends('layouts.app')

@section('content')
<style>
    .showcase-container {
        min-height: calc(100vh - 64px);
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        padding: 3rem 1rem;
    }
    
    .showcase-header {
        text-align: center;
        margin-bottom: 4rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .showcase-header h1 {
        font-size: 2.5rem;
        color: #111827;
        margin: 0;
        font-weight: 700;
    }
    
    .showcase-header p {
        font-size: 1.125rem;
        color: #6b7280;
        margin: 1rem 0 0;
        line-height: 1.6;
    }
    
    .showcase-wrapper {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .division-section {
        margin-bottom: 4rem;
    }
    
    .division-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid;
    }
    
    .division-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.5rem;
        font-size: 2rem;
        background: var(--light-color);
    }
    
    .division-info h2 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
    }
    
    .division-info p {
        margin: 0.5rem 0 0;
        color: #6b7280;
        font-size: 1rem;
    }
    
    .profiles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .profile-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .profile-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }
    
    .profile-card-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        background: #f3f4f6;
    }
    
    .profile-card-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .profile-card-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    
    .profile-card-title {
        color: #6b7280;
        font-size: 0.95rem;
        margin: 0.5rem 0 0;
    }
    
    .profile-card-bio {
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.5;
        margin: 1rem 0 0;
        flex-grow: 1;
    }
    
    .profile-card-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .profile-card-tag {
        display: inline-block;
        background: #f0f9ff;
        color: #0369a1;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .profile-card-cta {
        display: inline-block;
        background: #3b82f6;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        margin-top: 1rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .profile-card:hover .profile-card-cta {
        background: #2563eb;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: #111827;
        margin: 0;
    }
    
    .empty-state p {
        margin: 0.5rem 0 0;
    }
    
    @media (max-width: 768px) {
        .showcase-header h1 {
            font-size: 2rem;
        }
        
        .profiles-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="showcase-container">
    <div class="showcase-header">
        <h1>Meet Our Team</h1>
        <p>Talented professionals dedicated to delivering excellence in taxation, IT services, and technical support. Get to know our team members and their expertise.</p>
    </div>
    
    <div class="showcase-wrapper">
        @forelse($profilesByDivision as $divisionId => $divisionProfiles)
            @php
                $division = $divisionProfiles->first()->user->division;
                $colors = $division->getThemeColors();
            @endphp
            
            <div class="division-section">
                <!-- Division Header -->
                <div class="division-header" style="border-bottom-color: {{ $colors['primary'] }};">
                    <div class="division-icon" style="background: {{ $colors['light'] }}; color: {{ $colors['primary'] }};">
                        📊
                    </div>
                    <div class="division-info">
                        <h2 style="color: {{ $colors['primary'] }};">{{ $division->name }}</h2>
                        <p>{{ $division->tagline }}</p>
                    </div>
                </div>
                
                <!-- Profiles Grid -->
                <div class="profiles-grid">
                    @foreach($divisionProfiles as $profile)
                        <a href="{{ route('profiles.show', $profile) }}" class="profile-card">
                            <!-- Profile Image -->
                            <img src="{{ $profile->getImageUrl() }}" alt="{{ $profile->user->name }}" class="profile-card-image">
                            
                            <!-- Profile Info -->
                            <div class="profile-card-content">
                                <h3 class="profile-card-name">{{ $profile->user->name }}</h3>
                                <p class="profile-card-title">
                                    @if($profile->specializations && count($profile->specializations) > 0)
                                        {{ $profile->specializations[0] }}
                                    @else
                                        Professional
                                    @endif
                                </p>
                                
                                <!-- Bio Preview -->
                                @if($profile->bio)
                                    <p class="profile-card-bio">
                                        {{ Str::limit($profile->bio, 120) }}
                                    </p>
                                @endif
                                
                                <!-- Skills Tags -->
                                @if($profile->specializations && count($profile->specializations) > 0)
                                    <div class="profile-card-tags">
                                        @foreach(array_slice($profile->specializations, 0, 2) as $spec)
                                            <span class="profile-card-tag">{{ $spec }}</span>
                                        @endforeach
                                        @if(count($profile->specializations) > 2)
                                            <span class="profile-card-tag">+{{ count($profile->specializations) - 2 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            <!-- CTA Button -->
                            <div style="padding: 0 1.5rem 1.5rem;">
                                <span class="profile-card-cta">View Profile →</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <h3>No Team Members Yet</h3>
                <p>Team profiles will appear here once they complete their profiles.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
