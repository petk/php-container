<?php

declare(strict_types=1);

namespace Petk\Container\Tests\Fixtures\CircularReference;

class ChildClass
{
    public function __construct(private ParentClass $parent)
    {
    }
}
