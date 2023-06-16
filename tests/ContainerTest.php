<?php

declare(strict_types=1);

namespace Petk\Container\Tests;

use Petk\Container\Container;
use Petk\Container\Exception\ContainerEntryNotFoundException;
use Petk\Container\Exception\ContainerException;
use Petk\Container\Tests\Fixtures\CircularReference\ChildClass;
use Petk\Container\Tests\Fixtures\CircularReference\ParentClass;
use Petk\Container\Tests\Fixtures\Database;
use Petk\Container\Tests\Fixtures\Doer;
use Petk\Container\Tests\Fixtures\Utility;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @coversNothing
 */
class ContainerTest extends ContainerTestCase
{
    #[DataProvider('keyProvider')]
    public function testSet(string $key, string $valid): void
    {
        $container = new Container();

        $container->set(Database::class, function ($c) {
            return new Database();
        });

        $container->set(Doer::class, function ($c) {
            return new Doer();
        });

        $container->set(Utility::class, function ($c) {
            return new Utility($c->get(Doer::class));
        });

        $container->set('foo-bar', function ($c) {
            return new \stdClass();
        });

        $this->assertInstanceOf($valid, $container->get($key));
    }

    public function testSetCircularDependency(): void
    {
        $this->expectException(ContainerException::class);

        $container = new Container();

        $container->set(ParentClass::class, function ($c) {
            return new ParentClass($c->get(ChildClass::class));
        });

        $container->set(ChildClass::class, function ($c) {
            return new ChildClass($c->get(ParentClass::class));
        });

        $parent = $container->get(ParentClass::class);
    }

    public function testSetInvalidEntry(): void
    {
        $this->expectException(ContainerException::class);

        $container = new Container();

        $container->set(ParentClass::class, new \stdClass());

        $parent = $container->get(ParentClass::class);
    }

    public function testGetMissingEntry(): void
    {
        $this->expectException(ContainerEntryNotFoundException::class);

        $container = new Container();

        $container->set(\stdClass::class, function ($c) {
            return new \stdClass();
        });

        $parent = $container->get(ParentClass::class);
    }

    public static function keyProvider(): array
    {
        return [
            [Database::class, Database::class],
            [Doer::class, Doer::class],
            [Utility::class, Utility::class],
            ['foo-bar', \stdClass::class],
        ];
    }
}
