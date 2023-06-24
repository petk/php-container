<?php

declare(strict_types=1);

namespace Petk\Container\Exception;

use Psr\Container\ContainerExceptionInterface;

class ContainerCircularDependencyException extends \RuntimeException implements ContainerExceptionInterface
{
}
