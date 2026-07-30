<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Codex;

use Countable;
use GreatMarketrealmCompanion\Services\Registry\Registry;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * The Great Marketrealm Codex.
 *
 * Discovers and organises registered game knowledge.
 *
 * @since 0.3.0
 */
final class Codex implements Countable
{
    /**
     * Codex sections indexed by their unique keys.
     *
     * @var array<string, CodexSection>
     */
    private array $sections = [];

    /**
     * Register a Codex section.
     */
    public function registerSection(
        string $key,
        string $name
    ): CodexSection {
        if ($this->hasSection($key)) {
            return $this->sections[$key];
        }

        $section = new CodexSection(
            key: $key,
            name: $name
        );

        $this->sections[$key] = $section;

        return $section;
    }

    /**
     * Contribute a registry to a Codex section.
     */
    public function register(
        string $section,
        string $name,
        Registry $registry
    ): self {
        $this->registerSection(
            key: $section,
            name: $name
        )->addRegistry($registry);

        return $this;
    }

    /**
     * Retrieve a Codex section.
     */
    public function section(string $key): CodexSection
    {
        if (!$this->hasSection($key)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Codex section "%s" has not been registered.',
                    $key
                )
            );
        }

        return $this->sections[$key];
    }

    /**
     * Determine whether a section exists.
     */
    public function hasSection(string $key): bool
    {
        return isset($this->sections[$key]);
    }

    /**
     * Return every registered section.
     *
     * @return array<string, CodexSection>
     */
    public function sections(): array
    {
        return $this->sections;
    }

    /**
     * Return all entries grouped by section.
     *
     * @return array<string, CodexCollection>
     */
    public function all(): array
    {
        $collections = [];

        foreach ($this->sections as $key => $section) {
            $collections[$key] = $section->entries();
        }

        return $collections;
    }

    /**
     * Return the races collection.
     */
    public function races(): CodexCollection
    {
        return $this->section('races')->entries();
    }

    /**
     * Return the classes collection.
     */
    public function classes(): CodexCollection
    {
        return $this->section('classes')->entries();
    }

    /**
     * Return the number of entries across every section.
     */
    public function count(): int
    {
        $count = 0;

        foreach ($this->sections as $section) {
            $count += $section->count();
        }

        return $count;
    }
}
