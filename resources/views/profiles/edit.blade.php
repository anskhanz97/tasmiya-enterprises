@extends('layouts.app')

@section('title', 'Edit profile')

@section('content')
@php
    $sections = collect($profile->presentationSections());
    $banner = $profile->displayImageUrl('banner');
    $portrait = $profile->displayImageUrl('profile');
    $googleClientId = \App\Models\SiteSetting::get('integration_google_client_id', config('services.google.client_id'));
    $googleApiKey = \App\Models\SiteSetting::get('integration_google_api_key', config('services.google.api_key'));
    $googleAppId = \App\Models\SiteSetting::get('integration_google_app_id', config('services.google.app_id'));
    $driveReady = $googleClientId && $googleApiKey && $googleAppId && ctype_digit((string) $googleAppId);
    $driveConfig = [
        'clientId' => $googleClientId,
        'apiKey' => $googleApiKey,
        'appId' => $googleAppId,
        'verifyUrl' => route('settings.google.verify'),
        'canVerify' => auth()->user()?->isAdmin(),
    ];
    $oneDriveClientId = \App\Models\SiteSetting::get('integration_onedrive_client_id');
    $linkedIn = old('social_links.linkedin',$profile->social_links['linkedin'] ?? '');
    if ($linkedIn && !\Illuminate\Support\Str::startsWith($linkedIn, ['http://', 'https://'])) {
        $linkedIn = 'https://' . $linkedIn;
    }
