{{-- Testimonial Card Component --}}
@props(['testimonial', 'featured' => false])

@php
    $initials = $testimonial->getInitials();
    $avatarUrl = $testimonial->getAvatarUrl();
    $ratingLabel = $testimonial->getRatingLabel();
    $ratingClass = $testimonial->getRatingClass();
@endphp

<div class="card testimonial-card h-100 shadow-sm {{ $featured ? 'featured-testimonial' : '' }}">
    @if($featured)
        <div class="featured-badge">
            <span class="badge bg-warning text-dark">★ Featured</span>
        </div>
    @endif
    
    <div class="card-body">
        {{-- Rating --}}
        <div class="mb-3">
            <x-star-rating :rating="$testimonial->rating" :count="0" size="sm" :showLabel="false" />
            <small class="text-muted d-block mt-1">{{ $ratingLabel }}</small>
        </div>
        
        {{-- Testimonial Content --}}
        <p class="card-text text-muted mb-4 lh-lg">
            "{{ $testimonial->content }}"
        </p>
        
        {{-- Author Info --}}
        <div class="d-flex align-items-center gap-3">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $testimonial->author_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e9ecef; font-weight: bold; color: #666;">
                    {{ $initials }}
                </div>
            @endif
            
            <div>
                <p class="mb-0" style="font-weight: 600; color: #333;">{{ $testimonial->author_name }}</p>
                <small class="text-muted">
                    @if($testimonial->author_position)
                        {{ $testimonial->author_position }}
                        @if($testimonial->author_company), {{ $testimonial->author_company }}@endif
                    @elseif($testimonial->author_company)
                        {{ $testimonial->author_company }}
                    @endif
                </small>
            </div>
        </div>
    </div>
    
    @if(auth()->check() && auth()->user()->isAdmin())
        <div class="card-footer bg-light border-top">
            <div class="d-flex gap-2 flex-wrap justify-content-end">
                @if(!$testimonial->is_approved)
                    <form action="{{ route('testimonials.approve', $testimonial) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form action="{{ route('testimonials.reject', $testimonial) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                    </form>
                @else
                    @if(!$testimonial->is_featured)
                        <form action="{{ route('testimonials.feature', $testimonial) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">Feature</button>
                        </form>
                    @else
                        <form action="{{ route('testimonials.unfeature', $testimonial) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-secondary">Unfeature</button>
                        </form>
                    @endif
                    
                    <form action="{{ route('testimonials.destroy', $testimonial) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this testimonial?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.testimonial-card {
    border: none;
    border-left: 4px solid #f0f0f0;
    transition: all 0.3s ease;
}

.testimonial-card:hover {
    border-left-color: #ffc107;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.testimonial-card.featured-testimonial {
    border-left-color: #ffc107;
    background: linear-gradient(135deg, #fffbf010 0%, #fff8f010 100%);
}

.featured-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 10;
}
</style>
