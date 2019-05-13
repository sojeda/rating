<?php

namespace Laraveles\Rating\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasRate
{
    /**
     * @param bool $onlyApprove
     * @return HasMany
     */
    public function myRatings(bool $onlyApprove = true)
    {
        /** @var HasMany $hasMany */
        $hasMany = $this->hasMany(config('rating.models.rating'), 'user_id');

        if ($onlyApprove) {
            $hasMany->where('approved_at', '<>', null);
        }

        return $hasMany;
    }
    /**
     * Calculate the average rating of the current model.
     *
     * @param Model|null $model
     * @return float The average rating.
     */
    public function averageMyRating($model = null, bool $onlyApprove = true): float
    {
        if ($this->myRatings()->count() == 0) {
            return 0.00;
        }

        return (float) $this->myRatings($onlyApprove)->avg('rating');
    }

}