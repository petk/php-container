<?php

declare(strict_types=1);

namespace Petk\Container\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Generic exception in a container.
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{
}
