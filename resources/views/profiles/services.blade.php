@extends('layouts.app')

@section('title', 'Manage services | ' . $profile->user->name)

@section('content')
<style>
.so-shell{--ink:#1a2b4e;--muted:#66748b;--line:#dce3f1;background:#f2f3f9;min-height:100vh;padding:38px clamp(18px,4vw,64px) 90px;color:var(--ink)}
.so-inner{max-width:1120px;margin:auto}.so-back{display:inline-block;margin-bottom:24px;color:#504aa4;font-size:14px;font-weight:800;text-decoration:none}.so-back:hover{text-decoration:underline}.so-head{display:flex;justify-content:space-between;gap:22px;align-items:end;margin-bottom:28px}.so-head h1{font-size:clamp(34px,4vw,54px);letter-spacing:-.05em;line-height:1.02;margin:0 0 12px;color:#20275a}.so-head p{max-width:620px;color:var(--muted);line-height:1.6;margin:0}.so-count{flex:none;color:#5149a8;font-weight:800;border:1px solid #c9c8e7;border-radius:10px;padding:10px 14px;background:#fff}
.so-alert{border-radius:12px;padding:15px 18px;margin-bottom:20px}.so-alert--success{background:#e4f6ed;color:#116143}.so-alert--error{background:#fff0ee;color:#9b3e3a}.so-alert ul{margin:6px 0 0;padding-left:20px}
.so-list{display:grid;gap:14px;margin-bottom:38px}.so-item{background:#fff;border:1px solid var(--line);border-radius:18px;padding:18px 20px}.so-item__summary{display:grid;grid-template-columns:68px minmax(0,1fr) auto;align-items:center;gap:18px}.so-item__icon{display:grid;place-items:center;width:68px;height:68px;color:#4e4ba4;background:#f0f0fa;border-radius:14px}.so-item__icon .work-icon{width:49px;height:49px}.so-item h2{font-size:21px;line-height:1.2;margin:0 0 6px;color:#1f2a56}.so-item p{color:var(--muted);font-size:13px;line-height:1.5;margin:0}.so-item__meta{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:8px;font-size:12px;color:#66748b}.so-item__meta strong{color:#1b3757}.so-item__actions{display:flex;gap:8px;align-items:center}.so-item summary{list-style:none;cursor:pointer;border:1px solid #c9c8e7;background:#f4f3fc;color:#4e469c;border-radius:9px;padding:10px 15px;font-size:12px;font-weight:800}.so-item summary::-webkit-details-marker{display:none}.so-item summary:hover{background:#e9e7f9}.so-delete{border:1px solid #e7c9c9;background:#fff6f5;color:#a33938;border-radius:9px;padding:10px 14px;font-size:12px;font-weight:800;cursor:pointer}.so-delete:hover{background:#ffe9e7}.so-item__edit{border-top:1px solid var(--line);margin-top:20px;padding-top:20px}.so-empty{padding:26px;border:1px dashed #b7c3dc;border-radius:16px;background:#fff;color:var(--muted)}
.so-create{background:#fff;border:1px solid var(--line);border-radius:22px;padding:clamp(22px,3vw,36px);box-shadow:0 16px 40px rgba(31,42,86,.05)}.so-create h2{font-size:28px;margin:0 0 7px;color:#20275a;letter-spacing:-.035em}.so-create>p{color:var(--muted);font-size:14px;margin:0 0 24px}.so-mode{display:flex;gap:9px;flex-wrap:wrap;margin-bottom:20px}.so-mode label{cursor:pointer}.so-mode input{position:absolute;opacity:0}.so-mode span{display:inline-block;padding:11px 15px;border:1px solid #ccd3e7;border-radius:10px;color:#4f5c79;font-size:13px;font-weight:800}.so-mode input:checked+span{color:#fff;background:#5149a8;border-color:#5149a8}.so-mode input:focus-visible+span,.so-icon-choice input:focus-visible+span{outline:3px solid #897eef;outline-offset:2px}.so-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px}.so-field{display:flex;flex-direction:column;gap:8px}.so-field--wide{grid-column:1/-1}.so-field label,.so-icons legend{font-size:13px;font-weight:800;color:#26365e}.so-field input,.so-field textarea,.so-field select{width:100%;border:1px solid #cbd4e8;background:#fbfcff;border-radius:9px;padding:11px 13px;color:#203057;font:inherit;font-size:14px}.so-field textarea{min-height:94px;resize:vertical}.so-field input:focus,.so-field textarea:focus,.so-field select:focus{outline:3px solid #ded9fa;border-color:#6f63bc}.so-hint{font-size:12px;line-height:1.5;color:#73819a}.so-icons{border:0;padding:0;margin:22px 0 0}.so-icons legend{margin-bottom:10px}.so-icons__grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:9px}.so-icon-choice{cursor:pointer}.so-icon-choice input{position:absolute;opacity:0}.so-icon-choice span{display:grid;justify-items:center;gap:5px;padding:10px 5px;border:1px solid #d7def0;border-radius:10px;color:#5260a3;background:#fafbff;text-align:center}.so-icon-choice input:checked+span{border-color:#6457b6;background:#eeecfa;box-shadow:inset 0 0 0 1px #6457b6}.so-icon-choice .work-icon{width:38px;height:38px}.so-icon-choice small{font-size:10px;line-height:1.3;color:#56617b}.so-submit{margin-top:22px;background:#203e60;color:#fff;border:0;border-radius:9px;padding:12px 18px;font-size:13px;font-weight:800;cursor:pointer}.so-submit:hover{background:#5149a8}.so-create[hidden],.so-fields[hidden]{display:none!important}
@media(max-width:750px){.so-head{display:block}.so-count{display:inline-block;margin-top:17px}.so-item__summary{grid-template-columns:58px minmax(0,1fr)}.so-item__icon{width:58px;height:58px}.so-item__actions{grid-column:1/-1}.so-fields{grid-template-columns:1fr}.so-icons__grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(prefers-reduced-motion:reduce){.so-shell *{scroll-behavior:auto!important;transition:none!important}}
.so-field[hidden]{display:none!important}
</style>
<main class="so-shell"><div class="so-inner">
    <a class="so-back" href="{{ route('profiles.edit', $profile) }}">← Back to profile editor</a>
    <header class="so-head"><div><h1>{{ $profile->user->name }}'s service cards</h1><p>Manage the services shown on this profile and in the public catalogue. Prices and wording belong to this listing; another specialist's card will not change.</p></div><span class="so-count">{{ $offerings->count() }} {{ \Illuminate\Support\Str::plural('service', $offerings->count()) }}</span></header>
    @if(session('success'))<div class="so-alert so-alert--success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="so-alert so-alert--error" role="alert"><strong>Check these fields:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section aria-label="Current service cards" class="so-list">
        @forelse($offerings as $offering)
            <article class="so-item" id="offering-{{ $offering->id }}">
                <div class="so-item__summary">
                    <div class="so-item__icon"><x-work-icon :type="$offering->iconKey()" /></div>
                    <div><h2>{{ $offering->title() }}</h2><p>{{ \Illuminate\Support\Str::limit($offering->description(), 130) }}</p><div class="so-item__meta"><strong>{{ $offering->currency() }} {{ number_format((float) $offering->price()) }}</strong><span>{{ count($offering->tags()) }} {{ \Illuminate\Support\Str::plural('tag', count($offering->tags())) }}</span><span>{{ $offering->service->division->name }}</span></div></div>
                    <div class="so-item__actions"><details><summary>Edit card</summary></details><form method="POST" action="{{ route('profiles.services.destroy', [$profile, $offering]) }}" onsubmit="return confirm('Remove this card from your profile and the catalogue?')">@csrf @method('DELETE')<button class="so-delete" type="submit">Remove</button></form></div>
                </div>
                <form class="so-item__edit" method="POST" action="{{ route('profiles.services.update', [$profile, $offering]) }}" hidden>@csrf @method('PUT')
                    <div class="so-fields">
                        <div class="so-field"><label for="title-{{ $offering->id }}">Card title</label><input id="title-{{ $offering->id }}" name="card_title" maxlength="100" required value="{{ $offering->title() }}"></div>
                        <div class="so-field"><label for="price-{{ $offering->id }}">Your starting price</label><input id="price-{{ $offering->id }}" name="price" type="number" min="0" max="999999.99" step="0.01" required value="{{ $offering->price() }}"></div>
                        <div class="so-field so-field--wide"><label for="description-{{ $offering->id }}">Description</label><textarea id="description-{{ $offering->id }}" name="card_description" maxlength="500" required>{{ $offering->description() }}</textarea></div>
                        <div class="so-field"><label for="currency-{{ $offering->id }}">Currency</label><select id="currency-{{ $offering->id }}" name="currency">@foreach(['PKR','USD','EUR','GBP'] as $currency)<option value="{{ $currency }}" @selected($offering->currency() === $currency)>{{ $currency }}</option>@endforeach</select></div>
                        <div class="so-field"><label for="tags-{{ $offering->id }}">Tags</label><input id="tags-{{ $offering->id }}" name="tags" maxlength="200" value="{{ implode(', ', $offering->tags()) }}"><span class="so-hint">Separate up to four short tags with commas.</span></div>
                    </div>
                    @include('profiles.partials.icon-gallery', ['selectedIcon' => $offering->iconKey()])
                    <button class="so-submit" type="submit">Save this card</button>
                </form>
            </article>
        @empty
            <div class="so-empty">No service cards yet. Add a service below to appear in the catalogue.</div>
        @endforelse
    </section>

    <section class="so-create" aria-labelledby="so-add-title"><h2 id="so-add-title">Add a service card</h2><p>Choose a service from your division or introduce a new one.</p>
        <form method="POST" action="{{ route('profiles.services.store', $profile) }}">@csrf
            <div class="so-mode" role="group" aria-label="Service source"><label><input type="radio" name="source" value="existing" @checked(old('source', $availableServices->isNotEmpty() ? 'existing' : 'new') === 'existing')><span>Use an existing service</span></label><label><input type="radio" name="source" value="new" @checked(old('source', $availableServices->isNotEmpty() ? 'existing' : 'new') === 'new')><span>Create a new service</span></label></div>
            <div class="so-fields">
                <div class="so-field so-field--wide" data-source="existing"><label for="service_id">Service</label><select id="service_id" name="service_id"><option value="">Choose a service</option>@foreach($availableServices as $service)<option value="{{ $service->id }}" data-price="{{ $service->base_price }}" data-currency="{{ $service->currency }}" data-icon="{{ config('service_cards.services.' . $service->slug . '.icon', 'audit') }}" data-tags="{{ implode(', ', config('service_cards.services.' . $service->slug . '.focus', [])) }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>@endforeach</select>@if($availableServices->isEmpty())<span class="so-hint">All existing services in your division are already listed. Create a new one instead.</span>@endif</div>
                <div class="so-field" data-source="new"><label for="new_title">New service title</label><input id="new_title" name="card_title" maxlength="100" value="{{ old('card_title') }}" placeholder="e.g., Business website launch"></div>
                <div class="so-field so-field--wide" data-source="new"><label for="new_description">Description</label><textarea id="new_description" name="card_description" maxlength="500" placeholder="What will the client receive?">{{ old('card_description') }}</textarea></div>
                <div class="so-field"><label for="new_price">Your starting price</label><input id="new_price" name="price" type="number" min="0" max="999999.99" step="0.01" required value="{{ old('price') }}" placeholder="0.00"></div>
                <div class="so-field"><label for="new_currency">Currency</label><select id="new_currency" name="currency">@foreach(['PKR','USD','EUR','GBP'] as $currency)<option value="{{ $currency }}" @selected(old('currency', 'PKR') === $currency)>{{ $currency }}</option>@endforeach</select></div>
                <div class="so-field so-field--wide"><label for="new_tags">Tags</label><input id="new_tags" name="tags" maxlength="200" value="{{ old('tags') }}" placeholder="Planning, Setup, Ongoing support"><span class="so-hint">Separate up to four short tags with commas.</span></div>
            </div>
            @include('profiles.partials.icon-gallery', ['selectedIcon' => old('icon_key', config('service_cards.divisions.' . ($profile->user->division?->slug ?? '') . '.icon', 'audit'))])
            <button class="so-submit" type="submit">Add service card</button>
        </form>
    </section>
</div></main>
<script>
document.querySelectorAll('.so-item').forEach(item => {
    const details = item.querySelector('details');
    const form = item.querySelector('.so-item__edit');
    details.addEventListener('toggle', () => { form.hidden = !details.open; });
});
const sourceChoices = document.querySelectorAll('input[name="source"]');
function syncSource() {
    const selected = document.querySelector('input[name="source"]:checked')?.value;
    document.querySelectorAll('[data-source]').forEach(field => {
        field.hidden = field.dataset.source !== selected;
        field.querySelectorAll('input,select,textarea').forEach(input => { input.disabled = field.hidden; });
    });
}
sourceChoices.forEach(choice => choice.addEventListener('change', syncSource));
syncSource();
document.getElementById('service_id').addEventListener('change', event => {
    const option = event.target.selectedOptions[0];
    if (!option?.value) return;
    document.getElementById('new_price').value = option.dataset.price || '';
    document.getElementById('new_currency').value = option.dataset.currency || 'PKR';
    document.getElementById('new_tags').value = option.dataset.tags || '';
    const icon = document.querySelector('.so-create input[name="icon_key"][value="' + option.dataset.icon + '"]');
    if (icon) icon.checked = true;
});
</script>
@endsection
