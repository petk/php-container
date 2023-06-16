<?php

declare(strict_types=1);

namespace Petk\Container;

use Petk\Container\Exception\ContainerEntryNotFoundException;
use Petk\Container\Exception\ContainerException;
use Psr\Container\ContainerInterface;

/**
 * PSR-11 compatible dependency injection container.
 */
class Container implements ContainerInterface
{
    /**
     * All defined services and parameters.
     */
    public array $entries = [];

    /**
     * Already retrieved items are stored for faster retrievals in the same run.
     */
    private array $store = [];

    /**
     * Services already created to prevent circular references.
     */
    private array $locks = [];

    /**
     * Class constructor.
     */
    public function __construct(array $configurations = [])
    {
        $this->entries = $configurations;
    }

    /**
     * Set service.
     */
    public function set(string $key, mixed $entry): void
    {
        $this->entries[$key] = $entry;
    }

    /**
     * Get entry.
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new ContainerEntryNotFoundException($id . ' entry not found.');
        }

        if (!isset($this->store[$id])) {
            $this->store[$id] = $this->createEntry($id);
        }

        return $this->store[$id];
    }

    /**
     * Check if entry is available in the container.
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * Create new entry - service or configuration parameter.
     */
    private function createEntry(string $id): mixed
    {
        $entry = &$this->entries[$id];

        // Entry is a configuration parameter.
        if (!class_exists($id) && !is_callable($entry)) {
            return $entry;
        }

        // Invalid entry.
        if (class_exists($id) && !is_callable($entry)) {
            throw new ContainerException($id . ' entry must be callable.');
        }

        // Circular reference.
        if (class_exists($id) && isset($this->locks[$id])) {
            throw new ContainerException($id . ' entry contains a circular reference.');
        }

        $this->locks[$id] = true;

        return $entry($this);
    }
}
