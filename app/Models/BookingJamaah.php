<?php

namespace App\Models;

use App\Enums\RoomType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingJamaah extends Model
{
    use BelongsToTenant;

    protected $table = 'booking_jamaah';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'room_type' => RoomType::class,
            'price' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function jamaah(): BelongsTo
    {
        return $this->belongsTo(Jamaah::class);
    }

    public function rombongan(): BelongsTo
    {
        return $this->belongsTo(Rombongan::class);
    }
}
