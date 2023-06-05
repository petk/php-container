<?php

declare(strict_types=1);

namespace Petk\Container\Tests\Container;

use Petk\Container\Container;
use Petk\Container\Tests\ContainerTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ContainerTest extends ContainerTestCase
{
    #[DataProvider('keyProvider')]
    public function testSet($key, $valid): void
    {
        $container = new Container();

        $container->set(Database::class, function ($c) {
            return new Database();
        });

        $this->assertEquals($valid, $container->get($key));
    }

    public static function keyProvider(): array
    {
        return [
            [Database::class, new Database()],
        ];
    }
}

class Database
{
}
