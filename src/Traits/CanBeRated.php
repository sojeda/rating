<?php

namespace Laraveles\Rating\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laraveles\Rating\Contracts\Qualifier;

trait CanBeRated
{
    /**
     * Relationship for models that rated this model.
     *
     * @param Model|null $model The model types of the results.
     * @return morphToMany The relationship.
     */
    public function qualifiers($model = null, bool $approved = true)
    {
        $modelClass = $model ? (new $model)->getMorphClass() : $this->getMorphClass();

        /** @var MorphToMany $morphToMany */
        $morphToMany = $this->morphToMany(
            $model ?: $this->getMorphClass(),
            'rateable',
            'ratings',
            'rateable_id',
            'qualifier_id'
        );

        if ($approved) {
            $morphToMany->wherePivot('approved_at', '<>', null);
        }

        return $morphToMany
                    ->withPivot('qualifier_type', 'score', 'comments', 'approved_at')
                    ->wherePivot('qualifier_type', $modelClass)
                    ->wherePivot('rateable_type', $this->getMorphClass());
    }

    /**
     * @param string|null $modelType
     * @param bool $approved
     * @return HasMany
     */
    public function qualifications(string $modelType = null, bool $approved = false): HasMany
    {
        $hasMany = $this->hasMany(config('rating.models.rating'), 'rateable_id');

        if ($modelType) {
            $hasMany->where('qualifier_type', $modelType);
        }

        if ($approved) {
            $hasMany->whereNotNull('approved_at');
        }

        return $hasMany
            ->where('rateable_type', $this->getMorphClass());
    }

    /**
     * @param Qualifier|Model $model
     * @return bool
     */
    public function hasRateBy(Qualifier $model): bool
    {
        return $this->qualifications()
            ->where('qualifier_id', $model->getKey())
            ->where('qualifier_type', get_class($model))
            ->exists();
    }

    /**
     * @param string|null $modelType
     * @return float
     */
    public function averageRating(string $modelType = null, bool $approved = false): float
    {
        return $this->qualifications($modelType, $approved)->avg('score') ?: 0.0;
    }
}
