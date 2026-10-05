@extends('layouts.app')
@section('title', 'Manage my reviews')
@section('content')
<style>
.pr{max-width:1000px;margin:0 auto;padding:48px 20px 72px;color:#211b3a}.pr h1{font-size:clamp(28px,4vw,44px);margin:12px 0}.pr-intro{color:#65748b;max-width:680px}.pr a{color:#6550a7}.pr-panel{background:white;border:1px solid #e2dcf2;border-radius:18px;padding:24px;margin-top:24px}.pr-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.pr label{display:block;font-weight:600;font-size:14px}.pr input,.pr select,.pr textarea{display:block;width:100%;box-sizing:border-box;margin-top:7px;padding:12px;border:1px solid #d8d0e8;border-radius:10px;background:#fcfbff;color:#211b3a;font:inherit}.pr textarea{min-height:125px;resize:vertical}.pr input:focus-visible,.pr select:focus-visible,.pr textarea:focus-visible,.pr button:focus-visible,.pr summary:focus-visible{outline:3px solid #9b89d2;outline-offset:3px}.pr-wide{grid-column:1/-1}.pr button{border:0;border-radius:10px;padding:12px 18px;font:inherit;font-weight:700;cursor:pointer}.pr-save{background:#6550a7;color:white}.pr-delete{background:#fff0f0;color:#a12c39;margin-top:16px}.pr summary{cursor:pointer;font-weight:700;line-height:1.7}.pr-state{font-size:13px;color:#6550a7;margin:0 0 18px}.pr-notice{padding:16px;background:#eee9fa;border-radius:12px;margin-top:20px}.pr-error{background:#fff0f0;color:#9d2532}.pr-pages{display:flex;justify-content:space-between;margin-top:24px}@media(max-width:600px){.pr-grid{grid-template-columns:1fr}.pr-panel{padding:18px}}
</style>
<style>.pr-actions{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-top:20px}.pr-actions .pr-delete{margin:0}.pr-review-link{display:inline-block;margin:14px 0 20px;font-size:13px;font-weight:700}.pr-panel form{margin:0}.pr-grid{margin-top:20px}</style>
<main class="pr">
    <a href="{{ route('profiles.show', $profile) }}#profile-reviews">Back to {{ $profile->user->name }}’s profile</a>
    <h1>Manage my reviews</h1>
    <p class="pr-intro">Manage genuine client feedback about {{ $profile->user->name }} and their services. These reviews appear in “What clients say”; they are separate from the website’s Google Reviews and Our Clients sections.</p>
    @if(session('success'))<div class="pr-notice" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="pr-notice pr-error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <section class="pr-panel">
        <h2>Add a client review</h2>
        <p class="pr-intro">Use feedback received from a real client, with permission to publish their name and words.</p>
        <form method="POST" action="{{ route('profiles.reviews.store', $profile) }}">
            @csrf
            @include('profiles.partials.review-form', ['review' => null])
        </form>
    </section>
    <h2 style="margin-top:36px">Your reviews</h2>
    @forelse($reviews as $review)
    <details class="pr-panel" @if($errors->any() && (int)old('editing_review') === $review->id) open @endif>
        <summary>{{ $review->author_name }} — {{ $review->rating }}/5 · {{ $review->is_approved ? 'Published' : 'Hidden / pending' }}</summary>
        @if($review->is_approved)<a class="pr-review-link" href="{{ route('profiles.show', $profile) }}#review-{{ $review->id }}">View published review ↗</a>@endif
        <form id="review-edit-{{ $review->id }}" method="POST" action="{{ route('profiles.reviews.update', [$profile, $review]) }}">
            @csrf @method('PUT')
            @include('profiles.partials.review-form')
        </form>
        <form id="review-delete-{{ $review->id }}" method="POST" action="{{ route('profiles.reviews.destroy', [$profile, $review]) }}" onsubmit="return confirm('Permanently delete this review? This cannot be undone.')">
            @csrf @method('DELETE')
        </form>
        <div class="pr-actions"><button class="pr-save" type="submit" form="review-edit-{{ $review->id }}">Save changes</button><button class="pr-delete" type="submit" form="review-delete-{{ $review->id }}">Delete review</button></div>
    </details>
    @empty
    <p class="pr-panel">No reviews yet. Add genuine client feedback above, or manage submitted reviews when they arrive.</p>
    @endforelse
    @if($reviews->hasPages())<nav class="pr-pages" aria-label="Review pages">@if($reviews->previousPageUrl())<a href="{{ $reviews->previousPageUrl() }}">Previous</a>@endif<span>Page {{ $reviews->currentPage() }} of {{ $reviews->lastPage() }}</span>@if($reviews->nextPageUrl())<a href="{{ $reviews->nextPageUrl() }}">Next</a>@endif</nav>@endif
</main>
@endsection
