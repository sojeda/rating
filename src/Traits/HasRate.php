<?php

namespace Laraveles\Rating\Traits;


use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasRate
{
    /**
     * @param bool $onlyApprove
     * @return HasMany
     */
    public function ratings(bool $onlyApprove)
    {
        /** @var HasMany $hasMany */
        $hasMany = $this->hasMany(config('rating.models.rating'), 'user_id');

        if ($onlyApprove) {
            $hasMany->where('approved_at', '<>', null);
        }

        return $hasMany;
    }
}