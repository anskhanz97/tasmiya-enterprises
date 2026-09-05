@extends('layouts.app')

@section('title', 'Create Service | Tasmiya Enterprises')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h2 class="mb-4" style="color: #333; font-weight: 700;">Create New Service</h2>

                    <x-form-errors />

                    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Service Name --}}
                        <div class="mb-4">
                            <label for="name" class="form-label fw-600" style="color: #333;">Service Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g., Tax Consultation">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Division --}}
                        <div class="mb-4">
                            <label for="division_id" class="form-label fw-600" style="color: #333;">Division</label>
                            <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id" required>
                                <option value="">-- Select Division --</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" @selected(old('division_id') == $division->id)>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Short Description --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-600" style="color: #333;">
                                Short Description
                                <small class="text-muted">(max 500 characters)</small>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required placeholder="Brief description of the service">{{ old('description') }}</textarea>
                            <small class="form-text text-muted" id="descriptionCounter">0 / 500</small>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Long Description --}}
                        <div class="mb-4">
                            <label for="long_description" class="form-label fw-600" style="color: #333;">
                                Detailed Description
                                <small class="text-muted">(optional)</small>
                            </label>
                            <textarea class="form-control @error('long_description') is-invalid @enderror" id="long_description" name="long_description" rows="5" placeholder="Comprehensive details about the service, features, and benefits">{{ old('long_description') }}</textarea>
                            @error('long_description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Pricing --}}
                        <div class="mb-4">
                            <label class="form-label fw-600" style="color: #333;">Pricing</label>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="base_price" class="form-label">Base Price</label>
                                    <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" id="base_price" name="base_price" value="{{ old('base_price') }}" required placeholder="0.00">
                                    @error('base_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency">
                                        <option value="PKR" @selected(old('currency') === 'PKR' || old('currency') === null)>PKR</option>
                                        <option value="USD" @selected(old('currency') === 'USD')>USD</option>
                                        <option value="EUR" @selected(old('currency') === 'EUR')>EUR</option>
                                        <option value="GBP" @selected(old('currency') === 'GBP')>GBP</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Images & Icons --}}
                        <div class="mb-4">
                            <label class="form-label fw-600" style="color: #333;">Media</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="icon_url" class="form-label">Icon URL</label>
                                    <input type="url" class="form-control @error('icon_url') is-invalid @enderror" id="icon_url" name="icon_url" value="{{ old('icon_url') }}" placeholder="https://example.com/icon.png">
                                    @error('icon_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="image_url" class="form-label">Cover Image URL</label>
                                    <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
                                    @error('image_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Profiles (Specialists) --}}
                        <div class="mb-4">
                            <label for="profiles" class="form-label fw-600" style="color: #333;">Assign Specialists</label>
                            <div class="alert alert-light border" style="max-height: 300px; overflow-y: auto;">
                                @foreach($profiles as $profile)
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="profile_{{ $profile->id }}" name="profiles[]" value="{{ $profile->id }}" @checked(in_array($profile->id, old('profiles') ?? []))>
                                        <label class="form-check-label" for="profile_{{ $profile->id }}">
                                            <strong>{{ $profile->user->name }}</strong>
                                            <small class="text-muted d-block">{{ $profile->title ?? 'Specialist' }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('profiles')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Actions --}}
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Services
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Create Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Description character counter
    const descriptionInput = document.getElementById('description');
    const descriptionCounter = document.getElementById('descriptionCounter');

    function updateCounter() {
        descriptionCounter.textContent = descriptionInput.value.length + ' / 500';
    }

    descriptionInput.addEventListener('input', updateCounter);
    updateCounter(); // Initialize
</script>
@endpush
@endsection
