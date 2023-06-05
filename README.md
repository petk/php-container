# PHP dependency injection container

![Test workflow](https://github.com/petk/php-container/actions/workflows/tests.yaml/badge.svg)

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

## Circular dependencies

Circular dependencies are a bad practice where two classes have dependencies of
each other. See the following example:

```php

// ...

class ChildClass
{
    public function __construct(private ParentClass $parent)
    {
    }
}

class ParentClass
{
    public function __construct(private ChildClass $child)
    {
    }
}

$container->set(ParentClass::class, function ($c) {
    return new ParentClass($c->get(ChildClass::class));
});

$container->set(ChildClass::class, function ($c) {
    return new ChildClass($c->get(ParentClass::class));
});

// This will throw ContainerException.
```

Above will throw the `ContainerException` which means building dependencies
should be done differently.

## Development

PHPUnit is used to run tests:

```sh
./vendor/bin/phpunit --display-warnings
```
