<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Workshop extends Model
{
    /** Call inside a transaction before reading capacity or active bookings. */
    public static function lockForCapacity(int $id): self
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite ignores FOR UPDATE. Acquire its writer lock before reading.
            DB::table('workshops')->where('id', $id)->update(['id' => DB::raw('id')]);
        }

        return static::whereKey($id)->lockForUpdate()->firstOrFail();
    }

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
