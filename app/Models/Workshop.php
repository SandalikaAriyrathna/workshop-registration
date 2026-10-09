<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'code',
        'title',
        'instructor',
        'starts_at',
        'capacity',
        'status',
        'location',
        'description',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'capacity' => 'integer',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function activeRegistrations()
    {
        return $this->hasMany(Registration::class)
            ->where('status', 'active');
    }
}
