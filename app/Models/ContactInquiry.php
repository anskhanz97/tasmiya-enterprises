<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInquiry extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'name',
        'subject',
        'message',
        'source',
        'source_type',
        'status',
        'assigned_to',
        'notes',
        'responded_at',
        'resolved_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who submitted this inquiry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user this inquiry is assigned to.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to get new inquiries.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope to get inquiries by source.
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope to get inquiries assigned to a user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Mark as contacted.
     */
    public function markAsContacted(): void
    {
        $this->update([
            'status' => 'contacted',
            'responded_at' => now(),
        ]);
    }

    /**
     * Mark as resolved.
     */
    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Get contact name (from inquiry or related user).
     */
    public function getContactName(): string
    {
        return $this->name ?? $this->user?->name ?? 'Unknown';
    }

    /**
     * Get contact email.
     */
    public function getContactEmail(): string
    {
        return $this->email ?? $this->user?->email ?? $this->phone ?? '';
    }

    /**
     * Get contact phone.
     */
    public function getContactPhone(): string
    {
        return $this->phone ?? $this->user?->phone ?? $this->user?->whatsapp_number ?? '';
    }
}
