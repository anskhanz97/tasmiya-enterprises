<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LeadNotification extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, User $user): void
    {
        $query->where('user_id', $user->id)->where(fn ($q) => $q->whereNull('lead_id')
            ->orWhereHas('lead', fn ($lead) => $lead->visibleTo($user)));
    }

    public function targetUrl(): string
    {
        return $this->lead_id ? route('leads.index', ['lead' => $this->lead_id])
            : route('notifications.index', ['view' => 'followups']);
    }
}
