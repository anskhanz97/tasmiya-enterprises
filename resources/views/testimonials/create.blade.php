@extends('layouts.app')
@section('title', 'Share Your Feedback | Tasmiya Enterprises')
@section('content')
@php
    $backUrl = $service
        ? route('services.show', $service).($selectedProfileId ? '?profile='.$selectedProfileId : '')
        : ($selectedProfileId ? route('profiles.show', $selectedProfileId).'#profile-reviews' : route('services.index'));
@endphp
<style>
.feedback-page{--fb-ink:#261b45;--fb-violet:#7052b4;--fb-muted:#726981;--fb-line:#e2daee;background:#f4f1f8;padding:48px 20px 80px;color:var(--fb-ink)}
.feedback-wrap{max-width:1120px;margin:auto}.feedback-back{display:inline-block;color:#655094;text-decoration:none;margin-bottom:24px;font-size:14px}.feedback-layout{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);background:white;border:1px solid var(--fb-line);border-radius:24px;overflow:hidden;box-shadow:0 18px 50px #30204b0b}.feedback-intro{padding:clamp(26px,4vw,46px);background:linear-gradient(155deg,#261b45,#553987);color:white}.feedback-intro h1{font-size:clamp(34px,4vw,52px);line-height:1.08;margin:0 0 22px}.feedback-intro p{color:#ded4ee;font-size:15px;line-height:1.8;margin:0}.feedback-context{margin-top:36px;padding:22px 0;border-top:1px solid #ffffff35;border-bottom:1px solid #ffffff35}.feedback-context small{color:#d6c9e9}.feedback-context h2{font-size:23px;line-height:1.3;margin:8px 0}.feedback-note{margin-top:32px!important;font-size:13px!important}.feedback-form{padding:clamp(24px,4vw,44px);min-width:0}.feedback-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.feedback-field{min-width:0}.feedback-wide{grid-column:1/-1}.feedback-field>label,.feedback-form legend{display:block;font-size:14px;font-weight:700;margin-bottom:9px}.feedback-form input:not([type=radio]),.feedback-form select,.feedback-form textarea{display:block;width:100%;box-sizing:border-box;background:#fcfbfe;border:1px solid #d9d0e5;border-radius:10px;padding:12px 14px;font:inherit;font-size:14px;color:var(--fb-ink)}.feedback-form textarea{min-height:150px;resize:vertical;line-height:1.7}.feedback-form input::placeholder,.feedback-form textarea::placeholder{color:#8d839b}.feedback-form :is(input,select,textarea):focus{outline:3px solid #b6a1dc55;border-color:#8664bf}.feedback-help{display:block;color:var(--fb-muted);font-size:12px;line-height:1.6;margin-top:7px}.feedback-form fieldset{border:0;padding:0;margin:0;min-width:0}.feedback-stars{display:flex;gap:8px;flex-wrap:wrap}.feedback-star{position:relative}.feedback-star input{position:absolute;width:1px;height:1px;opacity:0}.feedback-star label{display:grid;place-items:center;width:48px;height:48px;border:1px solid #d9d0e5;border-radius:12px;font-size:29px;line-height:1;cursor:pointer;color:#b6adc5;background:#fcfbfe;transition:background .15s,color .15s}.feedback-star label.is-filled{color:#bd8113;background:#fff7e5;border-color:#e2c992}.feedback-star input:checked+label{border-color:#8057b5;box-shadow:0 0 0 2px #8057b522}.feedback-star input:focus-visible+label{outline:3px solid #8c6ac1;outline-offset:3px}.feedback-error{color:#a33247;font-size:12px;margin-top:7px}.feedback-errors{border:1px solid #edc8cf;background:#fff4f6;padding:15px;border-radius:12px;margin-bottom:22px}.feedback-errors p{margin:3px 0;font-size:13px}.feedback-actions{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:28px;padding-top:24px;border-top:1px solid var(--fb-line)}.feedback-actions small{color:var(--fb-muted);font-size:12px;max-width:190px;line-height:1.6}.feedback-submit{border:0;border-radius:11px;background:var(--fb-violet);color:white;padding:14px 22px;font:inherit;font-size:14px;font-weight:700;cursor:pointer}.feedback-submit:hover{background:#57378f}.feedback-back:focus-visible,.feedback-submit:focus-visible{outline:3px solid #9274c3;outline-offset:4px}@media(max-width:760px){.feedback-layout{grid-template-columns:1fr}.feedback-context{margin-top:24px}.feedback-note{margin-top:20px!important}.feedback-page{padding:28px 16px 50px}}@media(max-width:440px){.feedback-fields{grid-template-columns:1fr}.feedback-actions{align-items:flex-start;flex-direction:column}.feedback-submit{width:100%}.feedback-actions small{max-width:none}.feedback-stars{gap:6px}.feedback-star label{width:42px;height:42px}}@media(prefers-reduced-motion:reduce){.feedback-star label{transition:none}}
</style>
<main class="feedback-page"><div class="feedback-wrap">
    <a class="feedback-back" href="{{ $backUrl }}">← Back to {{ $service ? 'service' : ($selectedProfileId ? 'profile' : 'services') }}</a>
    <div class="feedback-layout">
        <aside class="feedback-intro">
            <h1>Share your experience.</h1>
            <p>Your feedback helps other clients choose the right specialist and helps us improve the work we do.</p>
            @if($service)<div class="feedback-context"><small>You’re reviewing</small><h2>{{ $service->name }}</h2><p>{{ $service->division?->name }}</p></div>@endif
            <p class="feedback-note">Reviews are checked before publication. Please share genuine feedback, and avoid including private or sensitive information.</p>
        </aside>
        <section class="feedback-form" aria-label="Client review form">
            @if($errors->any())<div class="feedback-errors" role="alert"><strong>Please check your review</strong>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <form action="{{ route('testimonials.store') }}" method="POST">
                @csrf
                <div class="feedback-fields">
                    @if($service)<input type="hidden" name="service_id" value="{{ $service->id }}">
                    @else<div class="feedback-field feedback-wide"><label for="service_id">Service (optional)</label><select id="service_id" name="service_id"><option value="">General feedback about a specialist</option>@foreach($services as $svc)<option value="{{ $svc->id }}" @selected(old('service_id') == $svc->id)>{{ $svc->name }} — {{ $svc->division?->name }}</option>@endforeach</select></div>@endif
                    <div class="feedback-field feedback-wide"><label for="profile_id">Specialist (optional)</label><select id="profile_id" name="profile_id"><option value="">{{ $service ? 'Feedback about this service' : 'Choose a specialist' }}</option>@foreach($profiles as $person)<option value="{{ $person->id }}" @selected(old('profile_id', $selectedProfileId) == $person->id)>{{ $person->user->name }}</option>@endforeach</select><small class="feedback-help">Choose the person you worked with so your review reaches their profile.</small></div>
                    <fieldset class="feedback-wide"><legend>How would you rate your experience?</legend><div class="feedback-stars">@foreach(range(1,5) as $rating)<div class="feedback-star"><input id="rating_{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" @checked(old('rating') == $rating) required><label for="rating_{{ $rating }}"><span aria-hidden="true">★</span><span class="feedback-sr" style="position:absolute;clip-path:inset(50%);width:1px;height:1px;overflow:hidden">{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</span></label></div>@endforeach</div><small class="feedback-help" id="ratingLabel" aria-live="polite">Select a rating</small></fieldset>
                    <div class="feedback-field feedback-wide"><label for="content">Your review</label><textarea id="content" name="content" minlength="20" maxlength="500" required aria-describedby="contentCounter" placeholder="What went well? What could we do better?">{{ old('content') }}</textarea><small class="feedback-help" id="contentCounter">20–500 characters</small></div>
                    <div class="feedback-field feedback-wide"><label for="author_name">Your name</label><input id="author_name" name="author_name" value="{{ old('author_name') }}" maxlength="100" autocomplete="name" required placeholder="Full name"></div>
                    <div class="feedback-field"><label for="author_position">Job title (optional)</label><input id="author_position" name="author_position" value="{{ old('author_position') }}" maxlength="100" autocomplete="organization-title" placeholder="e.g. Business owner"></div>
                    <div class="feedback-field"><label for="author_company">Company (optional)</label><input id="author_company" name="author_company" value="{{ old('author_company') }}" maxlength="100" autocomplete="organization" placeholder="Company name"></div>
                    <div class="feedback-field feedback-wide"><label for="author_image_url">Photo URL (optional)</label><input id="author_image_url" name="author_image_url" type="url" value="{{ old('author_image_url') }}" placeholder="https://example.com/photo.jpg"></div>
                </div>
                <div class="feedback-actions"><small>Your name and feedback will be visible once approved.</small><button class="feedback-submit" type="submit">Submit review</button></div>
            </form>
        </section>
    </div>
</div></main>
<script>
(() => {
    const inputs = [...document.querySelectorAll('.feedback-star input')];
    const labels = [...document.querySelectorAll('.feedback-star label')];
    const names = ['Poor', 'Fair', 'Good', 'Very good', 'Excellent'];
    const status = document.getElementById('ratingLabel');
    const paint = value => labels.forEach((label, index) => label.classList.toggle('is-filled', index < value));
    const update = () => {
        const value = Number(inputs.find(input => input.checked)?.value || 0);
        paint(value);
        status.textContent = value ? `${value} out of 5 — ${names[value - 1]}` : 'Select a rating';
    };
    inputs.forEach(input => input.addEventListener('change', update));
    labels.forEach((label, index) => label.addEventListener('mouseenter', () => paint(index + 1)));
    document.querySelector('.feedback-stars').addEventListener('mouseleave', update);
    const content = document.getElementById('content');
    const count = () => document.getElementById('contentCounter').textContent = `${content.value.length} / 500 characters (minimum 20)`;
    content.addEventListener('input', count);
    count(); update();
})();
</script>
@endsection
