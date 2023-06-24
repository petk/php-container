<?php

declare(strict_types=1);

namespace Petk\Container\Tests;

use Petk\Container\Container;
use Petk\Container\Exception\ContainerCircularDependencyException;
use Petk\Container\Exception\ContainerEntryNotFoundException;
use Petk\Container\Exception\ContainerInvalidEntryException;
use Petk\Container\Tests\Fixtures\CircularReference\ChildClass;
use Petk\Container\Tests\Fixtures\CircularReference\ParentClass;
use Petk\Container\Tests\Fixtures\Database;
use Petk\Container\Tests\Fixtures\Doer;
use Petk\Container\Tests\Fixtures\Utility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @internal
 *
 * @coversNothing
 */
class ContainerTest extends ContainerTestCase
{
    /**
     * @throws ExpectationFailedException
     */
    #[DataProvider('constructorEntriesProvider')]
    public function testConstructor(string $id, mixed $valid): void
    {
        $entries = [
            'database_username' => 'foobar',
            'some_configuration' => 42,
        ];

        $container = new Container($entries);

        $this->assertEquals($valid, $container->get($id));
    }

    /**
     * @return array<int,array<int,mixed>>
     */
    public static function constructorEntriesProvider(): array
    {
        return [
            ['database_username', 'foobar'],
            ['some_configuration', 42],
        ];
    }

    /**
     * @param class-string<object> $valid
     *
     * @throws Exception
     */
    #[DataProvider('idProvider')]
    public function testSet(string $id, string $valid): void
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

        $this->assertInstanceOf($valid, $container->get($id));
    }

    /**
     * @return array<int,array<int,class-string|string>>
     */
    public static function idProvider(): array
    {
        return [
            [Database::class, Database::class],
            [Doer::class, Doer::class],
            [Utility::class, Utility::class],
            ['foo-bar', \stdClass::class],
        ];
    }

    /**
     * @throws ContainerInvalidEntryException
     */
    public function testSetInvalidEntry(): void
    {
        $this->expectException(ContainerInvalidEntryException::class);

        $container = new Container();

        $container->set(ParentClass::class, new \stdClass());
    }

    /**
     * @throws ContainerCircularDependencyException
     */
    public function testSetCircularDependency(): void
    {
        $this->expectException(ContainerCircularDependencyException::class);

        $container = new Container();

        $container->set(ParentClass::class, function ($c) {
            return new ParentClass($c->get(ChildClass::class));
        });

        $container->set(ChildClass::class, function ($c) {
            return new ChildClass($c->get(ParentClass::class));
        });

        $container->get(ParentClass::class);
    }

    /**
     * @throws ContainerEntryNotFoundException
     */
    public function testGetMissingEntry(): void
    {
        $this->expectException(ContainerEntryNotFoundException::class);

        $container = new Container();

        $container->set(\stdClass::class, function ($c) {
            return new \stdClass();
        });

        $container->get(ParentClass::class);
    }

    /**
     * @throws ExpectationFailedException
     */
    #[DataProvider('hasProvider')]
    public function testHas(string $id, bool $has): void
    {
        $entries = [
            'configuration_option' => 42,
            'username' => 'john',
        ];

        $container = new Container($entries);

        $container->set(\stdClass::class, function ($c) {
            return new \stdClass();
        });

        $this->assertEquals($has, $container->has($id));
    }

    /**
     * @return array<int,array<int,bool|class-string|string>>
     */
    public static function hasProvider(): array
    {
        return [
            [Database::class, false],
            ['username', true],
            ['configuration_option', true],
            [Doer::class, false],
            [\stdClass::class, true],
        ];
    }
}
