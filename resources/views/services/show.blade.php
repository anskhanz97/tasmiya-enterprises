@extends('layouts.app')
@section('title', ($displayOffering?->title() ?? $service->name) . ' | Tasmiya Enterprises')
@section('content')
@php
    $title = $displayOffering?->title() ?? $service->name;
    $theme = $service->getThemeColors();
    $specialist = $displayOffering?->profile;
    $price = $displayOffering
        ? $displayOffering->currency().' '.number_format((float) $displayOffering->price())
        : $service->getFormattedPrice();
    $breakdown = $ratingBreakdown->all();
    $totalRatings = array_sum($breakdown);
@endphp
<style>
.sd-page { --sd-ink:#261b45; --sd-muted:#746b86; --sd-violet:#7152bd; --sd-line:#e4ddef; padding:42px clamp(18px,4vw,64px) 90px; background:linear-gradient(145deg,#f4f1fa,#edf0f8); color:var(--sd-ink); }
.sd-wrap { max-width:1320px; margin:auto; }
.sd-breadcrumb { display:flex; flex-wrap:wrap; align-items:center; gap:9px; margin:0 0 24px; color:var(--sd-muted); font-size:.82rem; }
.sd-breadcrumb a { color:#645097; text-decoration:none; }
.sd-breadcrumb a:hover { text-decoration:underline; }
.sd-hero { position:relative; isolation:isolate; overflow:hidden; display:flex; align-items:flex-start; gap:28px; padding:clamp(28px,4vw,55px); border-radius:26px; color:#fff; background:linear-gradient(115deg,#251944,#493078 65%,#7152a0); }
.sd-hero::after { content:''; position:absolute; z-index:-1; right:-140px; top:-200px; width:500px; height:500px; border:1px solid #ffffff24; border-radius:50%; box-shadow:0 0 0 55px #ffffff06,0 0 0 110px #ffffff04; }
.sd-icon { display:grid; place-items:center; flex:0 0 92px; height:92px; background:#ffffff12; border:1px solid #ffffff35; border-radius:22px; color:#e5d8ff; }
.sd-icon svg,.sd-icon img { width:58px; height:58px; object-fit:contain; }
.sd-division { color:#d0baf1; font-size:.85rem; font-weight:700; }
.sd-hero h1 { font-size:clamp(2rem,4.4vw,3.8rem); letter-spacing:-.055em; line-height:1.1; margin:12px 0 15px; max-width:22ch; font-weight:900; }
.sd-hero p { margin:0; color:#e2d9f0; font-size:.95rem; }
.sd-hero a { color:#fff; text-underline-offset:4px; }
.sd-meta { display:flex; align-items:center; flex-wrap:wrap; gap:12px 24px; margin-top:26px; font-size:.85rem; color:#dfd4ef; }
.sd-meta a { text-decoration:none; }
.sd-stars { color:#f6b83b; letter-spacing:.08em; }
.sd-star-scale { display:inline-block; position:relative; color:#ffffff35; }
.sd-star-scale::before { content:'★★★★★'; position:absolute; left:0; top:0; width:var(--stars); overflow:hidden; white-space:nowrap; color:#f6b83b; }
.sd-meta strong { color:#fff; font-weight:800; }
.sd-layout { display:grid; grid-template-columns:minmax(0,1fr) 330px; gap:26px; margin-top:28px; align-items:start; }
.sd-main,.sd-aside { display:grid; min-width:0; gap:24px; }
.sd-panel { padding:clamp(24px,3vw,38px); background:#fff; border:1px solid var(--sd-line); border-radius:20px; box-shadow:0 10px 30px #35275207; min-width:0; }
.sd-panel h2 { font-size:1.5rem; font-weight:800; letter-spacing:-.035em; margin:0 0 18px; color:var(--sd-ink); }
.sd-panel h3 { font-size:1.1rem; font-weight:700; margin:28px 0 12px; }
.sd-copy { color:#655d78; line-height:1.8; font-size:.95rem; max-width:78ch; overflow-wrap:anywhere; }
.sd-tags { display:flex; flex-wrap:wrap; gap:8px; margin-top:22px; }
.sd-tags span { padding:8px 12px; border-radius:8px; background:#f1edf8; color:#685097; font-size:.76rem; font-weight:700; }
.sd-specialists { display:grid; gap:12px; }
.sd-specialist { display:flex; gap:16px; padding:20px; border:1px solid var(--sd-line); border-radius:14px; text-decoration:none; color:var(--sd-ink); background:#fbfaff; transition:background .2s,border-color .2s; }
.sd-specialist:hover,.sd-specialist:focus-visible { background:#f2ecfb; border-color:#baa5db; }
.sd-specialist.is-selected { border-color:#aa8cce; background:#f5f0fc; }
.sd-avatar { width:64px; height:64px; flex:none; border-radius:50%; object-fit:cover; background:linear-gradient(145deg,#e6e0f5,#d5c9ed); display:grid; place-items:center; font-weight:800; }
.sd-specialist h3 { font-size:1rem; margin:0 0 4px; }
.sd-specialist p { font-size:.8rem; line-height:1.5; color:var(--sd-muted); margin:5px 0 8px; }
.sd-specialist small { display:block; color:#76628f; font-size:.74rem; margin-bottom:5px; }
.sd-specialist strong { color:#65459b; font-size:.85rem; }
.sd-summary { padding:28px; }
.sd-summary-label { display:block; color:var(--sd-muted); font-size:.8rem; }
.sd-price { display:block; font-size:2rem; font-weight:900; letter-spacing:-.04em; color:#513182; margin:7px 0 10px; }
.sd-summary p { font-size:.82rem; color:var(--sd-muted); line-height:1.6; margin:0 0 23px; }
.sd-summary-person { display:flex; align-items:center; gap:12px; padding:19px 0; border-top:1px solid var(--sd-line); }
.sd-summary-person .sd-avatar { width:48px; height:48px; }
.sd-summary-person strong { display:block; font-size:.87rem; }
.sd-summary-person span { font-size:.75rem; color:var(--sd-muted); }
.sd-button { display:inline-flex; align-items:center; justify-content:center; gap:10px; background:var(--sd-violet); color:#fff; text-decoration:none; border:1px solid transparent; border-radius:11px; padding:13px 18px; font:700 .83rem 'Poppins',sans-serif; cursor:pointer; text-align:center; }
.sd-button:hover { background:#563794; color:#fff; }
.sd-button:focus-visible,.sd-specialist:focus-visible { outline:3px solid #aa8cdd; outline-offset:3px; }
.sd-summary .sd-button { width:100%; margin-top:8px; }
.sd-button--secondary { background:#f3eef9; color:#67479e; border-color:#e5dcef; }
.sd-button--secondary:hover { background:#e9dff7; color:#4b2c7e; }
.sd-button--danger { background:#fff4f4; border-color:#eed5d7; color:#9c414b; }
.sd-button--danger:hover { background:#f8e1e3; color:#812d36; }
.sd-panel-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
.sd-panel-head h2 { margin:0; }
.sd-reviews { display:grid; gap:16px; }
.sd-review { padding:24px; border:1px solid var(--sd-line); border-radius:14px; background:#fbfaff; }
.sd-review blockquote { margin:14px 0 20px; color:#655d78; font-size:.9rem; line-height:1.75; overflow-wrap:anywhere; }
.sd-review-author { display:flex; align-items:center; gap:11px; }
.sd-review-author .sd-avatar { width:40px; height:40px; font-size:.7rem; }
.sd-review-author strong { display:block; font-size:.84rem; }
.sd-review-author small { color:var(--sd-muted); font-size:.72rem; }
.sd-review-meta { display:flex; flex-wrap:wrap; justify-content:space-between; gap:8px; align-items:center; }
.sd-review-meta time { color:var(--sd-muted); font-size:.72rem; }
.sd-featured { color:#7557a9; font-size:.72rem; font-weight:800; }
.sd-actions { display:flex; flex-wrap:wrap; gap:10px; }
.sd-review .sd-actions { margin-top:17px; }
.sd-review .sd-button { font-size:.72rem; padding:8px 12px; }
.sd-empty { color:var(--sd-muted); line-height:1.7; }
.sd-rating { display:flex; align-items:baseline; gap:9px; margin-bottom:18px; }
.sd-rating strong { font-size:2.5rem; letter-spacing:-.06em; color:#58378a; }
.sd-rating span { color:var(--sd-muted); font-size:.8rem; }
.sd-rating-row { display:grid; grid-template-columns:30px minmax(0,1fr) 28px; gap:10px; align-items:center; margin:12px 0; font-size:.76rem; color:#817589; }
.sd-rating-row span:first-child { color:#ad843b; }
.sd-rating-bar { height:7px; background:#eee8f6; border-radius:10px; overflow:hidden; }
.sd-rating-bar span { display:block; height:100%; background:#8a65c3; border-radius:inherit; }
.sd-pagination { display:flex; align-items:center; justify-content:center; gap:14px; margin-top:24px; font-size:.8rem; color:var(--sd-muted); }
.sd-pagination a,.sd-pagination > span.sd-arrow { display:grid; place-items:center; width:40px; height:40px; border-radius:50%; border:1px solid var(--sd-line); color:#67479e; background:#fff; text-decoration:none; font-size:1.4rem; }
.sd-pagination > span.sd-arrow { opacity:.35; }
@media(max-width:900px) { .sd-layout { grid-template-columns:minmax(0,1fr) 285px; gap:18px; } .sd-specialist { padding:16px; } }
@media(max-width:760px) { .sd-layout { grid-template-columns:1fr; } .sd-aside { order:-1; } .sd-rating-panel { display:none; } .sd-summary { padding:24px; } .sd-price { font-size:1.7rem; } .sd-hero { gap:20px; } .sd-icon { flex-basis:65px; height:65px; border-radius:16px; } .sd-icon svg,.sd-icon img { width:42px; height:42px; } }
@media(max-width:480px) { .sd-page { padding-top:24px; } .sd-hero { display:block; padding:28px 24px; } .sd-icon { width:58px; height:58px; margin-bottom:22px; } .sd-hero h1 { font-size:2.2rem; } .sd-panel-head .sd-button { width:100%; } .sd-specialist { gap:12px; } .sd-specialist .sd-avatar { width:48px; height:48px; } .sd-review { padding:19px; } }
@media(prefers-reduced-motion:reduce) { .sd-specialist { transition:none; } }
</style>
<main class="sd-page"><div class="sd-wrap">
    <nav class="sd-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('services.index') }}">Services</a><span aria-hidden="true">/</span>
        <a href="{{ route('services.index') }}#division-{{ $service->division->slug }}">{{ $service->division->name }}</a><span aria-hidden="true">/</span>
        <span aria-current="page">{{ $title }}</span>
    </nav>
    <header class="sd-hero">
        <div class="sd-icon" aria-hidden="true">
            @if($displayOffering)<x-work-icon :type="$displayOffering->iconKey()" />
            @elseif($service->icon_url)<img src="{{ $service->icon_url }}" alt="">
            @else<x-work-icon type="audit" />@endif
        </div>
        <div>
            <span class="sd-division">{{ $service->division->name }}</span>
            <h1>{{ $title }}</h1>
            @if($specialist)<p>Offered by <a href="{{ route('profiles.show', $specialist) }}">{{ $specialist->user->name }}</a></p>@endif
            <div class="sd-meta">
                @if($reviewCount > 0)<a href="#sd-reviews"><span class="sd-stars sd-star-scale" style="--stars:{{ min(100, max(0, $averageRating / 5 * 100)) }}%" aria-hidden="true">★★★★★</span> <strong>{{ number_format($averageRating, 1) }}</strong> / 5 · {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</a>
                @else<span>No client reviews yet</span>@endif
                <span>{{ $specialistsCount }} {{ Str::plural('specialist', $specialistsCount) }} available</span>
            </div>
        </div>
    </header>
    <div class="sd-layout">
        <div class="sd-main">
            <section class="sd-panel">
                <h2>About this service</h2>
                <p class="sd-copy">{{ $displayOffering?->description() ?? $service->description }}</p>
                @if($displayOffering && $displayOffering->tags())<div class="sd-tags" aria-label="Areas covered">@foreach($displayOffering->tags() as $tag)<span>{{ $tag }}</span>@endforeach</div>@endif
                @if($service->long_description)<h3>What the work involves</h3><div class="sd-copy">{!! nl2br(e($service->long_description)) !!}</div>@endif
            </section>
            @if($visibleOfferings->isNotEmpty())
            <section class="sd-panel">
                <h2>Choose your specialist</h2>
                <div class="sd-specialists">
                    @foreach($visibleOfferings as $offering)
                    @php $profile = $offering->profile; $avatar = $profile->teamImageUrl('profile'); @endphp
                    <a class="sd-specialist {{ $specialist?->id === $profile->id ? 'is-selected' : '' }}" href="{{ route('services.show', $service) }}?profile={{ $profile->id }}" @if($specialist?->id === $profile->id) aria-current="true" @endif>
                        @if($avatar)<img class="sd-avatar" src="{{ $avatar }}" alt="" width="64" height="64" loading="lazy">@else<span class="sd-avatar" aria-hidden="true">{{ mb_substr($profile->user->name,0,1) }}</span>@endif
                        <div><h3>{{ $profile->user->name }}</h3><small>{{ $specialist?->id === $profile->id ? 'Selected specialist' : 'View this specialist’s listing' }}</small>
                            @if($profile->bio)<p>{{ Str::limit($profile->bio, 130) }}</p>@endif
                            <strong>From {{ $offering->currency() }} {{ number_format((float) $offering->price()) }}</strong>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif
            @auth
            @if(auth()->user()->isAdmin())
            <div class="sd-actions">
                <a href="{{ route('services.edit', $service) }}" class="sd-button sd-button--secondary">Edit service</a>
                <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Delete this service and its associated data?');">
                    @csrf @method('DELETE')<button type="submit" class="sd-button sd-button--danger">Delete service</button>
                </form>
            </div>
            @endif
            @endauth
            <section class="sd-panel" id="sd-reviews">
                <div class="sd-panel-head"><h2>Client reviews</h2><a href="{{ route('testimonials.create', ['service_id'=>$service->id, 'profile_id'=>request('profile')]) }}" class="sd-button sd-button--secondary">Write a review</a></div>
                @if($testimonials->isEmpty())<p class="sd-empty">No reviews yet. Share your experience of this service.</p>
                @else
                <div class="sd-reviews">
                    @foreach($testimonials as $testimonial)
                    <article class="sd-review">
                        <div class="sd-review-meta"><span class="sd-stars" aria-label="{{ $testimonial->rating }} out of 5 stars">{{ str_repeat('★',(int)$testimonial->rating) }}{{ str_repeat('☆',5-(int)$testimonial->rating) }}</span><time datetime="{{ $testimonial->created_at->toDateString() }}">{{ $testimonial->created_at->format('M Y') }}</time>@if($testimonial->is_featured)<span class="sd-featured">Featured review</span>@endif</div>
                        <blockquote>{{ $testimonial->content }}</blockquote>
                        <div class="sd-review-author">
                            @if($testimonial->getAvatarUrl())<img class="sd-avatar" src="{{ $testimonial->getAvatarUrl() }}" alt="" width="40" height="40" loading="lazy">@else<span class="sd-avatar" aria-hidden="true">{{ $testimonial->getInitials() }}</span>@endif
                            <div><strong>{{ $testimonial->author_name }}</strong><small>{{ $testimonial->author_position }}{{ $testimonial->author_position && $testimonial->author_company ? ', ' : '' }}{{ $testimonial->author_company }}</small></div>
                        </div>
                        @auth
                        @if(auth()->user()->isAdmin())
                        <div class="sd-actions">
                            <form action="{{ $testimonial->is_featured ? route('testimonials.unfeature', $testimonial) : route('testimonials.feature', $testimonial) }}" method="POST">@csrf<button class="sd-button sd-button--secondary" type="submit">{{ $testimonial->is_featured ? 'Remove featured status' : 'Feature review' }}</button></form>
                            <form action="{{ route('testimonials.destroy', $testimonial) }}" method="POST" onsubmit="return confirm('Delete this review?');">@csrf @method('DELETE')<button class="sd-button sd-button--danger" type="submit">Delete review</button></form>
                        </div>
                        @endif
                        @endauth
                    </article>
                    @endforeach
                </div>
                @if($testimonials->hasPages())
                <nav class="sd-pagination" aria-label="Review pages">
                    @if($testimonials->onFirstPage())<span class="sd-arrow" aria-disabled="true">‹</span>@else<a href="{{ $testimonials->appends(request()->only('profile'))->previousPageUrl() }}" aria-label="Previous reviews">‹</a>@endif
                    <span>Page {{ $testimonials->currentPage() }} of {{ $testimonials->lastPage() }}</span>
                    @if($testimonials->hasMorePages())<a href="{{ $testimonials->appends(request()->only('profile'))->nextPageUrl() }}" aria-label="Next reviews">›</a>@else<span class="sd-arrow" aria-disabled="true">›</span>@endif
                </nav>
                @endif
                @endif
            </section>
        </div>
        <aside class="sd-aside" aria-label="Price and service information">
            <section class="sd-panel sd-summary">
                <span class="sd-summary-label">Starting price{{ $specialist ? ' with '.$specialist->user->name : '' }}</span>
                <strong class="sd-price">{{ $price }}</strong>
                <p>Discuss your requirements with {{ $specialist ? $specialist->user->name : 'our team' }} to confirm the scope and final quotation.</p>
                @if($specialist)
                @php $avatar = $specialist->teamImageUrl('profile'); @endphp
                <div class="sd-summary-person">@if($avatar)<img class="sd-avatar" src="{{ $avatar }}" alt="" width="48" height="48">@else<span class="sd-avatar">{{ mb_substr($specialist->user->name,0,1) }}</span>@endif<div><strong>{{ $specialist->user->name }}</strong><span>{{ $service->division->name }}</span></div></div>
                @if($specialist->getWhatsAppUrl())<a class="sd-button" href="{{ $specialist->getWhatsAppUrl('Hi, I would like to discuss '.$title.'.') }}" target="_blank" rel="noopener noreferrer">Discuss on WhatsApp</a>@endif
                <a class="sd-button sd-button--secondary" href="{{ route('profiles.show', $specialist) }}">View specialist profile</a>
                @else<a class="sd-button" href="{{ route('contact.create') }}">Get in touch</a>@endif
            </section>
            <section class="sd-panel sd-rating-panel">
                <h2>Service ratings</h2>
                @if($reviewCount)
                <div class="sd-rating"><strong>{{ number_format($averageRating,1) }}</strong><span>/ 5 from {{ $reviewCount }} reviews</span></div>
                @for($star=5; $star>=1; $star--)
                @php $count=$breakdown[$star] ?? 0; $percentage=$totalRatings ? $count/$totalRatings*100 : 0; @endphp
                <div class="sd-rating-row"><span>{{ $star }} ★</span><div class="sd-rating-bar" role="meter" aria-label="{{ $star }} star reviews" aria-valuemin="0" aria-valuemax="{{ $totalRatings }}" aria-valuenow="{{ $count }}"><span style="width:{{ $percentage }}%"></span></div><span>{{ $count }}</span></div>
                @endfor
                @else<p class="sd-empty">Ratings will appear after clients share their experience.</p>@endif
            </section>
        </aside>
    </div>
</div></main>
@endsection
