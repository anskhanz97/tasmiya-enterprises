<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOffering extends Model
{
    protected $table = 'service_profile';

    public $timestamps = false;

    protected $fillable = [
        'service_id', 'profile_id', 'card_title', 'card_description',
        'price', 'currency', 'icon_key', 'tags',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tags' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function title(): string
    {
        return $this->card_title ?: $this->service->name;
    }

    public function description(): string
    {
        return $this->card_description ?: $this->service->description;
    }

    public function price(): string
    {
        return $this->price ?? $this->service->base_price;
    }

    public function currency(): string
    {
        return $this->currency ?: $this->service->currency;
    }

    public function iconKey(): string
    {
        return $this->icon_key ?: (config("service_cards.services.{$this->service->slug}.icon")
            ?: config("service_cards.divisions.{$this->service->division->slug}.icon", 'audit'));
    }

    public function tags(): array
    {
        return $this->tags ?? config("service_cards.services.{$this->service->slug}.focus", []);
    }
}
