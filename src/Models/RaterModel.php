<?php

namespace Laraveles\Rating\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class RaterModel extends Model
{
    protected $table = 'ratings';
    protected $guarded = [];
    protected $casts = [
        'rating' => 'float',
    ];
    protected $dates = [
        'approved_at'
    ];

    public function rateable()
    {
        return $this->morphTo();
    }

    public function rater()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(config('rating.models.user'), 'user_id');
    }

    public function scopeApproved(Builder $builder)
    {
        return $builder->whereNotNull('approved_at');
    }

    public function scopeNotApproved(Builder $builder)
    {
        return $builder->whereNull('approved_at');
    }
}
