<?php

namespace App\Models;

use App\Enums\PackageType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => PackageType::class,
            'departure_date' => 'date',
            'return_date' => 'date',
            'base_price' => 'decimal:2',
            'handling_fee' => 'decimal:2',
            'insurance_fee' => 'decimal:2',
            'visa_fee' => 'decimal:2',
            'is_published' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(PackagePrice::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function rombongan(): HasMany
    {
        return $this->hasMany(Rombongan::class);
    }

    public function priceFor(string $roomType): float
    {
        $extra = (float) $this->handling_fee + (float) $this->insurance_fee + (float) $this->visa_fee;
        $room = $this->prices->firstWhere('room_type', $roomType);

        $roomPrice = $room ? (float) $room->price : (float) $this->base_price;

        return $roomPrice + $extra;
    }

    public function lowestPrice(): float
    {
        if ($this->prices->isNotEmpty()) {
            return (float) $this->prices->min('price');
        }

        return (float) $this->base_price;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
