{{-- Star Rating Display Component --}}
@props(['rating' => 0, 'count' => 0, 'size' => 'md', 'showLabel' => true])

@php
    $sizeClass = match($size) {
        'sm' => 'fs-7',
        'md' => 'fs-6',
        'lg' => 'fs-5',
        default => 'fs-6'
    };
    
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - ceil($rating);
@endphp

<div class="d-flex align-items-center gap-2">
    <div class="rating-display">
        @for($i = 0; $i < $fullStars; $i++)
            <span class="star {{ $sizeClass }}">★</span>
        @endfor
        
        @if($hasHalfStar)
            <span class="star-half {{ $sizeClass }}">★</span>
        @endif
        
        @for($i = 0; $i < $emptyStars; $i++)
            <span class="star-empty {{ $sizeClass }}">★</span>
        @endfor
    </div>
    
    @if($showLabel)
        <div class="rating-info">
            <span class="rating-value fw-bold">{{ number_format($rating, 1) }}</span>
            @if($count > 0)
                <span class="rating-count text-muted" style="font-size: 0.85em;">
                    ({{ $count }} {{ Str::plural('review', $count) }})
                </span>
            @endif
        </div>
    @endif
</div>

<style>
.rating-display {
    display: flex;
    gap: 2px;
}

.star {
    color: #ffc107;
    font-weight: bold;
    line-height: 1;
}

.star-half {
    color: #ffc107;
    font-weight: bold;
    line-height: 1;
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.star-empty {
    color: #ddd;
    font-weight: bold;
    line-height: 1;
}

.rating-value {
    color: #333;
    font-size: 1.1em;
}
</style>
