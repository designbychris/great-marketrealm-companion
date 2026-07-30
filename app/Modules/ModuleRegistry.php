<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\Modules;

use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Registered application modules.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
final class ModuleRegistry
{
    /**
     * Registered module classes.
     *
     * @var array<string, class-string<Module>>
     */
    private array $modules = [];

    /**
     * Register a module class.
     *
     * @param class-string<Module> $moduleClass
     */
    public function register(string $moduleClass): void
    {
        if (! is_subclass_of($moduleClass, Module::class)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid application module: %s',
                    $moduleClass
                )
            );
        }

        $key = strtolower(
            str_replace(
                '\\',
                '.',
                $moduleClass
            )
        );

        $this->modules[$key] = $moduleClass;
    }

    /**
     * Return all registered module classes.
     *
     * @return array<int, class-string<Module>>
     */
    public function all(): array
    {
        return array_values($this->modules);
    }

    /**
     * Determine whether a module class is registered.
     *
     * @param class-string<Module> $moduleClass
     */
    public function has(string $moduleClass): bool
    {
        return in_array(
            $moduleClass,
            $this->modules,
            true
        );
    }
}
