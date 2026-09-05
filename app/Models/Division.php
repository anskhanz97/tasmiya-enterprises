<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Division Model
 * 
 * Represents the three main business divisions of Tasmiya Enterprises:
 * 1. FBR Taxation Services (Atif Safdar)
 * 2. IT & Digital Services (Waseem Asghar, Ans Khan)
 * 3. Technical Support & Installation (Nazim Rauf)
 * 
 * Each division has:
 * - Unique branding (colors, icons, taglines)
 * - Team members (users) assigned to it
 * - Services offered
 * - Portfolio/projects
 * - Testimonials
 * 
 * Why this table exists:
 * - Allows easy scaling if new divisions are added
 * - Stores division-specific branding/colors
 * - Makes querying division-specific content easy
 * - Maintains organizational structure
 */
class Division extends Model
{
    use HasFactory;

    /**
     * Fillable columns - These can be mass-assigned
     * Example: Division::create(['name' => 'FBR Taxation', ...])
     */
    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'theme_color',        // Primary color in hex
        'primary_color',
        'secondary_color',
        'icon_path',
    ];

    /**
     * Casts - Automatically convert columns to specific types
     * 
     * Example: $division->created_at returns a Carbon instance,
     * allowing methods like ->diffForHumans()
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get all team members belonging to this division
     * 
     * Example:
     *   $division = Division::find(1);
     *   $members = $division->users; // Get all team members in this division
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all services offered by this division
     * (Will be created in Phase 4)
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get all testimonials for this division
     * (Will be created in Phase 4)
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Get all projects in this division's portfolio
     * (Will be created in Phase 5)
     *
     * Note: Project model not yet implemented
     * Uncomment this method when Project model is created
     *
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
    */

    // ========== SCOPES (Reusable Query Methods) ==========

    /**
     * Find division by slug
     * 
     * Usage: Division::whereSlug('fbr-taxation')->first()
     * Returns the FBR Taxation division
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Get only active divisions
     * (For future use when we add soft deletes or is_active column)
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ========== ACCESSORS (Computed Properties) ==========

    /**
     * Get CSS class for styling this division's pages
     * 
     * Example: $division->cssClass returns "division-fbr-taxation"
     * Used in Blade: <div class="{{ $division->cssClass }}">
     */
    public function getCssClassAttribute()
    {
        return 'division-' . $this->slug;
    }

    /**
     * Get full icon URL (if icon_path is just filename)
     * 
     * Example: $division->iconUrl might return "storage/icons/tax.svg"
     */
    public function getIconUrlAttribute()
    {
        if (!$this->icon_path) {
            return null;
        }

        // If already a full URL, return as-is
        if (str_starts_with($this->icon_path, 'http')) {
            return $this->icon_path;
        }

        // Otherwise prepend storage path
        return asset('storage/' . $this->icon_path);
    }

    // ========== UTILITY METHODS ==========

    /**
     * Get the leader(s) of this division
     * 
     * Returns: array of User objects who are division leaders
     * Note: Should be improved to add an 'is_leader' field in users table
     */
    public function leaders()
    {
        // For now, filter by division and role
        return $this->users()
            ->where('role', 'team_member')
            ->where('is_leader', true)
            ->get();
    }

    /**
     * Get member count
     * 
     * Example: $division->memberCount returns 2
     */
    public function getMemberCountAttribute()
    {
        return $this->users()->active()->count();
    }

    // ========== THEMING METHODS ==========

    /**
     * Get theme colors for this division
     * 
     * Returns an array with complete color scheme for CSS generation
     * 
     * Usage:
     * $theme = $division->getThemeColors();
     * return view('profiles.show', ['theme' => $theme]);
     * 
     * In view:
     * <style>
     *   :root {
     *     --primary-color: {{ $theme['primary'] }};
     *     --accent-color: {{ $theme['accent'] }};
     *     --text-color: {{ $theme['text'] }};
     *     --background-gradient: {{ $theme['gradient'] }};
     *   }
     * </style>
     * 
     * @return array - Color scheme with keys:
     *                primary: Main brand color
     *                secondary: Secondary color
     *                text: Text color on primary
     *                accent: Highlight/CTA color
     *                light: Lighter shade of primary
     *                dark: Darker shade of primary
     *                gradient: Linear gradient combining primary and secondary
     */
    public function getThemeColors(): array
    {
        // Predefined color schemes for each division
        // Format: primary_color → secondary_color
        $colorSchemes = [
            'fbr-taxation' => [
                'primary' => '#1e3a8a',          // Navy Blue
                'secondary' => '#3b82f6',       // Bright Blue
                'accent' => '#f59e0b',          // Gold/Amber
                'text' => '#ffffff',            // White
                'light' => '#dbeafe',           // Light Blue
                'dark' => '#0f172a',            // Dark Navy
                'gradient' => 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',
            ],
            'it-digital' => [
                'primary' => '#7c3aed',         // Purple
                'secondary' => '#a855f7',       // Lighter Purple
                'accent' => '#ec4899',          // Pink
                'text' => '#ffffff',            // White
                'light' => '#ede9fe',           // Light Purple
                'dark' => '#4c1d95',            // Dark Purple
                'gradient' => 'linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)',
            ],
            'tech-support' => [
                'primary' => '#f97316',         // Orange
                'secondary' => '#fb923c',       // Lighter Orange
                'accent' => '#ef4444',          // Red
                'text' => '#ffffff',            // White
                'light' => '#fed7aa',           // Light Orange
                'dark' => '#7c2d12',            // Dark Orange
                'gradient' => 'linear-gradient(135deg, #f97316 0%, #fb923c 100%)',
            ],
        ];

        // Get color scheme based on division slug
        $scheme = $colorSchemes[$this->slug] ?? $colorSchemes['fbr-taxation'];

        // Override with database values if they exist
        if ($this->primary_color) {
            $scheme['primary'] = $this->primary_color;
        }
        if ($this->secondary_color) {
            $scheme['secondary'] = $this->secondary_color;
        }
        if ($this->theme_color) {
            // Legacy: use theme_color as primary if set
            $scheme['primary'] = $this->theme_color;
        }

        return $scheme;
    }

    /**
     * Get CSS class name for this division's theme
     * 
     * Usage in views:
     * <div class="profile {{ $division->getThemeCssClass() }}">
     * 
     * Example output: "theme-fbr-taxation" or "theme-it-digital"
     * 
     * @return string - CSS class name
     */
    public function getThemeCssClass(): string
    {
        return 'theme-' . $this->slug;
    }

    /**
     * Get inline CSS style tag with theme variables
     * 
     * Usage in views:
     * {{ $division->getThemeStyles() }}
     * 
     * Output:
     * <style>
     *   .theme-fbr-taxation {
     *     --primary-color: #1e3a8a;
     *     --accent-color: #f59e0b;
     *     ...
     *   }
     * </style>
     * 
     * @return string - HTML style tag with theme variables
     */
    public function getThemeStyles(): string
    {
        $colors = $this->getThemeColors();
        $class = $this->getThemeCssClass();

        return <<<CSS
        <style>
            .{$class} {
                --primary-color: {$colors['primary']};
                --secondary-color: {$colors['secondary']};
                --accent-color: {$colors['accent']};
                --text-color: {$colors['text']};
                --light-color: {$colors['light']};
                --dark-color: {$colors['dark']};
                --gradient-bg: {$colors['gradient']};
            }
        </style>
        CSS;
    }

    /**
     * Get brand information for this division
     * 
     * Returns complete branding package for using elsewhere
     * 
     * Usage:
     * $brand = $division->getBrandInfo();
     * echo $brand['display_name']; // "FBR Taxation Services"
     * 
     * @return array - Brand information
     */
    public function getBrandInfo(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'icon' => $this->getIconUrlAttribute(),
            'colors' => $this->getThemeColors(),
            'css_class' => $this->getThemeCssClass(),
            'member_count' => $this->users()->active()->count(),
        ];
    }
}
