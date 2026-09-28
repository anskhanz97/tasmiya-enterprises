<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Service Model
 *
 * Represents a service offered by Tasmiya Enterprises.
 * Services belong to divisions and can be provided by multiple profiles.
 *
 * @property int $id
 * @property string $name Service name (e.g., "Tax Consultation")
 * @property string $slug URL-friendly identifier (auto-generated from name)
 * @property string $description Short description (max 500 chars)
 * @property string|null $long_description Full description with details
 * @property string|null $icon_url Icon image URL
 * @property string|null $image_url Service image URL
 * @property float $base_price Starting price for the service
 * @property string $currency Currency code (default: PKR)
 * @property int $division_id FK to divisions table
 * @property bool $is_active Whether service is visible on website
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'long_description',
        'icon_url',
        'image_url',
        'base_price',
        'currency',
        'division_id',
        'is_active',
        'created_by_profile_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     *
     * Automatically generates slug from name on create/update.
     */
    protected static function booting(): void
    {
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });

        static::updating(function ($service) {
            if ($service->isDirty('name')) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    /**
     * Service belongs to a Division.
     *
     * Each service belongs to exactly one division (FBR Taxation, IT & Digital, Tech Support).
     * This establishes what area of expertise offers this service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Service can be provided by many Profiles.
     *
     * Many-to-many relationship through service_profile pivot table.
     * Allows tracking which experts provide which services.
     *
     * Example: "Tax Consultation" can be provided by Atif, and other tax experts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'service_profile');
    }

    public function offerings()
    {
        return $this->hasMany(ServiceOffering::class);
    }

    /**
     * Service has many Testimonials.
     *
     * Uses polymorphic relationship to handle testimonials for both
     * services and profiles from a single testimonials table.
     *
     * Example: "Tax Consultation" has 15 client testimonials.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function testimonials()
    {
        return $this->morphMany(Testimonial::class, 'testimonialable');
    }

    /**
     * Scope: Get only active (visible) services.
     *
     * Usage: Service::active()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get services for a specific division.
     *
     * Usage: Service::byDivision(1)->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $divisionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    /**
     * Scope: Eager load with testimonials and apply rating calculation.
     *
     * Usage: Service::withRatings()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRatings($query)
    {
        return $query->with([
            'testimonials' => function ($q) {
                $q->where('is_approved', true);
            }
        ]);
    }

    /**
     * Get the average rating from approved testimonials.
     *
     * Returns null if no testimonials. Returns rounded to 1 decimal place.
     *
     * Example Usage:
     *   $service = Service::find(1);
     *   echo $service->getAverageRating(); // 4.5
     *
     * @return float|null Average rating (1.0 - 5.0) or null if none
     */
    public function getAverageRating()
    {
        $approved = $this->testimonials()
            ->where('is_approved', true)
            ->count();

        if ($approved === 0) {
            return null;
        }

        return round(
            $this->testimonials()
                ->where('is_approved', true)
                ->avg('rating'),
            1
        );
    }

    /**
     * Get count of approved testimonials.
     *
     * Example Usage:
     *   echo $service->getTestimonialCount(); // "24 reviews"
     *
     * @return int Count of approved testimonials
     */
    public function getTestimonialCount()
    {
        if ($this->getAttribute('approved_testimonials_count') !== null) {
            return (int) $this->getAttribute('approved_testimonials_count');
        }

        return $this->testimonials()
            ->where('is_approved', true)
            ->count();
    }

    /**
     * Get count of profiles (experts) offering this service.
     *
     * Example Usage:
     *   echo $service->getSpecialistsCount(); // "3 experts"
     *
     * @return int Count of profiles
     */
    public function getSpecialistsCount()
    {
        if ($this->getAttribute('specialists_count') !== null) {
            return (int) $this->getAttribute('specialists_count');
        }

        return $this->profiles()->count();
    }

    /**
     * Get visible (approved) testimonials for display.
     *
     * Returns testimonials ordered by featured first, then newest.
     * This is commonly used in views.
     *
     * Example Usage:
     *   $testimonials = $service->getVisibleTestimonials();
     *   foreach ($testimonials as $testimonial) {
     *       echo $testimonial->content;
     *   }
     *
     * @param int $limit Maximum testimonials to return
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVisibleTestimonials($limit = 5)
    {
        return $this->testimonials()
            ->where('is_approved', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get rating breakdown by star count.
     *
     * Useful for displaying a rating distribution chart.
     * Returns count of testimonials for each star level (1-5).
     *
     * Example Usage:
     *   $breakdown = $service->getRatingBreakdown();
     *   // Returns:
     *   // [
     *   //   '5' => 15,
     *   //   '4' => 8,
     *   //   '3' => 1,
     *   //   '2' => 0,
     *   //   '1' => 0,
     *   // ]
     *
     * @return array Rating breakdown [5 => count, 4 => count, ...]
     */
    public function getRatingBreakdown()
    {
        $breakdown = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        $ratings = $this->testimonials()
            ->where('is_approved', true)
            ->pluck('rating')
            ->countBy();

        foreach ($ratings as $rating => $count) {
            if (isset($breakdown[$rating])) {
                $breakdown[$rating] = $count;
            }
        }

        return $breakdown;
    }

    /**
     * Get the URL to view this service.
     *
     * Example Usage:
     *   <a href="{{ $service->getUrl() }}">View</a>
     *
     * @return string URL route
     */
    public function getUrl()
    {
        return route('services.show', ['service' => $this->slug]);
    }

    /**
     * Get formatted price with currency symbol.
     *
     * Example Usage:
     *   echo $service->getFormattedPrice(); // "₨5,000"
     *
     * @return string Formatted price
     */
    public function getFormattedPrice()
    {
        $symbols = [
            'PKR' => '₨',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        return $symbol . number_format($this->base_price, 0);
    }

    /**
     * Check if service has testimonials.
     *
     * @return bool
     */
    public function hasTestimonials()
    {
        return $this->getTestimonialCount() > 0;
    }

    /**
     * Get theme colors from division.
     *
     * Convenience method to access division's theme for service display.
     *
     * @return array Division theme colors
     */
    public function getThemeColors()
    {
        return $this->division->getThemeColors();
    }
}
