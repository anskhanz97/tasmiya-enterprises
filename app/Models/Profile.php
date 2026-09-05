<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Profile Model
 * 
 * Represents user profile information including bio, specializations, contact details,
 * and portfolio information. Separate from User model to maintain separation of concerns.
 * 
 * User Model (Phase 2): WHO can access? (Authentication & Authorization)
 * Profile Model (Phase 3): WHAT do they show? (Display & Personalization)
 * 
 * @property int $id
 * @property int $user_id - Foreign key to users table
 * @property string|null $bio - User biography/about section
 * @property array|null $specializations - ["Tax Planning", "Compliance"] - JSON array
 * @property int|null $experience_years - Years of professional experience
 * @property string|null $profile_image_url - Avatar image URL (Phase 5: Google Drive)
 * @property string|null $banner_image_url - Cover/banner image URL (Phase 5: Google Drive)
 * @property array|null $social_links - {"whatsapp": "03001234567", "linkedin": "..."} - JSON
 * @property array|null $qualifications - ["CPA", "BS Accounting"] - JSON array
 * @property array|null $languages - ["Urdu", "English"] - JSON array
 * @property float|null $consultation_fee - Hourly consultation rate
 * @property bool $is_visible - Can be shown in team showcase
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @relationship user() - Owner of this profile (BelongsTo User)
 */
