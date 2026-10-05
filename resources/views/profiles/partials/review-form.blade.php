@php
    $useOld = (string)old('editing_review', '') === (string)($review?->id ?? '');
    $value = fn($field, $default = '') => $useOld ? old($field, $review?->$field ?? $default) : ($review?->$field ?? $default);
    $serviceId = $review && $review->testimonialable_type === \App\Models\Service::class ? $review->testimonialable_id : '';
    if($useOld) $serviceId = old('service_id', $serviceId);
@endphp
<input type="hidden" name="editing_review" value="{{ $review?->id }}">
<div class="pr-grid">
    <label>Client name<input name="author_name" maxlength="100" required value="{{ $value('author_name') }}"></label>
    <label>Rating<select name="rating" required>@foreach(range(5,1) as $rating)<option value="{{ $rating }}" @selected((int)$value('rating', 5) === $rating)>{{ $rating }} out of 5 stars</option>@endforeach</select></label>
    <label>Position (optional)<input name="author_position" maxlength="100" value="{{ $value('author_position') }}"></label>
    <label>Company (optional)<input name="author_company" maxlength="100" value="{{ $value('author_company') }}"></label>
    <label>Related service<select name="service_id"><option value="">General feedback about this person</option>@foreach($services as $service)<option value="{{ $service->id }}" @selected((string)$serviceId === (string)$service->id)>{{ $service->name }}</option>@endforeach</select></label>
    <label>Visibility<select name="is_approved"><option value="0" @selected(!(bool)$value('is_approved', false))>Hidden / pending</option><option value="1" @selected((bool)$value('is_approved', false))>Published</option></select></label>
    <label class="pr-wide">Client feedback<textarea name="content" minlength="20" maxlength="500" required>{{ $value('content') }}</textarea></label>
    @unless($review)<div class="pr-wide"><button class="pr-save" type="submit">Add review</button></div>@endunless
</div>
