<?php

return [

    /*
     * The models for rating tables.
     */

    'models' => [
        'rating' => \Laraveles\Rating\Models\RaterModel::class,
        'user' => \Laraveles\Rating\Models\User::class,
    ],

    'required_approval' => true,

];
