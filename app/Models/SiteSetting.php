<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

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
            if (! $setting) return $default;
            return $setting->type === 'secret' && $setting->value
                ? Crypt::decryptString($setting->value)
                : $setting->value;
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
                'value' => $type === 'secret' && $value ? Crypt::encryptString($value) : $value,
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
        $saved = static::get('social_links');
        if ($saved !== null) {
            $links = json_decode($saved, true);
            return is_array($links) ? array_values(array_filter($links, fn ($link) => is_array($link) && isset($link['label'], $link['url']))) : [];
        }

        // Legacy settings remain visible until an admin saves the new ordered list.
        $links = [];
        foreach (['facebook' => 'Facebook', 'twitter' => 'X', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram'] as $key => $label) {
            if ($url = static::get('social_'.$key)) $links[] = ['label' => $label, 'url' => $url];
        }
        return $links;
    }
}
