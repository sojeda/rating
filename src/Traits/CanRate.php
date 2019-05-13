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
     * @param bool $approved
     * @return MorphToMany The relationship.
     */
    public function ratings($model = null, bool $approved = true): MorphToMany
    {
        /** @var MorphToMany $morphToMany */
        $morphToMany = $this->morphToMany(
            $model ?: $this->getMorphClass(),
            'rater',
            'ratings',
            'rater_id',
            'rateable_id'
        );

        if ($approved) {
            $morphToMany->wherePivot('approved_at', '<>', null);
        }

        $morphToMany
            ->withPivot('rateable_type', 'rating', 'comment', 'cause', 'approved_at')
            ->wherePivot('rateable_type', $model ?: $this->getMorphClass())
            ->wherePivot('rater_type', $this->getMorphClass());

        return $morphToMany;
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

        return $this->ratings($model->getMorphClass(), false)->find($model->getKey()) !== null;
    }

    /**
     * Rate a certain model.
     *
     * @param Model $model The model which will be rated.
     * @param $rating
     * @param Model $user
     * @param string|null $comment
     * @param string|null $cause
     * @return bool
     * @internal param float $rate The rate amount.
     */
    public function rate($model, $rating, $user = null, string $comment = null, string $cause = null): bool
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
            'user_id' => $user ? $user->getKey() : null,
            'rateable_id' => $model->getKey(),
            'rating' => (float) $rating,
            'comment' => $comment,
            'cause' => $cause,
            'approved_at' => config('rating.required_approval', false) ? null : Carbon::now(),
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