@endphp
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');
.pe-shell{--ink:#102a39;--muted:#60727e;--line:#dbe6e8;--paper:#fff;--accent:#bce8da;background:#eff4f3;color:var(--ink);min-height:100vh;font-family:'DM Sans',sans-serif;padding:32px clamp(18px,4vw,64px) 100px}
.pe-section-row .pe-description{grid-column:2 / -1;font-size:12px;background:white}
.pe-shell *{box-sizing:border-box}.pe-inner{max-width:1440px;margin:auto}.pe-eyebrow{text-transform:uppercase;letter-spacing:.17em;font-size:11px;font-weight:800;color:#207a73}.pe-shell h1,.pe-shell h2,.pe-shell h3{font-family:Outfit,sans-serif;letter-spacing:-.045em}.pe-topline{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:22px}.pe-topline a{color:var(--ink);text-decoration:none;font-weight:700;font-size:13px}.pe-topline a:hover{text-decoration:underline}
.pe-hero{min-height:310px;border-radius:28px;overflow:hidden;position:relative;display:flex;align-items:end;background:radial-gradient(circle at 74% 34%,#4f948d 0%,#163e50 45%,#0a2231 100%);isolation:isolate}.pe-hero:before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,rgba(7,32,43,.9),rgba(7,32,43,.22)),var(--hero-image);background-size:cover;background-position:center;z-index:-2}.pe-hero:after{content:'';position:absolute;width:540px;height:540px;right:-100px;top:-260px;border:1px solid rgba(255,255,255,.28);border-radius:50%;box-shadow:0 0 0 80px rgba(255,255,255,.035),0 0 0 160px rgba(255,255,255,.025);z-index:-1}.pe-hero-content{padding:40px clamp(28px,5vw,72px);display:flex;gap:24px;align-items:end;width:100%;color:white}.pe-avatar{width:102px;height:102px;flex:none;border-radius:23px;object-fit:cover;border:4px solid rgba(255,255,255,.75);background:#7ba9aa}.pe-avatar-placeholder{display:grid;place-items:center;font:700 36px Outfit}.pe-hero h1{font-size:clamp(36px,4.2vw,64px);line-height:.99;margin:8px 0 10px}.pe-hero p{margin:0;color:#c8e4e1}.pe-hero-actions{margin-left:auto;align-self:end}.pe-hero-actions a{display:inline-block;padding:12px 18px;background:white;color:#133545;text-decoration:none;border-radius:11px;font-weight:800;font-size:13px}
.pe-intro{display:flex;justify-content:space-between;align-items:end;gap:20px;padding:38px 0 20px}.pe-intro h2{font-size:clamp(30px,3vw,45px);line-height:1.05;margin:8px 0}.pe-intro p{color:var(--muted);margin:0;max-width:510px}.pe-layout{display:grid;grid-template-columns:230px minmax(0,1fr);gap:22px;align-items:start}.pe-nav{position:sticky;top:100px;padding:18px;background:#dfeae8;border-radius:20px}.pe-nav a{display:block;text-decoration:none;color:#42616b;font-size:13px;font-weight:800;padding:12px 14px;border-radius:11px}.pe-nav a:hover,.pe-nav a:focus{background:white;color:#113748}.pe-nav small{display:block;font-size:11px;line-height:1.5;color:#667f85;padding:18px 13px 6px}.pe-card{background:var(--paper);border:1px solid var(--line);border-radius:22px;padding:clamp(22px,3vw,38px);margin-bottom:18px;box-shadow:0 8px 28px rgba(20,59,67,.035)}.pe-card-head{display:flex;justify-content:space-between;align-items:start;gap:20px;margin-bottom:24px}.pe-card h3{font-size:28px;line-height:1.1;margin:5px 0}.pe-card p{color:var(--muted);line-height:1.6;margin:0}.pe-num{font:700 14px Outfit;color:#20847d;background:#e5f6ef;padding:10px 12px;border-radius:10px}.pe-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.pe-field{display:flex;flex-direction:column;gap:9px;margin-bottom:18px}.pe-field label,.pe-label{font-size:13px;font-weight:800;color:#193746}.pe-field input,.pe-field textarea,.pe-row input[type=text],.pe-section-row input[type=text]{width:100%;min-width:0;border:1px solid #cbd9dc;background:#f8fbfa;color:#153542;border-radius:10px;padding:12px 14px;font:500 14px 'DM Sans',sans-serif;outline:none}.pe-field input:focus,.pe-field textarea:focus,.pe-row input:focus,.pe-section-row input:focus{border-color:#268d83;box-shadow:0 0 0 3px #bde6dc}.pe-field textarea{min-height:150px;resize:vertical}.pe-hint{color:#6c8189;font-size:12px;line-height:1.45}.pe-image-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.pe-image-card{border:1px solid var(--line);border-radius:16px;padding:14px;background:#f9fbfa}.pe-image-preview{height:175px;border-radius:10px;background:#d6e5e2;overflow:hidden;display:grid;place-items:center;color:#567078;font-size:13px}.pe-image-preview img{width:100%;height:100%;object-fit:cover}.pe-image-card:first-child .pe-image-preview img{object-position:center 25%}.pe-image-card h4{font:700 17px Outfit;margin:16px 0 4px}.pe-image-card p{font-size:12px;margin-bottom:15px}.pe-image-actions{display:flex;gap:8px;flex-wrap:wrap}.pe-button,.pe-image-actions label,.pe-image-actions button{border:1px solid #a7c8c2;color:#164b4e;background:#e8f4f0;border-radius:9px;padding:10px 12px;font:800 12px 'DM Sans',sans-serif;cursor:pointer;text-decoration:none}.pe-image-actions label:hover,.pe-image-actions button:hover,.pe-button:hover{background:#d5ede5}.pe-image-actions input[type=file]{position:absolute;width:1px;height:1px;opacity:0}.pe-image-actions button:disabled{opacity:.5;cursor:not-allowed}.pe-image-card .pe-field{margin:16px 0 5px}.pe-check{display:flex;align-items:center;gap:9px;color:#284854;font-weight:700;font-size:13px}.pe-check input{accent-color:#138a7d;width:16px;height:16px}.pe-errors{background:#fff0ee;border:1px solid #ecbeb8;color:#973e32;border-radius:12px;padding:16px 22px;margin-bottom:18px}.pe-errors ul{margin:8px 0 0;padding-left:20px}.pe-sort-list{display:grid;gap:9px}.pe-row,.pe-section-row{display:flex;gap:10px;align-items:center;padding:10px;border:1px solid var(--line);border-radius:12px;background:#f9fbfa}.pe-row.dragging,.pe-section-row.dragging{opacity:.4}.pe-grip{cursor:grab;color:#638580;font-size:20px;font-weight:700;line-height:1;user-select:none;padding:5px}.pe-remove,.pe-move{border:0;background:#e7efed;color:#365963;padding:10px 12px;border-radius:8px;cursor:pointer;font:800 12px 'DM Sans',sans-serif}.pe-remove:hover{background:#fae5e1;color:#a13d32}.pe-row input{flex:1}.pe-mini-actions{display:flex;gap:4px}.pe-section-row{display:grid;grid-template-columns:28px 90px minmax(0,1fr) 105px;gap:13px}.pe-section-row strong{font:700 13px Outfit}.pe-section-row .pe-mini-actions{grid-column:2 / -1;justify-self:end}.pe-section-row .pe-check{justify-self:end}.pe-add{margin-top:14px}.pe-footer{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:23px 0}.pe-save{background:#103e48;color:white;border:0;border-radius:12px;padding:15px 26px;cursor:pointer;font:800 14px 'DM Sans',sans-serif;box-shadow:0 10px 24px rgba(16,62,72,.18)}.pe-save:hover{background:#17616a;transform:translateY(-2px)}.pe-footer a{color:#42616b;font-weight:700;text-decoration:none}.pe-status{font-size:12px;color:#58757c;margin-top:9px;min-height:18px}.pe-card:target{scroll-margin-top:100px}
.pe-shell{--ink:#202857;--muted:#6e7694;--line:#e1e3f1;--accent:#e4dffa;background:#f2f2f8}.pe-eyebrow{color:#665aae}.pe-hero{background:#25295e}.pe-hero:before{background:linear-gradient(90deg,rgba(29,32,77,.93),rgba(29,32,77,.25)),var(--hero-image);background-size:cover;background-position:center}.pe-nav{background:#e6e5f3}.pe-nav a{color:#555c86}.pe-nav a:hover,.pe-nav a:focus{color:#43379d}.pe-num{color:#584ba9;background:#ece9fc}.pe-image-actions label,.pe-image-actions button,.pe-button{background:#efedfb;border-color:#ccc5ed;color:#50439d}.pe-image-actions label:hover,.pe-image-actions button:hover,.pe-button:hover{background:#e2ddfa}.pe-check input{accent-color:#6557b8}.pe-save{background:#5649aa;box-shadow:0 10px 24px rgba(86,73,170,.18)}.pe-save:hover{background:#423794}.pe-row.dragging,.pe-section-row.dragging{opacity:.55}
body.pe-google-picker-open .picker-dialog-bg{position:fixed!important;inset:0!important;width:100vw!important;height:100vh!important}
body.pe-google-picker-open .picker-dialog{position:fixed!important;top:50vh!important;left:50vw!important;transform:translate(-50%,-50%)!important;margin:0!important;max-width:calc(100vw - 32px)!important;max-height:calc(100vh - 32px)!important}
@media(max-width:900px){.pe-layout{grid-template-columns:1fr}.pe-nav{position:static;display:flex;overflow:auto;gap:5px}.pe-nav a{white-space:nowrap}.pe-nav small{display:none}.pe-image-grid{grid-template-columns:1fr}}@media(max-width:620px){.pe-shell{padding:20px 13px 80px}.pe-hero{min-height:270px}.pe-hero-content{padding:24px;align-items:start;flex-direction:column}.pe-hero-actions{margin:0}.pe-avatar{width:78px;height:78px}.pe-intro{display:block}.pe-grid{grid-template-columns:1fr}.pe-section-row{grid-template-columns:24px minmax(0,1fr) 90px}.pe-section-row strong{display:none}.pe-section-row input[type=text]{grid-column:2 / 3}.pe-section-row .pe-check{grid-column:3}.pe-section-row .pe-mini-actions{grid-column:2 / -1}}@media(prefers-reduced-motion:reduce){.pe-shell *{scroll-behavior:auto!important;transition:none!important;animation:none!important}}
</style>

<main class="pe-shell"><div class="pe-inner">
    <div class="pe-topline"><span class="pe-eyebrow">Tasmiya / Profile studio</span><a href="{{ route('dashboard') }}">Back to dashboard ↗</a></div>
    <div class="pe-hero" style="--hero-image: {{ $banner ? "url('{$banner}')" : 'none' }}">
        <div class="pe-hero-content">
            @if($portrait)<img class="pe-avatar" src="{{ $portrait }}" alt="{{ $profile->user->name }}">@else<div class="pe-avatar pe-avatar-placeholder">{{ strtoupper(substr($profile->user->name,0,1)) }}</div>@endif
            <div><span class="pe-eyebrow" style="color:#b9efe2">Your public presence</span><h1>{{ $profile->user->name }}</h1><p>Make every detail feel unmistakably yours.</p></div>
            <div class="pe-hero-actions"><a href="{{ route('profiles.show',$profile) }}" target="_blank" rel="noopener">View live profile ↗</a></div>
        </div>
    </div>
    <div class="pe-intro"><div><span class="pe-eyebrow">The editing desk</span><h2>Shape your profile.</h2></div><p>Update your imagery, expertise, and the order of your public sections. Changes go live when you save.</p></div>
    @if($errors->any())<div class="pe-errors" role="alert"><strong>Some changes need attention.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="pe-layout">
        <nav class="pe-nav" aria-label="Profile editor sections"><a href="#pe-media">01 &nbsp; Visual identity</a><a href="#pe-story">02 &nbsp; Your story</a><a href="#pe-skills">03 &nbsp; Expertise</a><a href="#pe-sections">04 &nbsp; Page sections</a><a href="#pe-contact">05 &nbsp; Contact & visibility</a><a href="{{ route('profiles.services.index', $profile) }}">Service cards ↗</a><small>Drag rows to reorder, or use the move controls. The public page follows the saved order.</small></nav>
        <form id="pe-form" action="{{ route('profiles.update',$profile) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <section class="pe-card" id="pe-media"><div class="pe-card-head"><div><span class="pe-eyebrow">Visual identity</span><h3>Images that tell your story</h3><p>Your current seeded images appear here too. Replace either with a file, Drive image, or image URL.</p></div><span class="pe-num">01</span></div>
                <div class="pe-image-grid">
                    @foreach(['banner'=>'Cover image','profile'=>'Portrait image'] as $kind=>$label)
                    @php $currentImage = $kind === 'banner' ? $banner : $portrait; @endphp
                    <div class="pe-image-card" data-image-kind="{{ $kind }}">
                        <div class="pe-image-preview" id="{{ $kind }}-preview">@if($currentImage)<img src="{{ $currentImage }}" alt="Current {{ strtolower($label) }}">@else<span>No {{ strtolower($label) }} selected</span>@endif</div>
                        <h4>{{ $label }}</h4><p>{{ $kind === 'banner' ? 'Wide image behind your name.' : 'Your main photo across the team and profile pages.' }}</p>
                        <div class="pe-image-actions"><label for="{{ $kind }}_image">Upload from computer<input type="file" name="{{ $kind }}_image" id="{{ $kind }}_image" accept="image/jpeg,image/png,image/webp,image/gif"></label><button type="button" class="pe-drive" data-target="{{ $kind }}" @disabled(!$driveReady) title="{{ $driveReady ? 'Choose an image from Google Drive' : 'Ask an administrator to configure Google Drive in Integrations' }}">From Google Drive</button><button type="button" class="pe-onedrive" data-target="{{ $kind }}" @disabled(!$oneDriveClientId) title="{{ $oneDriveClientId ? 'Choose an image from OneDrive' : 'Ask an administrator to configure OneDrive in Integrations' }}">From OneDrive</button></div>
                        <div class="pe-field"><label for="{{ $kind }}_image_url">Or paste an image URL</label><input type="url" name="{{ $kind }}_image_url" id="{{ $kind }}_image_url" value="{{ old($kind.'_image_url') }}" placeholder="https://example.com/image.jpg"></div>
                        <label class="pe-check"><input type="checkbox" name="remove_{{ $kind }}_image" value="1" @checked(old('remove_'.$kind.'_image'))> Remove current {{ strtolower($label) }}</label>
                        <div class="pe-status" id="{{ $kind }}-status">{{ $currentImage ? 'Current image is active.' : 'No image is active.' }} Files up to 5 MB.</div>
                    </div>
                    @endforeach
                </div>
            </section>
            <section class="pe-card" id="pe-story"><div class="pe-card-head"><div><span class="pe-eyebrow">Your story</span><h3>Introduce yourself</h3><p>A concise introduction gives visitors the context behind your work.</p></div><span class="pe-num">02</span></div><div class="pe-field"><label for="bio">Biography</label><textarea id="bio" name="bio" maxlength="500" placeholder="What do you do, and what makes your approach distinct?">{{ old('bio',$profile->bio) }}</textarea><span class="pe-hint">Up to 500 characters.</span></div><div class="pe-field" style="max-width:240px"><label for="experience_years">Years of experience</label><input type="number" id="experience_years" name="experience_years" min="0" max="80" value="{{ old('experience_years',$profile->experience_years) }}"></div></section>
            <section class="pe-card" id="pe-skills"><div class="pe-card-head"><div><span class="pe-eyebrow">Expertise</span><h3>Put your best skills first</h3><p>Edit the text directly, drag to arrange, or remove anything that no longer fits.</p></div><span class="pe-num">03</span></div><div id="pe-skills-list" class="pe-sort-list">
                @foreach(old('specializations',$profile->specializations ?? []) as $skill)
                    <div class="pe-row" draggable="true"><span class="pe-grip" aria-hidden="true">⋮⋮</span><input type="text" name="specializations[]" maxlength="50" value="{{ $skill }}" aria-label="Specialization"><div class="pe-mini-actions"><button type="button" class="pe-move" data-move="up" aria-label="Move skill up">↑</button><button type="button" class="pe-move" data-move="down" aria-label="Move skill down">↓</button><button type="button" class="pe-remove" data-remove="skill">Remove</button></div></div>
                @endforeach
            </div><button type="button" id="pe-add-skill" class="pe-button pe-add">+ Add a skill</button><p class="pe-hint" style="margin-top:10px">Up to 10 skills. Their order is reflected on your live profile.</p></section>
            <section class="pe-card" id="pe-sections"><div class="pe-card-head"><div><span class="pe-eyebrow">Page composition</span><h3>Arrange the experience</h3><p>Rename, show or hide, and reorder the main sections of your public profile.</p></div><span class="pe-num">04</span></div><input type="hidden" name="section_order" id="section_order" value="{{ old('section_order',$sections->pluck('key')->implode(',')) }}"><div id="pe-sections-list" class="pe-sort-list">
                @foreach($sections as $section)
                <div class="pe-section-row" draggable="true" data-key="{{ $section['key'] }}"><span class="pe-grip" aria-hidden="true">⋮⋮</span><strong>{{ ucfirst($section['key']) }}</strong><input type="text" name="section_titles[{{ $section['key'] }}]" maxlength="60" value="{{ old('section_titles.'.$section['key'],$section['title']) }}" aria-label="{{ ucfirst($section['key']) }} section title"><label class="pe-check"><input type="hidden" name="section_visibility[{{ $section['key'] }}]" value="0"><input type="checkbox" name="section_visibility[{{ $section['key'] }}]" value="1" @checked(old('section_visibility.'.$section['key'],$section['visible']))> Visible</label>@if(in_array($section['key'],['projects','services','testimonials']))<input class="pe-description" type="text" name="section_descriptions[{{ $section['key'] }}]" maxlength="160" value="{{ old('section_descriptions.'.$section['key'],$section['description']) }}" placeholder="Optional introduction for this section" aria-label="{{ ucfirst($section['key']) }} section introduction">@endif<div class="pe-mini-actions"><button type="button" class="pe-move" data-move="up" aria-label="Move {{ $section['key'] }} section up">↑</button><button type="button" class="pe-move" data-move="down" aria-label="Move {{ $section['key'] }} section down">↓</button></div></div>
                @endforeach
            </div><p class="pe-hint" style="margin-top:15px">These controls change section introductions and order. Your service cards, prices, tags, and icons have their own editor.</p><a class="pe-button" style="display:inline-block;margin-top:12px" href="{{ route('profiles.services.index', $profile) }}">Manage my service cards ↗</a></section>
            <section class="pe-card" id="pe-contact"><div class="pe-card-head"><div><span class="pe-eyebrow">Availability</span><h3>Stay in reach</h3><p>Decide how people contact you and whether your profile appears in the team showcase.</p></div><span class="pe-num">05</span></div><div class="pe-grid"><div class="pe-field"><label for="whatsapp">WhatsApp number</label><input type="tel" id="whatsapp" name="social_links[whatsapp]" value="{{ old('social_links.whatsapp',$profile->social_links['whatsapp'] ?? '') }}" placeholder="+92 300 1234567"></div><div class="pe-field"><label for="linkedin">LinkedIn URL</label><input type="url" id="linkedin" name="social_links[linkedin]" value="{{ $linkedIn }}" placeholder="https://linkedin.com/in/..."></div></div><input type="hidden" name="is_visible" value="0"><label class="pe-check"><input type="checkbox" name="is_visible" value="1" @checked(old('is_visible',$profile->is_visible))> Show this profile in the public team showcase</label></section>
            <div class="pe-footer"><a href="{{ route('profiles.show',$profile) }}">Cancel and view profile</a><button type="submit" class="pe-save">Save profile changes →</button></div>
        </form>
    </div>
</div></main>
@if($oneDriveClientId)
<div id="onedrive-picker" data-client-id="{{ $oneDriveClientId }}" hidden role="dialog" aria-modal="true" aria-label="Choose an image from OneDrive" style="position:fixed;inset:0;z-index:1000;background:#141335a8;display:none;align-items:center;justify-content:center;padding:20px"><div style="background:white;border-radius:18px;padding:25px;width:min(620px,100%);max-height:80vh;overflow:auto"><div style="display:flex;justify-content:space-between;align-items:center"><h2 style="font:700 27px Outfit;color:#202857">Choose from OneDrive</h2><button type="button" id="onedrive-close" aria-label="Close" style="border:0;background:#eeeafa;border-radius:8px;padding:8px 12px;cursor:pointer">Close</button></div><p id="onedrive-status" style="color:#626d89;font-size:13px;margin:14px 0"></p><button type="button" id="onedrive-back" style="border:0;background:#eeeafa;border-radius:8px;padding:8px 12px;cursor:pointer;display:none">Back</button><div id="onedrive-list" style="display:grid;gap:8px;margin-top:14px"></div><button type="button" id="onedrive-more" style="border:0;background:#eeeafa;border-radius:8px;padding:9px 14px;cursor:pointer;display:none;margin-top:12px">Load more</button></div></div>
@vite('resources/js/onedrive-picker.js')
@endif
@if($driveReady)
<script src="https://apis.google.com/js/api.js" async defer></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="pe-picker-help" hidden role="status" style="position:fixed;z-index:1000000;left:50%;bottom:20px;transform:translateX(-50%);width:min(520px,calc(100vw - 32px));background:#102a39;color:white;border-radius:12px;padding:13px 16px;box-shadow:0 18px 40px #102a3955;font:600 13px 'DM Sans',sans-serif;line-height:1.5"><span>If Google Picker stays blank here, close it and try this same page in Chrome. Embedded browsers can block Google’s frame.</span> <button type="button" id="pe-picker-close" style="margin-left:8px;border:0;border-radius:7px;background:#bce8da;color:#102a39;padding:7px 10px;font-weight:800;cursor:pointer">Close picker</button></div>
@endif
<script>
document.addEventListener('DOMContentLoaded', () => {
    const skillList = document.getElementById('pe-skills-list');
    const sectionList = document.getElementById('pe-sections-list');
    const orderInput = document.getElementById('section_order');
    const updateOrder = () => { orderInput.value = [...sectionList.children].map(row => row.dataset.key).join(','); };
    document.getElementById('pe-add-skill').addEventListener('click', () => {
        if (skillList.children.length >= 10) return;
        const row = document.createElement('div');
        row.className = 'pe-row'; row.draggable = true;
        row.innerHTML = '<span class="pe-grip" aria-hidden="true">⋮⋮</span><input type="text" name="specializations[]" maxlength="50" aria-label="Specialization" placeholder="New skill"><div class="pe-mini-actions"><button type="button" class="pe-move" data-move="up" aria-label="Move skill up">↑</button><button type="button" class="pe-move" data-move="down" aria-label="Move skill down">↓</button><button type="button" class="pe-remove" data-remove="skill">Remove</button></div>';
        skillList.appendChild(row); row.querySelector('input').focus();
    });
    document.getElementById('pe-form').addEventListener('click', event => {
        const button = event.target.closest('button[data-remove],button[data-move]'); if (!button) return;
        const row = button.closest('.pe-row,.pe-section-row'); const list = row.parentElement;
        if (button.dataset.remove) row.remove();
        else if (button.dataset.move === 'up' && row.previousElementSibling) list.insertBefore(row,row.previousElementSibling);
        else if (button.dataset.move === 'down' && row.nextElementSibling) list.insertBefore(row.nextElementSibling,row);
        updateOrder();
    });
    [skillList,sectionList].forEach(list => {
        let dragged;
        list.addEventListener('dragstart', event => { dragged = event.target.closest('.pe-row,.pe-section-row'); if (!dragged) return; dragged.classList.add('dragging'); event.dataTransfer.effectAllowed = 'move'; });
        list.addEventListener('dragover', event => { if (!dragged) return; event.preventDefault(); const over = event.target.closest('.pe-row,.pe-section-row'); if (over && over !== dragged && over.parentElement === list) { const before = event.clientY < over.getBoundingClientRect().top + over.offsetHeight / 2; list.insertBefore(dragged,before ? over : over.nextSibling); updateOrder(); } });
        list.addEventListener('dragend', () => { dragged?.classList.remove('dragging'); dragged = null; updateOrder(); });
    });
    for (const kind of ['banner','profile']) {
        const input = document.getElementById(kind+'_image'); const preview = document.getElementById(kind+'-preview'); const status = document.getElementById(kind+'-status');
        input.addEventListener('change', () => { const file = input.files[0]; if (!file) return; if (file.size > 5*1024*1024) { status.textContent='Image must be 5 MB or less.'; input.value=''; return; } const url = URL.createObjectURL(file); preview.replaceChildren(Object.assign(document.createElement('img'),{src:url,alt:'New '+kind+' preview',onload:()=>URL.revokeObjectURL(url)})); status.textContent='New image selected: '+file.name; document.querySelector('[name="remove_'+kind+'_image"]').checked=false; });
    }
    @if($driveReady)
    const driveConfig = @json($driveConfig);
    let tokenClient, accessToken, pickerReady=false, activeKind, activePicker, pickerHelpTimer, pickerOpenScrollY=0, priorBodyOverflow='';
    const pickerHelp = document.getElementById('pe-picker-help');
    document.body.appendChild(pickerHelp);
    const closePicker = () => { clearTimeout(pickerHelpTimer); pickerHelp.hidden=true; const picker=activePicker; activePicker=null; picker?.dispose(); document.body.classList.remove('pe-google-picker-open'); document.body.style.overflow=priorBodyOverflow; };
    document.getElementById('pe-picker-close').addEventListener('click',()=>{closePicker();setStatus('Google Picker closed. If it stayed blank, retry this page in Chrome.');});
    const setStatus = message => { document.getElementById(activeKind+'-status').textContent=message; };
    const loadPicker = () => new Promise((resolve,reject) => { if (pickerReady) return resolve(); if (!window.gapi) return reject(new Error('Google Picker is still loading. Try again in a moment.')); gapi.load('picker',{callback:()=>{pickerReady=true;resolve();},onerror:()=>reject(new Error('Google Picker could not load.'))}); });
    async function openPicker() {
        await loadPicker();
        if (!window.google?.accounts?.oauth2) throw new Error('Google sign-in is still loading. Try again in a moment.');
        if (!tokenClient) tokenClient = google.accounts.oauth2.initTokenClient({client_id:driveConfig.clientId,scope:'https://www.googleapis.com/auth/drive.file',callback:response=>{ if(response.error){setStatus('Google authorization failed. Check the OAuth web client and authorized JavaScript origin.');return;} accessToken=response.access_token; try { showPicker(); } catch(error) { closePicker(); setStatus(error.message || 'Google Picker could not open.'); } },error_callback:()=>setStatus('Google sign-in did not complete. If Google says “no registered origin,” add '+window.location.origin+' to the OAuth web client’s Authorized JavaScript origins.')});
        setStatus('Opening Google sign-in…');
        tokenClient.requestAccessToken({prompt:accessToken?'':'consent'});
    }
    function showPicker() {
        closePicker();
        priorBodyOverflow=document.body.style.overflow;
        document.body.classList.add('pe-google-picker-open');
        activePicker = new google.picker.PickerBuilder().addView(new google.picker.DocsView(google.picker.ViewId.DOCS_IMAGES).setMode(google.picker.DocsViewMode.LIST)).setOAuthToken(accessToken).setDeveloperKey(driveConfig.apiKey).setAppId(driveConfig.appId).setOrigin(window.location.origin).setCallback(async data => {
            if (data.action === google.picker.Action.ERROR) { closePicker(); setStatus('Google Picker could not load. Check that Google Picker API and Drive API are enabled in the same Cloud project.'); return; }
            if (data.action === google.picker.Action.CANCEL) { closePicker(); setStatus('No Drive image selected.'); return; }
            if (data.action !== google.picker.Action.PICKED) return;
            closePicker();
            const selected = data.docs[0]; setStatus('Importing '+selected.name+' from Google Drive…');
            try {
                const response = await fetch('https://www.googleapis.com/drive/v3/files/'+encodeURIComponent(selected.id)+'?alt=media',{headers:{Authorization:'Bearer '+accessToken}});
                if (!response.ok) throw new Error('Google Drive could not download this image.');
                const blob = await response.blob();
                if (!blob.type.startsWith('image/')) throw new Error('Please choose an image file.');
                if (blob.size > 5*1024*1024) throw new Error('Please choose an image under 5 MB.');
                const file = new File([blob],selected.name || 'drive-image',{type:blob.type}); const transfer = new DataTransfer(); transfer.items.add(file);
                const input = document.getElementById(activeKind+'_image'); input.files=transfer.files; input.dispatchEvent(new Event('change',{bubbles:true}));
                setStatus('Drive image imported. Save changes to publish it.');
                if (driveConfig.canVerify) {
                    try {
                        await fetch(driveConfig.verifyUrl,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({file_id:selected.id,access_token:accessToken})});
                    } catch (_) { /* The image is already imported; verification can be retried later. */ }
                }
            } catch(error) { setStatus(error.message); }
        }).build();
        activePicker.setVisible(true);
        document.body.style.overflow='hidden';
        requestAnimationFrame(()=>window.scrollTo(0,pickerOpenScrollY));
        setStatus('Google Picker opened. Choose an image, or close it if it stays blank.');
        pickerHelpTimer = setTimeout(()=>{ if (activePicker?.isVisible()) pickerHelp.hidden=false; },5000);
    }
    document.querySelectorAll('.pe-drive').forEach(button=>button.addEventListener('click',async()=>{activeKind=button.dataset.target;pickerOpenScrollY=window.scrollY;try{await openPicker();}catch(error){closePicker();setStatus(error.message);}}));
    @endif
});
</script>
@endsection
