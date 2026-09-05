@extends('layouts.app')

@section('title', 'Share Your Feedback | Tasmiya Enterprises')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h2 class="mb-2" style="color: #333; font-weight: 700;">Share Your Feedback</h2>
                    <p class="text-muted mb-4">Help other clients by sharing your experience with our services</p>

                    <x-form-errors />

                    <form action="{{ route('testimonials.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Service Selection --}}
                        @if(!isset($service))
                            <div class="mb-4">
                                <label for="service_id" class="form-label fw-600" style="color: #333;">Service (Optional)</label>
                                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id">
                                    <option value="">-- Select a Service --</option>
                                    @foreach($services as $svc)
                                        <option value="{{ $svc->id }}" @selected(old('service_id') == $svc->id)>
                                            {{ $svc->name }} ({{ $svc->division->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Or choose a person below</small>
                                @error('service_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <div class="alert alert-light border mb-4">
                                <small class="text-muted">Reviewing:</small>
                                <p class="mb-0" style="font-weight: 600; color: #333;">{{ $service->name }}</p>
                            </div>
                        @endif

                        {{-- Profile Selection --}}
                        <div class="mb-4">
                            <label for="profile_id" class="form-label fw-600" style="color: #333;">Team Member (Optional)</label>
                            <select class="form-select @error('profile_id') is-invalid @enderror" id="profile_id" name="profile_id">
                                <option value="">-- Select a Team Member --</option>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>
                                        {{ $profile->user->name }} ({{ $profile->title ?? 'Specialist' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Or choose a service above</small>
                            @error('profile_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Rating --}}
                        <div class="mb-4">
                            <label class="form-label fw-600" style="color: #333;">How would you rate your experience?</label>
                            <div class="rating-selector mb-3">
                                <div class="d-flex gap-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="form-check">
                                            <input class="form-check-input rating-input" type="radio" id="rating_{{ $i }}" name="rating" value="{{ $i }}" @checked(old('rating') == $i) required>
                                            <label class="form-check-label" for="rating_{{ $i }}" style="cursor: pointer; font-size: 1.5rem; color: #ddd; transition: color 0.2s;">
                                                ★
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <small class="form-text text-muted" id="ratingLabel">Select a rating</small>
                            </div>
                            @error('rating')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Review Content --}}
                        <div class="mb-4">
                            <label for="content" class="form-label fw-600" style="color: #333;">
                                Your Review
                                <small class="text-muted">(20-500 characters)</small>
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required placeholder="Share your experience... What did you like? How can we improve?">{{ old('content') }}</textarea>
                            <small class="form-text text-muted" id="contentCounter">0 / 500</small>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Author Information --}}
                        <div class="mb-4">
                            <label class="form-label fw-600" style="color: #333;">Your Information</label>
                            
                            <div class="mb-3">
                                <label for="author_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('author_name') is-invalid @enderror" id="author_name" name="author_name" value="{{ old('author_name') }}" required placeholder="Your full name">
                                @error('author_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="author_position" class="form-label">Job Title</label>
                                <input type="text" class="form-control @error('author_position') is-invalid @enderror" id="author_position" name="author_position" value="{{ old('author_position') }}" placeholder="e.g., CEO, Manager, Developer">
                                @error('author_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="author_company" class="form-label">Company Name</label>
                                <input type="text" class="form-control @error('author_company') is-invalid @enderror" id="author_company" name="author_company" value="{{ old('author_company') }}" placeholder="Your company name">
                                @error('author_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="author_image_url" class="form-label">Profile Image URL</label>
                                <input type="url" class="form-control @error('author_image_url') is-invalid @enderror" id="author_image_url" name="author_image_url" value="{{ old('author_image_url') }}" placeholder="https://example.com/your-photo.jpg">
                                <small class="form-text text-muted">Optional: Add your profile photo</small>
                                @error('author_image_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Info Box --}}
                        <div class="alert alert-info mb-4">
                            <small>
                                <strong>Note:</strong> Your testimonial will be reviewed by our team before appearing on our website. 
                                We appreciate your honest feedback and will only display genuine, constructive reviews.
                            </small>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2 justify-content-between">
                            @if(isset($service))
                                <a href="{{ route('services.show', $service) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Service
                                </a>
                            @else
                                <a href="{{ route('services.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Services
                                </a>
                            @endif
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Success Message Example --}}
            @if(session('success'))
                <div class="alert alert-success mt-4 alert-dismissible fade show" role="alert">
                    <strong>Thank you!</strong> Your review has been submitted and is awaiting approval.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Rating selector with visual feedback
    const ratingInputs = document.querySelectorAll('.rating-input');
    const ratingLabels = document.querySelectorAll('.rating-input + label');
    const ratingLabel = document.getElementById('ratingLabel');

    const ratingLabels_text = {
        1: '😞 Poor',
        2: '😐 Fair',
        3: '😊 Good',
        4: '😄 Very Good',
        5: '😍 Excellent'
    };

    ratingInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            // Reset all colors
            ratingLabels.forEach(label => {
                label.style.color = '#ddd';
            });
            
            // Color selected and previous stars
            for (let i = 0; i <= index; i++) {
                ratingLabels[i].style.color = '#ffc107';
            }
            
            ratingLabel.textContent = ratingLabels_text[this.value];
        });

        // Hover effect
        input.addEventListener('mouseenter', function() {
            ratingLabels.forEach(label => {
                label.style.color = '#ddd';
            });
            for (let i = 0; i <= index; i++) {
                ratingLabels[i].style.color = '#ffc107';
            }
        });
    });

    // Reset on mouse leave
    document.querySelector('.rating-selector').addEventListener('mouseleave', function() {
        const checked = document.querySelector('.rating-input:checked');
        if (checked) {
            const checkedIndex = Array.from(ratingInputs).indexOf(checked);
            ratingLabels.forEach((label, idx) => {
                label.style.color = idx <= checkedIndex ? '#ffc107' : '#ddd';
            });
        } else {
            ratingLabels.forEach(label => {
                label.style.color = '#ddd';
            });
            ratingLabel.textContent = 'Select a rating';
        }
    });

    // Content character counter
    const contentInput = document.getElementById('content');
    const contentCounter = document.getElementById('contentCounter');

    function updateCounter() {
        contentCounter.textContent = contentInput.value.length + ' / 500';
    }

    contentInput.addEventListener('input', updateCounter);
    updateCounter();
</script>
@endpush

@push('styles')
<style>
    .rating-selector label {
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .rating-selector label:hover {
        color: #ffc107 !important;
    }
</style>
@endpush
@endsection
