<?php

declare(strict_types=1);

namespace Petk\Container\Tests\Fixtures\CircularReference;

class ParentClass
{
    public function __construct(private ChildClass $child)
    {
    }

    public function getChild(): ChildClass
    {
        return $this->child;
    }
}
