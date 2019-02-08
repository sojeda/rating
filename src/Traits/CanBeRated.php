<?php

namespace Laraveles\Rating\Traits;

trait CanBeRated
{
    /**
     * Relationship for models that rated this model.
     *
     * @param Model $model The model types of the results.
     * @return morphToMany The relationship.
     */
    public function raters($model = null)
    {
        return $this->morphToMany(($model) ?: $this->getMorphClass(), 'rateable', 'ratings', 'rateable_id', 'rater_id')
                    ->withPivot('rater_type', 'rating', 'comment', 'cause', 'approved_at')
                    ->wherePivot('rater_type', ($model) ?: $this->getMorphClass())
                    ->wherePivot('rateable_type', $this->getMorphClass())
                    ->wherePivot('approved_at', '<>', null);
    }

    /**
     * Calculate the average rating of the current model.
     *
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
