<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadDigestDelivery extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['digest_date' => 'date:Y-m-d', 'retry_at' => 'datetime', 'locked_until' => 'datetime', 'sent_at' => 'datetime', 'attempts' => 'integer'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
