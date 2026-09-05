@extends('layouts.app')

@section('content')
<style>
    {{ $division->getThemeStyles() }}
    
    .edit-container {
        min-height: calc(100vh - 64px);
        background: #f9fafb;
        padding: 2rem 1rem;
    }
    
    .edit-form-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .form-header {
        background: var(--gradient-bg);
        color: var(--text-color);
        padding: 2rem;
        border-radius: 12px 12px 0 0;
        text-align: center;
    }
    
    .form-header h2 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }
    
    .form-body {
        background: white;
        padding: 2rem;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        font-size: 1rem;
        font-family: inherit;
        transition: border-color 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .form-group small {
        display: block;
        color: #6b7280;
        margin-top: 0.25rem;
        font-size: 0.875rem;
    }
    
    .form-group-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    @media (max-width: 600px) {
        .form-group-row {
            grid-template-columns: 1fr;
        }
    }
    
    .array-field-group {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .array-field-item {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .array-field-item input {
        margin: 0;
    }
    
    .btn-remove-item {
        background: #ef4444;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s ease;
    }
    
    .btn-remove-item:hover {
        background: #dc2626;
    }
    
    .btn-add-item {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s ease;
        margin-top: 0.5rem;
    }
    
    .btn-add-item:hover {
        opacity: 0.9;
    }
    
    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .form-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-submit {
        background: var(--primary-color);
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        opacity: 0.9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .btn-cancel {
        background: #e5e7eb;
        color: #1f2937;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #d1d5db;
    }
    
    .social-link-field {
        background: white;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 1rem;
    }
    
    .social-link-field label {
        font-weight: 500;
        color: #4b5563;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
    }
    
    .checkbox-group label {
        margin: 0;
        font-weight: 500;
    }
</style>

<div class="edit-container">
    <div class="edit-form-wrapper">
        <!-- Form Header -->
        <div class="form-header">
            <h2>Edit Profile - {{ $profile->user->name }}</h2>
            <p style="margin: 0.5rem 0 0; opacity: 0.9;">Update your professional information and specializations</p>
        </div>
        
        <!-- Form Body -->
        <form action="{{ route('profiles.update', $profile) }}" method="POST" class="form-body" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Profile/Banner Image Upload -->
            <div class="form-group">
                <label>📸 Profile Banner Image</label>
                <div style="background: #f0f9ff; padding: 1.5rem; border-radius: 12px; border: 2px dashed #3b82f6;">
                    <p style="color: #1e40af; font-weight: 600; margin-bottom: 1rem;">
                        This is the large image shown at the top of your profile page
                    </p>
                    
                    <div>
                        <label for="banner_image" style="display: block; margin-bottom: 0.5rem;">Upload New Banner Image</label>
                        <input type="file" name="banner_image" id="banner_image" accept="image/*" style="margin-bottom: 0.5rem;">
                        <small style="display: block; color: #6b7280; margin-bottom: 1rem;">Recommended: 1200x800px or larger (JPG, PNG, max 2MB)</small>
                        
                        @if($profile->banner_image_url)
                            <div style="margin-top: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                                <p style="font-size: 0.875rem; color: #059669; font-weight: 600; margin-bottom: 0.5rem;">✓ Current Banner Image:</p>
                                <img src="{{ $profile->banner_image_url }}" alt="Current banner" style="max-width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; border: 2px solid var(--primary-color);">
                            </div>
                        @else
                            <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">No banner image set yet</p>
                        @endif
                        
                        <div id="banner_preview" style="margin-top: 1rem;"></div>
                        @error('banner_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Also keep URL input for direct links -->
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #bfdbfe;">
                        <p style="font-size: 0.875rem; color: #1e40af; margin-bottom: 0.5rem;">💡 Or provide a direct image URL:</p>
                        <input type="url" name="banner_image_url" id="banner_image_url" value="{{ old('banner_image_url', $profile->banner_image_url) }}" placeholder="https://example.com/banner.jpg" style="width: 100%;">
                        @error('banner_image_url')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Bio Section -->
            <div class="form-group">
                <label for="bio">About You</label>
                <textarea name="bio" id="bio" placeholder="Write a professional biography about yourself...">{{ old('bio', $profile->bio) }}</textarea>
                <small>Maximum 500 characters. Tell visitors about your expertise and experience.</small>
                @error('bio')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Experience Years -->
            <div class="form-group">
                <label for="experience_years">💼 Years of Experience</label>
                <input type="number" name="experience_years" id="experience_years" min="0" max="80" value="{{ old('experience_years', $profile->experience_years) }}" placeholder="e.g., 15">
                <small>Total years of professional experience (displayed in stats)</small>
                @error('experience_years')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Specializations -->
            <div class="form-group">
                <label>🎯 Specializations/Skills</label>
                <div class="array-field-group" id="specializations-group">
                    @forelse(old('specializations', $profile->specializations ?? []) as $index => $spec)
                        <div class="array-field-item">
                            <input type="text" name="specializations[]" value="{{ $spec }}" placeholder="e.g., Tax Planning">
                            <button type="button" class="btn-remove-item" onclick="this.parentElement.remove();">Remove</button>
                        </div>
                    @empty
                        <div class="array-field-item">
                            <input type="text" name="specializations[]" placeholder="e.g., Tax Planning">
                            <button type="button" class="btn-remove-item" onclick="this.parentElement.remove();">Remove</button>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="btn-add-item" onclick="addArrayField('specializations-group', 'e.g., Compliance Audit')">+ Add Specialization</button>
                <small>List your key skills and areas of expertise (displayed on profile)</small>
                @error('specializations.*')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- WhatsApp Contact -->
            <div class="form-group">
                <label>📱 WhatsApp Number</label>
                <input type="tel" name="social_links[whatsapp]" value="{{ old('social_links.whatsapp', $profile->social_links['whatsapp'] ?? '') }}" placeholder="+92-312-4246916">
                <small>Your WhatsApp number for client contact (displayed as contact button on profile)</small>
                @error('social_links.whatsapp')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Visibility Toggle -->
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $profile->is_visible) ? 'checked' : '' }}>
                    <label for="is_visible">Show in team showcase</label>
                </div>
                <small>When unchecked, your profile will be hidden from the public team showcase</small>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">Save Changes</button>
                <a href="{{ route('profiles.show', $profile) }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Add a new field to an array field group
     * 
     * @param {string} groupId - ID of the container group
     * @param {string} placeholder - Placeholder text for input
     */
    function addArrayField(groupId, placeholder) {
        const group = document.getElementById(groupId);
        const newItem = document.createElement('div');
        newItem.className = 'array-field-item';
        newItem.innerHTML = `
            <input type="text" name="${groupId.replace('-group', '')}[]" placeholder="${placeholder}">
            <button type="button" class="btn-remove-item" onclick="this.parentElement.remove();">Remove</button>
        `;
        group.appendChild(newItem);
    }
    
    /**
     * Banner image preview functionality
     * Shows preview of uploaded banner image before form submission
     */
    document.addEventListener('DOMContentLoaded', function() {
        const bannerImageInput = document.getElementById('banner_image');
        const bannerPreview = document.getElementById('banner_preview');
        
        bannerImageInput?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    bannerPreview.innerHTML = `
                        <div style="text-align: center;">
                            <p style="font-size: 0.875rem; color: #059669; font-weight: 600; margin-bottom: 0.5rem;">✓ New profile photo preview:</p>
                            <img src="${e.target.result}" alt="Preview" style="max-width: 250px; max-height: 250px; object-fit: cover; border-radius: 12px; border: 3px solid #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

@endsection
