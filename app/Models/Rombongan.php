<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombongan extends Model
{
    use BelongsToTenant;

    protected $table = 'rombongan';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(BookingJamaah::class);
    }
}
