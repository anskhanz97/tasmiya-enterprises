<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, string $type = 'text', ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
        
        Cache::forget("setting_{$key}");
    }

    /**
     * Get all social media links
     */
    public static function getSocialLinks(): array
    {
        return [
            'facebook' => static::get('social_facebook'),
            'twitter' => static::get('social_twitter'),
            'linkedin' => static::get('social_linkedin'),
            'instagram' => static::get('social_instagram'),
        ];
    }
}
