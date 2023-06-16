<?php

declare(strict_types=1);

namespace Petk\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

class ContainerEntryNotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
