<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\JamaahStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jamaah extends Model
{
    use BelongsToTenant;

    protected $table = 'jamaah';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'passport_issued_at' => 'date',
            'passport_expired_at' => 'date',
            'gender' => Gender::class,
            'status' => JamaahStatus::class,
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function bookingJamaah(): HasMany
    {
        return $this->hasMany(BookingJamaah::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ManasikAttendance::class);
    }

    public function isFemale(): bool
    {
        return $this->gender === Gender::P;
    }

    public function passportExpiringSoon(): bool
    {
        return $this->passport_expired_at
            && $this->passport_expired_at->isBefore(now()->addMonths(6));
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }
}