class Profile extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'profiles';

    /**
     * Mass assignable attributes
     * 
     * Only these fields can be bulk-assigned via create() or update()
     * This is a security feature to prevent unauthorized fields from being modified
     */
    protected $fillable = [
        'bio',
        'specializations',
        'experience_years',
        'profile_image_url',
        'banner_image_url',
        'social_links',
        'qualifications',
        'languages',
        'consultation_fee',
        'is_visible',
    ];

    /**
     * Attribute casting configuration
     * 
     * Automatically cast attributes when accessing or saving:
     * - 'array' type: JSON string ↔ PHP array
     * - 'integer' type: String → Integer
     * - 'boolean' type: 0/1 → false/true
     * 
     * Why this matters:
     * Without casting, $profile->specializations would be JSON string '[...]'
     * With casting, it's automatically a PHP array and we can use it directly
     * 
     * Example:
     * $profile->specializations[] = 'New Skill';
     * $profile->save(); // Automatically converted back to JSON
     */
    protected $casts = [
        'specializations' => 'array',
        'social_links' => 'array',
        'qualifications' => 'array',
        'languages' => 'array',
        'consultation_fee' => 'float',
        'is_visible' => 'boolean',
        'experience_years' => 'integer',
    ];

    /**
     * Relationship: User
     * 
     * Each profile belongs to one user.
     * This is an inverse relationship of User::hasOne('profile')
     * 
     * Returns: User model instance
     * 
     * Usage:
     * $profile = Profile::find(1);
     * echo $profile->user->name; // Get the user's name
     * 
     * Why BelongsTo?
     * - Profile table has user_id foreign key
     * - We navigate FROM profile TO user (the owner)
     * - One direction relationship
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the division this profile's user belongs to
     * 
     * This creates a "through" relationship:
     * Profile → User → Division
     * 
     * Since Laravel doesn't natively support through relationships,
     * we create a relationship based on the user's division_id.
     * 
     * Usage:
     * $profile = Profile::find(1);
     * $division = $profile->division; // Gets user's division
     * 
     * Eager loading:
     * $profiles = Profile::with('division')->get();
     * This will automatically load the division through the user's division_id
     * 
     * @return HasOneThrough
     */
    public function division(): HasOneThrough
    {
        // Get the user's division_id and use it to access Division directly
        // This creates the relationship: Profile.user.division_id → Division.id
        return $this->hasOneThrough(
            Division::class,
            User::class,
            'id',           // user table foreign key (profiles.user_id)
            'id',           // division table foreign key (users.division_id)
            'user_id',      // local key on profiles table
            'division_id'   // foreign key on users table
        );
    }

    /**
     * Get services this profile can provide (Many-to-Many)
     * 
     * A profile can provide multiple services.
     * A service can be provided by multiple profiles.
     * 
     * This creates a many-to-many relationship through the service_profile pivot table.
     * 
     * Usage:
     * $profile = Profile::find(1);
     * $services = $profile->services; // Get all services
     * 
     * // Check if profile offers a service
     * if ($profile->services()->where('service_id', 1)->exists()) {
     *     echo "This person offers Tax Consultation";
     * }
     * 
     * // Add a service
     * $profile->services()->attach($serviceId);
     * 
     * // Remove a service
     * $profile->services()->detach($serviceId);
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_profile');
    }

    /**
     * Get testimonials for this profile (Polymorphic)
     * 
     * Uses polymorphic relationship to get testimonials specifically for this profile.
     * 
     * Usage:
     * $profile = Profile::find(1);
     * $testimonials = $profile->testimonials()->approved()->get();
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function testimonials()
    {
        return $this->morphMany(Testimonial::class, 'testimonialable');
    }

    /**
     * Query Scope: Get only visible profiles
     * 
     * Scopes are reusable query filters that can be chained
     * 
     * Usage:
     * $visibleProfiles = Profile::visible()->get();
     * $visibleProfiles = Profile::where(...)->visible()->get(); // Chainable
     * 
     * Why scopes?
     * - DRY principle: Don't repeat where('is_visible', true)
     * - Readability: Code reads like English
     * - Maintainability: Change visibility logic in one place
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Query Scope: Get profiles by division
     * 
     * Filter profiles belonging to a specific division
     * 
     * Usage:
     * $taxProfiles = Profile::byDivision(1)->get(); // Division ID
     * $taxProfiles = Profile::byDivision($division->id)->visible()->get(); // Chainable
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $divisionId - Division ID to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDivision($query, $divisionId)
    {
        return $query->whereHas('user', function ($userQuery) use ($divisionId) {
            $userQuery->where('division_id', $divisionId);
        });
    }

    /**
     * Get specializations as formatted string for display
     * 
     * Converts array ['Tax Planning', 'Compliance'] into "Tax Planning, Compliance"
     * 
     * Usage in views:
     * <p>{{ $profile->getSpecializationsDisplay() }}</p>
     * 
     * Why accessor?
     * - Data transformation for display
     * - Don't store formatted data in database (only raw data)
     * - Same data can be used in different formats (JSON API, HTML, etc.)
     * 
     * @return string - Comma-separated specializations or empty string
     */
    public function getSpecializationsDisplay(): string
    {
        if (! is_array($this->specializations) || empty($this->specializations)) {
            return 'Not specified';
        }

        return implode(', ', $this->specializations);
    }

    /**
     * Get languages as formatted string for display
     * 
     * Usage:
     * <p>Speaks: {{ $profile->getLanguagesDisplay() }}</p>
     * 
     * @return string - Comma-separated languages or "Not specified"
     */
    public function getLanguagesDisplay(): string
    {
        if (! is_array($this->languages) || empty($this->languages)) {
            return 'Not specified';
        }

        return implode(', ', $this->languages);
    }

    /**
     * Get WhatsApp contact information
     * 
     * Extracts WhatsApp number from social_links JSON
     * 
     * Usage:
     * $whatsapp = $profile->getWhatsAppNumber();
     * if ($whatsapp) {
     *     $url = "https://wa.me/{$whatsapp}";
     * }
     * 
     * @return string|null - WhatsApp number or null if not set
     */
    public function getWhatsAppNumber(): ?string
    {
        return $this->social_links['whatsapp'] ?? null;
    }

    /**
     * Get WhatsApp message URL for CTA button
     * 
     * Generates clickable WhatsApp link with pre-filled message
     * 
     * Usage in views:
     * <a href="{{ $profile->getWhatsAppUrl() }}">Contact on WhatsApp</a>
     * 
     * Example output:
     * https://wa.me/923001234567?text=Hi%20Atif,%20I%20need%20tax%20advice
     * 
     * @param string $message - Pre-filled message text
     * @return string|null - Full WhatsApp URL or null if no number
     */
    public function getWhatsAppUrl($message = ''): ?string
    {
        $whatsapp = $this->getWhatsAppNumber();
        if (! $whatsapp) {
            return null;
        }

        // Remove any non-numeric characters from phone number
        $whatsapp = preg_replace('/\D/', '', $whatsapp);

        // Ensure international format (starts with country code)
        if (strlen($whatsapp) === 10) {
            $whatsapp = '92' . substr($whatsapp, 1); // Pakistan: 0301 → 923001
        } elseif (! str_starts_with($whatsapp, '92')) {
            $whatsapp = '92' . $whatsapp;
        }

        $url = "https://wa.me/{$whatsapp}";
        if ($message) {
            $url .= '?text=' . urlencode($message);
        }

        return $url;
    }

    /**
     * Check if profile has an image
     * 
     * Usage:
     * @if($profile->hasImage())
     *     <img src="{{ $profile->profile_image_url }}" />
     * @else
     *     <div class="placeholder-avatar">{{ $profile->user->name[0] }}</div>
     * @endif
     * 
     * @return bool
     */
    public function hasImage(): bool
    {
        return ! empty($this->profile_image_url) && $this->profile_image_url !== 'placeholder';
    }

    /**
     * Get profile image URL with fallback
     * 
     * Returns the profile image from organized directory or a placeholder avatar
     * Image stored at: public/images/profiles/{firstname}/image.png
     * 
     * Usage:
     * <img src="{{ $profile->getImageUrl() }}" />
     * 
     * @return string - Profile image URL or placeholder
     */
    public function getImageUrl(): string
    {
        // Try to get image from organized directory: public/images/profiles/{name}/image.png
        $firstName = strtolower(explode(' ', $this->user->name)[0]);
        $imagePath = "/images/profiles/{$firstName}/image.png";
        $fullPath = public_path($imagePath);

        if (file_exists($fullPath)) {
            return asset($imagePath);
        }

        // Fallback to stored URL if available
        if ($this->hasImage()) {
            return $this->profile_image_url;
        }

        // Generate placeholder avatar using user initials
        $initials = substr($this->user->name, 0, 1);
        $color = substr(md5($this->user->id), 0, 6); // Consistent color per user

        return "https://via.placeholder.com/150/{$color}/FFFFFF?text={$initials}";
    }

    /**
     * Get banner/cover image URL for profile page
     * 
     * Returns the banner image from organized directory
     * Image stored at: public/images/profiles/{firstname}/banner.png
     * 
     * Usage:
     * <img src="{{ $profile->getBannerImageUrl() }}" class="cover-image" />
     * 
     * @return string|null - Banner image URL or null if not found
     */
    public function getBannerImageUrl(): ?string
    {
        // Try to get banner from organized directory: public/images/profiles/{name}/banner.png
        $firstName = strtolower(explode(' ', $this->user->name)[0]);
        $bannerPath = "/images/profiles/{$firstName}/banner.png";
        $fullPath = public_path($bannerPath);

        if (file_exists($fullPath)) {
            return asset($bannerPath);
        }

        // Fallback to stored banner URL if available
        if ($this->banner_image_url) {
            return $this->banner_image_url;
        }

        return null;
    }

    /**
     * Get the division's theme colors for this profile
     * 
     * Returns the color scheme for the division this profile belongs to
     * 
     * Usage:
     * $theme = $profile->getThemeColors();
     * echo $theme['primary']; // #1e3a8a
     * 
     * @return array - Theme colors with keys: primary, accent, text, gradient
     */
    public function getThemeColors(): array
    {
        return $this->user->division?->getThemeColors() ?? [
            'primary' => '#6b7280',
            'accent' => '#9ca3af',
            'text' => '#ffffff',
            'gradient' => 'linear-gradient(135deg, #6b7280 0%, #9ca3af 100%)',
        ];
    }
}
