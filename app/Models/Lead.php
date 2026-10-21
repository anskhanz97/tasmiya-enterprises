<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = ['id'];

    protected $attributes = ['version' => 1];

    protected function casts(): array
    {
        return ['version' => 'integer', 'assigned_to' => 'integer', 'division_id' => 'integer'];
    }

    public function contact()
    {
        return $this->belongsTo(LeadContact::class, 'lead_contact_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function leadDetails()
    {
        return $this->hasMany(LeadDetail::class);
    }

    public function inquiries()
    {
        return $this->hasMany(ContactInquiry::class);
    }

    public function openLeadFollowup()
    {
        return $this->hasOne(LeadDetail::class)->open()->latestOfMany();
    }

    public function scopeWithInquiryContext(Builder $query): void
    {
        if ($query->getQuery()->columns === null) {
            $query->select('leads.*');
        }
        $previous = \Illuminate\Support\Facades\DB::table('leads as previous')
            ->whereColumn('previous.lead_contact_id', 'leads.lead_contact_id')
            ->whereColumn('previous.division_id', 'leads.division_id')
            ->whereColumn('previous.assigned_to', 'leads.assigned_to')
            ->whereNotNull('leads.assigned_to')->whereColumn('previous.id', '<', 'leads.id');
        $customers = (clone $previous)->whereExists(fn ($q) => $q->selectRaw('1')->from('lead_details as conversions')
            ->whereColumn('conversions.lead_id', 'previous.id')->where('conversions.activity_type', 'Conversion'));
        $query->addSelect(['previous_inquiry_count' => (clone $previous)->selectRaw('COUNT(*)'),
            'previous_customer_exists' => $customers->selectRaw('1')->limit(1)]);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        if (! $user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }
    }

    public function isClosed(): bool
    {
        return in_array($this->lead_status, ['Converted', 'Lost'], true);
    }

    public function scopeOverdue(Builder $query): void
    {
        $now = \Carbon\CarbonImmutable::now(config('leads.timezone'));
        $query->whereHas('openLeadFollowup', fn ($q) => $q->where(fn ($q) => $q
            ->where('follow_up_date', '<', $now->toDateString())
            ->orWhere(fn ($q) => $q->where('follow_up_date', $now->toDateString())
                ->whereNotNull('follow_up_time')->where('follow_up_time', '<', $now->format('H:i:s')))));
    }

    public function scopeNeedsAssignment(Builder $query): void
    {
        $query->whereNotIn('lead_status', ['Converted', 'Lost'])->where(fn ($q) => $q->whereNull('assigned_to')
            ->orWhereDoesntHave('assignee', fn ($u) => $u->where('is_active', true)));
    }

    public static function legacyStatus(string $status): string
    {
        return match ($status) {
            'Contacted', 'No Response' => 'contacted',
            'Interested' => 'in_progress',
            'Converted' => 'resolved',
            'Lost' => 'closed',
            default => 'new',
        };
    }
}
