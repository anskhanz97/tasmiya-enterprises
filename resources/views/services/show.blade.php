@extends('layouts.app')

@section('title', $service->name . ' | Tasmiya Enterprises')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-decoration-none">Services</a></li>
            <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-decoration-none">{{ $service->division->name }}</a></li>
            <li class="breadcrumb-item active">{{ $service->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Service Header --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body" style="background: linear-gradient(135deg, {{ $service->getThemeColors()['color'] }}10 0%, {{ $service->getThemeColors()['color'] }}05 100%);">
                    <div class="d-flex align-items-start gap-4 mb-4">
                        @if($service->icon_url)
                            <img src="{{ $service->icon_url }}" alt="{{ $service->name }}" style="width: 80px; height: 80px; object-fit: contain;">
                        @else
                            <div style="width: 80px; height: 80px; background: {{ $service->getThemeColors()['color'] }}; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 2rem;">
                                {{ substr($service->name, 0, 1) }}
                            </div>
                        @endif
                        
                        <div class="flex-grow-1">
                            <div style="color: {{ $service->getThemeColors()['color'] }}; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px;">
                                {{ $service->division->name }}
                            </div>
                            <h1 class="mb-3" style="color: #333; font-weight: 700;">{{ $service->name }}</h1>
                            
                            <div class="d-flex align-items-center gap-4 flex-wrap">
                                <div>
                                    <x-star-rating :rating="$averageRating" :count="$reviewCount" size="md" />
                                </div>
                                
                                <div class="text-muted">
                                    <strong>{{ $specialistsCount }}</strong> {{ Str::plural('Specialist', $specialistsCount) }} Available
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="alert alert-light border mb-0" style="background: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Starting Price</span>
                            <h3 class="mb-0" style="color: {{ $service->getThemeColors()['color'] }}; font-weight: 700;">
                                {{ $service->getFormattedPrice() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Description --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="mb-3" style="color: #333; font-weight: 600;">Overview</h4>
                    <p class="text-muted lh-lg mb-4">{{ $service->description }}</p>

                    @if($service->long_description)
                        <h5 style="color: #333; font-weight: 600; margin-top: 30px; margin-bottom: 15px;">Detailed Description</h5>
                        <div class="text-muted lh-lg">
                            {!! nl2br(e($service->long_description)) !!}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Specialists --}}
            @if($service->profiles->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-4" style="color: #333; font-weight: 600;">Our Specialists</h4>
                        
                        <div class="row g-3">
                            @foreach($service->profiles as $profile)
                                <div class="col-md-6">
                                    <div class="d-flex gap-3 p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        @if($profile->user->avatar_url)
                                            <img src="{{ $profile->user->avatar_url }}" alt="{{ $profile->user->name }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; flex-shrink: 0;">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: {{ $service->getThemeColors()['color'] }}; color: white; font-weight: bold; flex-shrink: 0;">
                                                {{ substr($profile->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <p class="mb-1" style="font-weight: 600; color: #333;">{{ $profile->user->name }}</p>
                                            <small class="text-muted d-block">{{ $profile->title ?? 'Specialist' }}</small>
                                            @if($profile->bio)
                                                <small class="text-muted d-block mt-1">{{ Str::limit($profile->bio, 60) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Admin Actions --}}
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="mb-4 d-flex gap-2">
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Service
                        </a>
                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this service? This will also delete all associated data.');"><
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete Service
                            </button>
                        </form>
                    </div>
                @endif
            @endauth

            {{-- Testimonials Section --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 style="color: #333; font-weight: 600; margin: 0;">Client Testimonials</h4>
                        <a href="{{ route('testimonials.create', ['type' => 'service', 'service_id' => $service->id]) }}" class="btn btn-sm btn-primary">
                            + Write Review
                        </a>
                    </div>

                    @if($testimonials->isEmpty())
                        <div class="alert alert-light text-center py-4">
                            <p class="text-muted mb-0">No testimonials yet. Be the first to share your experience!</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($testimonials as $testimonial)
                                <div class="col-12">
                                    <x-testimonial-card :testimonial="$testimonial" :featured="$testimonial->is_featured" />
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($testimonials->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $testimonials->render() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Service Stats --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4" style="color: #333; font-weight: 600;">Quick Stats</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Average Rating</small>
                        <h4 class="mb-0" style="color: {{ $service->getThemeColors()['color'] }}; font-weight: 700;">
                            {{ number_format($averageRating, 1) }}
                            <small style="font-size: 0.6em;">/ 5.0</small>
                        </h4>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block">Total Reviews</small>
                        <h4 class="mb-0" style="color: {{ $service->getThemeColors()['color'] }}; font-weight: 700;">
                            {{ $reviewCount }}
                        </h4>
                    </div>

                    <hr>

                    <div class="mb-0">
                        <small class="text-muted d-block">Specialists</small>
                        <h4 class="mb-0" style="color: {{ $service->getThemeColors()['color'] }}; font-weight: 700;">
                            {{ $specialistsCount }}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Rating Breakdown --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4" style="color: #333; font-weight: 600;">Rating Breakdown</h5>
                    
                    @php
                        $breakdown = $service->getRatingBreakdown();
                        $totalRatings = array_sum($breakdown);
                    @endphp

                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $breakdown[$star] ?? 0;
                            $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div style="color: #ffc107;">
                                    @for($i = 0; $i < $star; $i++)
                                        ★
                                    @endfor
                                </div>
                                <small class="text-muted">{{ $count }}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" style="width: {{ $percentage }}%; background-color: {{ $service->getThemeColors()['color'] }};" role="progressbar"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- CTA Card --}}
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, {{ $service->getThemeColors()['color'] }} 0%, {{ $service->getThemeColors()['accent'] ?? $service->getThemeColors()['color'] }} 100%); color: white;">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3" style="color: white; font-weight: 600;">Interested in this service?</h5>
                    <p class="card-text mb-4">Get in touch with our specialists today for a consultation.</p>
                    <a href="mailto:info@tasmiya.com" class="btn btn-light btn-sm">
                        Contact Us Today
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
