{{-- Service Card Component --}}
@props(['service', 'showAction' => true])

@php
    $themeColors = $service->getThemeColors();
    $specialistsCount = $service->getSpecialistsCount();
@endphp

<div class="card service-card h-100" style="border: none; border-radius: 24px; overflow: hidden; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background: white; position: relative;">
    <div class="card-header border-0" style="background: {{ $themeColors['gradient'] }}; padding: 35px; position: relative; overflow: hidden;">
        <div class="card-shine" style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent); animation: cardShine 3s infinite;"></div>
        <div class="d-flex align-items-center gap-3" style="position: relative; z-index: 2;">
            @if($service->icon_url)
                <div style="width: 70px; height: 70px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border-radius: 18px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                    <img src="{{ $service->icon_url }}" alt="{{ $service->name }}" style="width: 40px; height: 40px; object-fit: contain; filter: brightness(0) invert(1);">
                </div>
            @else
                <div style="width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 2rem; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                    {{ substr($service->name, 0, 1) }}
                </div>
            @endif
            <div style="flex: 1;">
                <h6 class="mb-2" style="color: white; font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                    {{ $service->division->name }}
                </h6>
                <div style="display: inline-block; background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(5px); padding: 6px 14px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.4);">
                    <small class="d-block" style="color: white; font-weight: 700; font-size: 0.75rem;">
                        {{ $specialistsCount }} {{ Str::plural('Expert', $specialistsCount) }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body" style="padding: 40px; position: relative; display: flex; flex-direction: column; min-height: 240px;">
        <h5 class="card-title" style="color: #0f172a; font-weight: 900; font-size: 1.6rem; margin-bottom: 20px; line-height: 1.3;">{{ $service->name }}</h5>
        
        <p class="card-text" style="color: #64748b; font-size: 1.05rem; line-height: 1.9; margin-bottom: 0; flex-grow: 1;">
            {{ $service->description }}
        </p>
    </div>
</div>

<style>
@keyframes cardShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.service-card {
    position: relative;
}

.service-card::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
    border-radius: 24px;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.service-card:hover {
    transform: translateY(-12px) scale(1.02) !important;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2) !important;
}

.service-card:hover::before {
    opacity: 1;
}
</style>
