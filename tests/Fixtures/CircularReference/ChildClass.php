<?php

namespace Petk\Container\Tests\Fixtures\CircularReference;

class ChildClass
{
    public function __construct(private ParentClass $parent)
    {
    }
}
