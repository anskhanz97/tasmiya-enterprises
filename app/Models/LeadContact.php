<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadContact extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['converted_at' => 'datetime'];
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
