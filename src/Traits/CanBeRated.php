<?php

namespace Laraveles\Rating\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanBeRated
{
    /**
     * Relationship for models that rated this model.
     *
     * @param Model $model The model types of the results.
     * @return morphToMany The relationship.
     */
    public function raters($model = null, bool $approved = true)
    {
        /** @var MorphToMany $morphToMany */
        $morphToMany = $this->morphToMany(
            $model ?: $this->getMorphClass(),
            'rateable',
            'ratings',
            'rateable_id',
            'rater_id'
        );

        if ($approved) {
            $morphToMany->wherePivot('approved_at', '<>', null);
        }

        return $morphToMany
                    ->withPivot('rater_type', 'rating', 'comment', 'cause', 'approved_at')
                    ->wherePivot('rater_type', ($model) ?: $this->getMorphClass())
                    ->wherePivot('rateable_type', $this->getMorphClass());
    }

    /**
     * Calculate the average rating of the current model.
     *
     * @param Model|null $model
     * @return float The average rating.
     */
    public function averageRating($model = null): float
    {
        if ($this->raters($model)->count() == 0) {
            return 0.00;
        }

        return (float) $this->raters($model)->avg('rating');
    }
}
