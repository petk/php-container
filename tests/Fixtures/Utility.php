<?php

declare(strict_types=1);

namespace Petk\Container\Tests\Fixtures;

class Utility
{
    public function __construct(private Doer $doer)
    {
    }

    public function getDoer(): Doer
    {
        return $this->doer;
    }
}
