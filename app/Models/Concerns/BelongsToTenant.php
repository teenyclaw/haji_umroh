<?php

namespace App\Models\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (tenancy()->check()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', tenancy()->id());
            }
        });

        static::creating(function ($model) {
            if (empty($model->tenant_id) && tenancy()->check()) {
                $model->tenant_id = tenancy()->id();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->withoutGlobalScope('tenant')->where(
            $this->getTable() . '.tenant_id',
            $tenantId
        );
    }
}
