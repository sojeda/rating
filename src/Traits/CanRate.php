<?php

namespace Laraveles\Rating\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laraveles\Rating\Contracts\Rater;
use Laraveles\Rating\Contracts\Rating;
use Laraveles\Rating\Events\ModelRated;
use Laraveles\Rating\Events\ModelUnrated;

trait CanRate
{
    /**
     * Relationship for models that this model currently rated.
     *
     * @param Model $model The model types of the results.
     * @return morphToMany The relationship.
     */
    public function ratings($model = null): MorphToMany
    {
        return $this->morphToMany(($model) ?: $this->getMorphClass(), 'rater', 'ratings', 'rater_id', 'rateable_id')
                    ->withPivot('rateable_type', 'rating', 'comment', 'cause', 'approved_at')
                    ->wherePivot('rateable_type', ($model) ?: $this->getMorphClass())
                    ->wherePivot('rater_type', $this->getMorphClass())
                    ->wherePivot('approved_at', '<>', null);
    }

    /**
     * Check if the current model is rating another model.
     *
     * @param Model $model The model which will be checked against.
     * @return bool
     */
    public function hasRated($model): bool
    {
        if (! $model instanceof Rater && ! $model instanceof Rating) {
            return false;
        }

        return (bool) ! is_null($this->ratings($model->getMorphClass())->find($model->getKey()));
    }

    /**
     * Rate a certain model.
     *
     * @param Model $model The model which will be rated.
     * @param float $rate The rate amount.
     * @return bool
     */
    public function rate($model, $rating, string $comment = null, string $cause = null): bool
    {
        if (! $model instanceof Rater && ! $model instanceof Rating) {
            return false;
        }

        if ($this->hasRated($model)) {
            return false;
        }

        $this->ratings()->attach($this->getKey(), [
            'rater_id' => $this->getKey(),
            'rateable_type' => $model->getMorphClass(),
            'rateable_id' => $model->getKey(),
            'rating' => (float) $rating,
            'comment' => $comment,
            'cause' => $cause,
            'approved_at' => config('rating.required_approval') ? null : Carbon::now(),
        ]);

        event(new ModelRated($model));

        return true;
    }

    /**
     * Rate a certain model.
     *
     * @param Model $model The model which will be rated.
     * @param $newRating
     * @return bool
     * @internal param float $rate The rate amount.
     */
    public function updateRatingFor($model, $newRating): bool
    {
        if (! $model instanceof Rater && ! $model instanceof Rating) {
            return false;
        }

        if (! $this->hasRated($model)) {
            return $this->rate($model, $newRating);
        }

        $this->unrate($model);

        return $this->rate($model, $newRating);
    }

    /**
     * Unrate a certain model.
     *
     * @param Model $model The model which will be unrated.
     * @return bool
     */
    public function unrate($model): bool
    {
        if (! $model instanceof Rater && ! $model instanceof Rating) {
            return false;
        }

        if (! $this->hasRated($model)) {
            return false;
        }

        $this->ratings($model->getMorphClass())->detach($model->getKey());

        event(new ModelUnrated($model));

        return true;
    }
}
