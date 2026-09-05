<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Testimonial Model
 *
 * Represents a client testimonial/review for a service or profile.
 * Uses polymorphic relationships to handle testimonials for multiple models.
 *
 * @property int $id
 * @property string $content Review text (20-500 characters)
 * @property int $rating Star rating (1-5)
 * @property string $author_name Name of the reviewer
 * @property string|null $author_position Job title/position
 * @property string|null $author_company Company name
 * @property string|null $author_image_url Image URL for reviewer's avatar
 * @property string $testimonialable_type Model class (Service::class or Profile::class)
 * @property int $testimonialable_id Service ID or Profile ID
 * @property bool $is_approved Admin approval status
 * @property bool $is_featured Whether to highlight this testimonial
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Testimonial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'rating',
        'author_name',
        'author_position',
        'author_company',
        'author_image_url',
        'testimonialable_type',
        'testimonialable_id',
        'is_approved',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Polymorphic relationship to either Service or Profile.
     *
     * A testimonial can belong to either a Service (review of a service)
     * or a Profile (review of an expert person), or both.
     *
     * Polymorphic relationships use the testimonialable_type and
     * testimonialable_id columns to track which model instance owns this testimonial.
     *
     * Example Usage:
     *   $testimonial = Testimonial::find(1);
     *
     *   if ($testimonial->testimonialable_type === Service::class) {
     *       echo "This is a review of: " . $testimonial->testimonialable->name;
     *   }
     *
     *   if ($testimonial->testimonialable_type === Profile::class) {
     *       echo "This is a review of: " . $testimonial->testimonialable->user->name;
     *   }
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function testimonialable()
    {
        return $this->morphTo();
    }

    /**
     * Get the service if this testimonial is for a service.
     *
     * Convenience method for service testimonials.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function service()
    {
        if ($this->testimonialable_type === Service::class) {
            return $this->testimonialable;
        }
        return null;
    }

    /**
     * Get the profile if this testimonial is for a profile.
     *
     * Convenience method for profile testimonials.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function profile()
    {
        if ($this->testimonialable_type === Profile::class) {
            return $this->testimonialable;
        }
        return null;
    }

    /**
     * Scope: Get only approved testimonials.
     *
     * Usage: Testimonial::approved()->get()
     *
     * Only approved testimonials are displayed on the website.
     * This prevents spam and inappropriate reviews from appearing publicly.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Get pending testimonials (not yet approved).
     *
     * Usage: Testimonial::pending()->get()
     *
     * Used in admin dashboard to show reviews awaiting approval.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope: Get featured testimonials.
     *
     * Usage: Testimonial::featured()->get()
     *
     * Featured testimonials are highlighted and often displayed first.
     * Admins can mark special or excellent reviews as featured.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Get high-quality testimonials (4+ stars).
     *
     * Usage: Testimonial::highRating()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Scope: Get testimonials for a specific service.
     *
     * Usage: Testimonial::forService($serviceId)->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $serviceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForService($query, $serviceId)
    {
        return $query
            ->where('testimonialable_type', Service::class)
            ->where('testimonialable_id', $serviceId);
    }

    /**
     * Scope: Get testimonials for a specific profile.
     *
     * Usage: Testimonial::forProfile($profileId)->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $profileId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForProfile($query, $profileId)
    {
        return $query
            ->where('testimonialable_type', Profile::class)
            ->where('testimonialable_id', $profileId);
    }

    /**
     * Check if testimonial is visible (approved).
     *
     * Usage: if ($testimonial->isVisible()) { ... }
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->is_approved === true;
    }

    /**
     * Check if testimonial is pending approval.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->is_approved === false;
    }

    /**
     * Get star rating as a visual string.
     *
     * Example Usage:
     *   echo $testimonial->getStars(); // "★★★★★" (5 stars)
     *
     * @return string Star representation
     */
    public function getStars()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get initials from author name for avatar fallback.
     *
     * Example Usage:
     *   "John Doe" → "JD"
     *   "Jane Smith" → "JS"
     *
     * @return string Two-letter initials
     */
    public function getInitials()
    {
        $names = explode(' ', trim($this->author_name));
        $initials = '';

        foreach (array_slice($names, 0, 2) as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }

        return $initials;
    }

    /**
     * Get avatar URL or generate placeholder with initials.
     *
     * Falls back to a UI Avatar service if no image provided.
     *
     * Example Usage:
     *   <img src="{{ $testimonial->getAvatarUrl() }}" alt="{{ $testimonial->author_name }}">
     *
     * @return string Avatar URL
     */
    public function getAvatarUrl()
    {
        if ($this->author_image_url) {
            return $this->author_image_url;
        }

        // Generate placeholder with initials
        $initials = $this->getInitials();
        return "https://ui-avatars.com/api/?name={$initials}&background=random&color=fff";
    }

    /**
     * Get author info as formatted string.
     *
     * Example Usage:
     *   echo $testimonial->getAuthorInfo(); // "John Doe, CEO at Acme Corp"
     *
     * @return string Formatted author information
     */
    public function getAuthorInfo()
    {
        $info = $this->author_name;

        if ($this->author_position) {
            $info .= ', ' . $this->author_position;
        }

        if ($this->author_company) {
            $info .= ' at ' . $this->author_company;
        }

        return $info;
    }

    /**
     * Get short version of content (first 100 chars with ellipsis).
     *
     * Useful for list views where full content is too long.
     *
     * Example Usage:
     *   echo $testimonial->getShortContent(); // "Great service! Highly recommend..."
     *
     * @return string Short content (max 100 chars)
     */
    public function getShortContent()
    {
        if (strlen($this->content) > 100) {
            return substr($this->content, 0, 100) . '...';
        }

        return $this->content;
    }

    /**
     * Get rating label as text.
     *
     * Example Usage:
     *   echo $testimonial->getRatingLabel(); // "Excellent" (for 5 stars)
     *
     * @return string Rating label
     */
    public function getRatingLabel()
    {
        return match ($this->rating) {
            5 => 'Excellent',
            4 => 'Very Good',
            3 => 'Good',
            2 => 'Fair',
            1 => 'Poor',
            default => 'Unknown',
        };
    }

    /**
     * Get CSS color class for rating display.
     *
     * Example Usage:
     *   <span class="{{ $testimonial->getRatingClass() }}">★★★★★</span>
     *
     * @return string CSS class name
     */
    public function getRatingClass()
    {
        return match ($this->rating) {
            5 => 'rating-excellent',
            4 => 'rating-very-good',
            3 => 'rating-good',
            2 => 'rating-fair',
            1 => 'rating-poor',
            default => 'rating-unknown',
        };
    }

    /**
     * Approve this testimonial (make it visible).
     *
     * Usage: $testimonial->approve();
     *
     * @return bool Success status
     */
    public function approve()
    {
        return $this->update(['is_approved' => true]);
    }

    /**
     * Reject this testimonial (hide it permanently).
     *
     * Usage: $testimonial->reject();
     *
     * Note: This doesn't delete it, just marks as unapproved.
     *
     * @return bool Success status
     */
    public function reject()
    {
        return $this->update(['is_approved' => false]);
    }

    /**
     * Mark this testimonial as featured.
     *
     * Usage: $testimonial->markFeatured();
     *
     * @return bool Success status
     */
    public function markFeatured()
    {
        return $this->update(['is_featured' => true]);
    }

    /**
     * Unmark this testimonial as featured.
     *
     * Usage: $testimonial->unmarkFeatured();
     *
     * @return bool Success status
     */
    public function unmarkFeatured()
    {
        return $this->update(['is_featured' => false]);
    }
}
