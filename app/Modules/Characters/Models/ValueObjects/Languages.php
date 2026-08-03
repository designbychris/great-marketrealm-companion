<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Languages Value Object.
 *
 * Represents the immutable collection of languages
 * known by a Character.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Languages
{
    /**
     * Create a Languages collection.
     *
     * @param array<int,Language> $languages
     */
    private function __construct(
        private array $languages
    ) {
        $this->guardAgainstInvalidCollection(
            $languages
        );
    }

    /**
     * Create an empty Languages collection.
     */
    public static function none(): self
    {
        return new self([]);
    }

    /**
     * Create a collection from Language values.
     */
    public static function fromLanguages(
        Language ...$languages
    ): self {
        return new self(
            self::normaliseLanguages(
                $languages
            )
        );
    }

    /**
     * Create a collection from primitive identifiers.
     *
     * @param array<int,mixed> $languages
     */
    public static function fromStrings(
        array $languages
    ): self {
        $resolved = [];

        foreach ($languages as $language) {
            if (! is_string($language)) {
                throw new InvalidArgumentException(
                    'Character language identifiers must be strings.'
                );
            }

            $resolved[] = Language::fromString(
                $language
            );
        }

        return self::fromLanguages(
            ...$resolved
        );
    }

    /**
     * Return all known languages.
     *
     * @return array<int,Language>
     */
    public function all(): array
    {
        return $this->languages;
    }

    /**
     * Return all canonical language identifiers.
     *
     * @return array<int,string>
     */
    public function values(): array
    {
        return array_map(
            static fn (
                Language $language
            ): string => $language->value(),
            $this->languages
        );
    }

    /**
     * Determine whether the Character knows a language.
     */
    public function has(
        Language|string $language
    ): bool {
        $language = $language instanceof Language
            ? $language
            : Language::fromString($language);

        foreach ($this->languages as $knownLanguage) {
            if ($knownLanguage->equals($language)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a language immutably.
     */
    public function add(
        Language|string $language
    ): self {
        $language = $language instanceof Language
            ? $language
            : Language::fromString($language);

        if ($this->has($language)) {
            return $this;
        }

        return new self(
            self::normaliseLanguages([
                ...$this->languages,
                $language,
            ])
        );
    }

    /**
     * Remove a language immutably.
     */
    public function remove(
        Language|string $language
    ): self {
        $language = $language instanceof Language
            ? $language
            : Language::fromString($language);

        if (! $this->has($language)) {
            return $this;
        }

        return new self(
            array_values(
                array_filter(
                    $this->languages,
                    static fn (
                        Language $knownLanguage
                    ): bool => ! $knownLanguage->equals(
                        $language
                    )
                )
            )
        );
    }

    /**
     * Merge another Languages collection.
     */
    public function merge(
        self $other
    ): self {
        return new self(
            self::normaliseLanguages([
                ...$this->languages,
                ...$other->languages,
            ])
        );
    }

    /**
     * Determine whether no languages are known.
     */
    public function isEmpty(): bool
    {
        return $this->languages === [];
    }

    /**
     * Return the number of known languages.
     */
    public function count(): int
    {
        return count(
            $this->languages
        );
    }

    /**
     * Determine whether this collection equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->values()
            === $other->values();
    }

    /**
     * Remove duplicates and return canonical ordering.
     *
     * @param array<int,Language> $languages
     *
     * @return array<int,Language>
     */
    private static function normaliseLanguages(
        array $languages
    ): array {
        $byValue = [];

        foreach ($languages as $language) {
            if (! $language instanceof Language) {
                throw new InvalidArgumentException(
                    'Languages collections may contain only Language values.'
                );
            }

            $byValue[
                $language->value()
            ] = $language;
        }

        $ordered = [];

        foreach (Language::all() as $supportedLanguage) {
            if (
                isset(
                    $byValue[
                        $supportedLanguage->value()
                    ]
                )
            ) {
                $ordered[] = $byValue[
                    $supportedLanguage->value()
                ];
            }
        }

        return $ordered;
    }

    /**
     * Guard against invalid collection members.
     *
     * @param array<int,Language> $languages
     */
    private function guardAgainstInvalidCollection(
        array $languages
    ): void {
        foreach ($languages as $language) {
            if (! $language instanceof Language) {
                throw new InvalidArgumentException(
                    'Languages collections may contain only Language values.'
                );
            }
        }
    }
}
