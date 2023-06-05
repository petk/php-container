# PHP dependency injection container

This is a PHP package with PSR-11 compatible dependency injection container.

## Installation

```sh
composer require petk/php-container
```

## Usage

```php
<?php

// Require Composer autoloader.
require __DIR__ . '/vendor/autoload.php';

// Use the Container class.
use Petk\Container\Container;

// Container instantiation.
$container = new Container();

// Adding dependencies to container is as easy as setting them to the given key.
$container->set(App\Utility::class, function ($c) {
    return new App\Utility();
});

// Adding more complex dependencies can be done like this.
$container->set(App\Database::class, function ($c) {
    return new App\Database($c->get(App\Utility::class));
});
```
