@props(['service' => null, 'offering' => null, 'showAction' => true, 'compact' => false])

@php
    $service = $service ?? $offering->service;
    $themeColors = $service->getThemeColors();
    $specialistsCount = $offering ? null : $service->getSpecialistsCount();
    $presentation = config("service_cards.services.{$service->slug}", []);
    $icon = $offering?->iconKey() ?? $presentation['icon'] ?? config("service_cards.divisions.{$service->division->slug}.icon", 'audit');
    $focus = $offering?->tags() ?? $presentation['focus'] ?? [];
    $cardTitle = $offering?->title() ?? $service->name;
    $cardDescription = $offering?->description() ?? $service->description;
    $cardPrice = $offering?->price() ?? $service->base_price;
    $cardCurrency = $offering?->currency() ?? $service->currency;
    $serviceUrl = route('services.show', $service) . ($offering ? '?profile=' . $offering->profile_id : '');
@endphp

<article class="service-card {{ $compact ? 'service-card--compact' : '' }}" style="--card-accent: {{ $themeColors['primary'] }};">
    <div class="service-card__visual">
        <div class="service-card__symbol">
            @if($service->icon_url && ! $offering?->icon_key)
                <img src="{{ $service->icon_url }}" alt="" loading="lazy" decoding="async" width="68" height="68">
            @else
                <x-work-icon :type="$icon" />
            @endif
        </div>
        <span class="service-card__category">{{ $service->division->name }}</span>
    </div>
    <div class="service-card__body">
        <h3><a href="{{ $serviceUrl }}">{{ $cardTitle }}</a></h3>
        <p class="service-card__description">{{ $cardDescription }}</p>
        @if($focus)
            <div class="service-card__focus" aria-label="Areas covered">
                @foreach($focus as $item)<span>{{ $item }}</span>@endforeach
            </div>
        @endif
    </div>
    <div class="service-card__footer">
        <div class="service-card__meta">
            <span>{{ $offering ? $offering->profile->user->name : ($specialistsCount > 0 ? $specialistsCount . ' ' . \Illuminate\Support\Str::plural('specialist', $specialistsCount) : 'Expert-led') }}</span>
            <strong>@if((float) $cardPrice > 0)<small>Starting at</small> {{ $cardCurrency ?: 'PKR' }} {{ number_format((float) $cardPrice) }}@else Quote on request @endif</strong>
        </div>
        @if($showAction)<a href="{{ $serviceUrl }}" class="service-card__action">View service <span aria-hidden="true">↗</span></a>@endif
    </div>
</article>

@once
<style>
    .service-card { display: flex; flex-direction: column; min-width: 0; height: 100%; background: #fff; border: 1px solid #dce6f0; border-radius: 18px; overflow: hidden; box-shadow: 0 12px 30px rgba(13,41,71,.07); transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease; }
    .service-card:hover { transform: translateY(-5px); border-color: color-mix(in srgb, var(--card-accent), white 55%); box-shadow: 0 20px 38px rgba(13,41,71,.13); }
    .service-card__visual { position: relative; isolation: isolate; min-height: 145px; display: flex; align-items: end; justify-content: space-between; gap: 12px; padding: 22px 24px; color: var(--card-accent); background: color-mix(in srgb, var(--card-accent), white 92%); overflow: hidden; }
    .service-card__visual::before { content: ''; position: absolute; z-index: -1; width: 230px; height: 230px; right: -50px; top: -130px; border: 28px solid color-mix(in srgb, var(--card-accent), white 82%); border-radius: 50%; }
    .service-card__visual::after { content: ''; position: absolute; z-index: -1; width: 130px; height: 130px; right: 58px; bottom: -98px; border: 1px solid color-mix(in srgb, var(--card-accent), white 57%); border-radius: 50%; }
    .service-card__symbol { display: grid; place-items: center; width: 92px; height: 92px; flex: 0 0 92px; border-radius: 22px; background: #fff; border: 1px solid color-mix(in srgb, var(--card-accent), white 75%); box-shadow: 0 9px 20px rgba(21,56,89,.09); }
    .service-card__symbol .work-icon, .service-card__symbol img { display: block; width: 68px; height: 68px; object-fit: contain; }
    .service-card__category { max-width: 130px; color: color-mix(in srgb, var(--card-accent), #142b45 35%); font-size: .72rem; line-height: 1.4; font-weight: 700; text-align: right; }
    .service-card__body { flex: 1; padding: 26px 26px 22px; }
    .service-card__body h3 { margin: 0 0 11px; color: #102945; font-size: clamp(1.3rem, 2vw, 1.58rem); line-height: 1.28; letter-spacing: -.035em; font-weight: 800; }
    .service-card__body h3 a { color: inherit; text-decoration: none; }
    .service-card__body h3 a:hover { color: var(--card-accent); }
    .service-card__description { color: #52677d; font-size: .93rem; line-height: 1.7; min-height: 4.8em; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; overflow: hidden; margin: 0 0 18px; }
    .service-card__focus { display: flex; flex-wrap: wrap; gap: 7px; }
    .service-card__focus span { padding: 6px 9px; border-radius: 6px; background: #f2f6fa; color: #405b75; font-size: .7rem; font-weight: 600; line-height: 1.35; }
    .service-card__footer { border-top: 1px solid #e5ecf3; margin: 0 26px; padding: 17px 0 22px; }
    .service-card__meta { display: flex; justify-content: space-between; align-items: end; gap: 10px; color: #718398; font-size: .73rem; }
    .service-card__meta strong { color: #102945; font-size: .85rem; font-weight: 800; text-align: right; }
    .service-card__meta small { display: block; color: #718398; font-size: .67rem; font-weight: 500; }
    .service-card__action { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 18px; padding: 11px 14px; border-radius: 8px; color: #fff; background: #143856; text-decoration: none; font-size: .82rem; font-weight: 700; transition: background .2s ease; }
    .service-card__action:hover { color: #fff; background: var(--card-accent); text-decoration: none; }
    .service-card__action span { font-size: 1.1rem; line-height: 1; }
    .service-card a:focus-visible { outline: 3px solid var(--card-accent); outline-offset: 3px; }
    .service-card--compact .service-card__visual { min-height: 126px; }
    .service-card--compact .service-card__symbol { width: 78px; height: 78px; flex-basis: 78px; border-radius: 18px; }
    .service-card--compact .service-card__symbol .work-icon, .service-card--compact .service-card__symbol img { width: 58px; height: 58px; }
    .service-card--compact .service-card__body { padding: 22px 22px 18px; }
    .service-card--compact .service-card__footer { margin: 0 22px; }
    .service-card--compact .service-card__body h3 { font-size: 1.32rem; }
    @media (prefers-reduced-motion: reduce) { .service-card, .service-card__action { transition: none; } .service-card:hover { transform: none; } }
</style>
@endonce
