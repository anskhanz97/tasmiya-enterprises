<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

class LeadDetail extends Model
{
    public const STATUSES = [
        'New' => ['label' => 'New', 'class' => 'bg-primary'],
        'Contacted' => ['label' => 'Contacted', 'class' => 'bg-info'],
        'No Response' => ['label' => 'No Response', 'class' => 'bg-warning'],
        'Interested' => ['label' => 'Interested', 'class' => 'bg-success'],
        'Converted' => ['label' => 'Converted', 'class' => 'bg-purple'],
        'Lost' => ['label' => 'Lost', 'class' => 'bg-danger'],
    ];

    public const ACTIVITY_TYPES = ['Lead Created', 'Call', 'Meeting', 'WhatsApp', 'Email', 'Note', 'Conversion'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['follow_up_date' => 'date:Y-m-d', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOpen($query)
    {
        return $query->whereNotNull('follow_up_date')->whereNull('completed_at')->whereNull('cancelled_at');
    }

    public static function statusClass(string $status): string
    {
        return self::STATUSES[$status]['class'] ?? 'bg-secondary';
    }

    public function isOverdue(): bool
    {
        if (! $this->follow_up_date || $this->completed_at || $this->cancelled_at) {
            return false;
        }
        $day = $this->follow_up_date->format('Y-m-d');
        $now = CarbonImmutable::now(config('leads.timezone'));

        return $this->follow_up_time
            ? CarbonImmutable::parse($day.' '.$this->follow_up_time, config('leads.timezone'))->lt($now)
            : $day < $now->toDateString();
    }
}
