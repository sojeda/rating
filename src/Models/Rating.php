<?php

namespace Laraveles\Rating\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Query\Builder;

class Rating extends Pivot
{
    protected $table = 'ratings';
    protected $guarded = [];
    protected $casts = [
        'rating' => 'float',
    ];
    protected $dates = [
        'approved_at',
    ];

    public function rateable()
    {
        return $this->morphTo();
    }

    public function rater()
    {
        return $this->morphTo();
    }

    public function scopeApproved(Builder $builder): Builder
    {
        return $builder->whereNotNull('approved_at');
    }

    public function scopeNotApproved(Builder $builder): Builder
    {
        return $builder->whereNull('approved_at');
    }
}
