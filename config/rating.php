<?php

return [
    'models' => [
        'rating' => \Laraveles\Rating\Models\Rating::class,
    ],
    'required_approval' => true,
    'from' => 1,
    'to' => 5,
];
