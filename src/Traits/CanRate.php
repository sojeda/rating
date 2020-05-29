<?php

namespace Laraveles\Rating\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laraveles\Rating\Contracts\Rateable;
use Laraveles\Rating\Events\ModelRated;
use Laraveles\Rating\Events\ModelUnrated;
use Laraveles\Rating\Exception\InvalidScore;

trait CanRate
{
    /**
     * @param Model|null $model
     * @param bool $approved
     * @return mixed
     */
    public function ratings($model = null, bool $approved = false)
    {
        $modelClass = $model ? (new $model)->getMorphClass() : $this->getMorphClass();

        $morphToMany = $this->morphToMany(
            $modelClass,
            'qualifier',
            'ratings',
            'qualifier_id',
            'rateable_id'
        );

        if ($approved) {
            $morphToMany->wherePivot('approved_at', '<>', null);
        }

        $morphToMany
            ->as('rating')
            ->withTimestamps()
            ->withPivot('rateable_type', 'score', 'approved_at')
            ->wherePivot('rateable_type', $modelClass)
            ->wherePivot('qualifier_type', $this->getMorphClass());

        return $morphToMany;
    }

    /**
     * @param Rateable $model
     * @param float $score
     * @param string|null $comments
     * @return bool
     * @throws \Exception
     */
    public function rate(Rateable $model, float $score, string $comments = null): bool
    {
        if ($this->hasRated($model)) {
            return false;
        }

        $from = config('rating.from');
        $to = config('rating.to');

        if ($score < $from || $score > $to) {
            throw new InvalidScore();
        }

        $this->ratings($model)->attach($model->getKey(), [
            'score' => $score,
            'comments' => $comments,
            'approved_at' => config('rating.required_approval', false) ? null : Carbon::now(),
            'rateable_type' => get_class($model),
        ]);

        event(new ModelRated($this, $model, $score));

        return true;
    }

    /**
     * @param Rateable|Model $model
     * @return bool
     */
    public function unrate(Rateable $model): bool
    {
        if (! $this->hasRated($model)) {
            return false;
        }

        $this->ratings($model->getMorphClass())->detach($model->getKey());

        event(new ModelUnrated($this, $model));

        return true;
    }

    /**
     * @param Rateable|Model $model
     * @return bool
     */
    public function hasRated(Rateable $model): bool
    {
        return ! is_null($this->ratings($model->getMorphClass())->find($model->getKey()));
    }

    /**
     * @param Rateable|Model $model
     * @param float $newRating
     * @return bool
     * @throws \Exception
     */
    public function updateRatingFor(Rateable $model, float $newRating): bool
    {
        if (! $this->hasRated($model)) {
            return $this->rate($model, $newRating);
        }

        $this->ratings($model->getMorphClass())->detach($model->getKey());

        return $this->rate($model, $newRating);
    }
}
