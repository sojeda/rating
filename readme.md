[![Build Status](https://travis-ci.org/Laraveles/rating.svg?branch=master)](https://travis-ci.org/Laraveles/rating)
[![codecov](https://codecov.io/gh/Laraveles/rating/branch/master/graph/badge.svg)](https://codecov.io/gh/Laraveles/rating/branch/master)
[![StyleCI](https://github.styleci.io/repos/142049701/shield?branch=master)](https://github.styleci.io/repos/141194551)
[![Latest Stable Version](https://poser.pugx.org/Laraveles/rating/v/stable)](https://packagist.org/packages/Laraveles/rating)
[![Total Downloads](https://poser.pugx.org/Laraveles/rating/downloads)](https://packagist.org/packages/Laraveles/rating)
[![Monthly Downloads](https://poser.pugx.org/Laraveles/rating/d/monthly)](https://packagist.org/packages/Laraveles/rating)
[![License](https://poser.pugx.org/Laraveles/rating/license)](https://packagist.org/packages/Laraveles/rating)

[![PayPal](https://img.shields.io/badge/PayPal-donate-blue.svg)](https://paypal.me/Laraveles)

# Laravel Eloquent Rating
Laravel Eloquent Rating allows you to assign ratings to any model.

# Installation
Install the package:

```php
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/sojeda/rating"
    }
],
```


```bash
$ composer require laraveles/rating
```

If your Laravel version does not support package discovery, add this line in the `providers` array in your `config/app.php` file:
```php
Laraveles\Rating\RatingServiceProvider::class,
```

Publish the config file & migration files:
```bash
$ php artisan vendor:publish --provider='Laraveles\Rating\RatingServiceProvider'
```

Migrate the database:
```bash
$ php artisan migrate
```

# Preparing the Model
To allow a model to rate other models, it should use the `CanRate` trait and implement the  `Qualifier` contract.
```php
use Laraveles\Rating\Traits\CanRate;
use Laraveles\Rating\Contracts\Rater;

class User extends Model implements \Laraveles\Rating\Contracts\Qualifier {
    use CanRate;
    ...
}
```

The other models that can be rated should use `CanBeRated` trait and `Rateable` contract.
```php
use Laraveles\Rating\Traits\CanBeRated;
use Laraveles\Rating\Contracts\Rateable;

class User extends Model implements Rateable {
    use CanBeRated;
    ...
}
```

If your model can both rate & be rated by other models, you should use `Rate` trait and `Rating` contract.
```php
use Laraveles\Rating\Traits\Rate;
use Laraveles\Rating\Contracts\Rating;

class User extends Model implements Rating {
    use Rate;
    ...
}
```

# Usage
To rate other models, simply call `rate()` method:
```php
$page = Page::find(1);

$user->rate($page, 10);
$user->hasRated($page); // true
$page->averageRating(User::class); // 10.0, as float
```

As a second argument to the `rate()` method, you can pass the rating score. It can either be string, integer or float.

To update a rating, you can call `updateRatingFor()` method:
```php
$user->updateRatingFor($page, 9);
$page->averageRating(User::class); // 9.00, as float
```

As you have seen, you can call `averageRating()` within models that can be rated. The return value is the average arithmetic value of all ratings as `float`.

If we leave the argument empty, we will get `0.00` because no other `Page` model has rated the page so far. But since users have rated the page, we will calculate the average only from the `User` models, since only they have voted the page, strictly by passing the class name as the argument.
```php
$page = Page::find(1);

$user1->rate($page, 10);
$user2->rate($page, 6);

$page->averageRating(); // 0.00
$page->averageRating(User::class); // 8.00, as float
```

While in our example, the `User` class can both rate and be rated, we can leave the argument empty if we reference to its class:
```php
$user = User::find(1);

$user1->rate($user, 10);
$user2->rate($user, 6);

$user->averageRating(); // 8.00, as float
$user->averageRating(User::class); // 8.00, it is equivalent
```

The relationships are based on this too:
```php
$page->raters()->get(); // Pages that have rated this page
$page->raters(User::class)->get(); // Users that have rated this page

$user->ratings()->get(); // Users that this user has rated
$user->ratings(Page::class)->get(); // Pages that this user has rated
```

## Events

```ModelRated```

You can define your own listeners in your app's EventServiceProvider. E.g.:

```php
<?php

use Laraveles\Rating\Events\ModelRated;
use Laraveles\Rating\Events\ModelUnrated;

/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    ModelRated::class => [
        \App\Listeners\MyListenerRating::class,
    ],
    ModelUnrated::class => [
        \App\Listeners\MyListenerUnrating::class,
    ],
];
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email soj3da@gmail.com instead of using the issue tracker.

## Credits

- [rennokki](https://github.com/rennokki/rating)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

