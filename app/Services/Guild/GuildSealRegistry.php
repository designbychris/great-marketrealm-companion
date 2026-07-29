<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Guild;

defined('ABSPATH') || exit;

/**
 * Stores the visual heraldry used for adventurer class seals.
 *
 * @since 0.3.0
 */
final class GuildSealRegistry
{
    /**
     * Default seal configuration.
     *
     * @var array<string, string>
     */
    private const DEFAULT_SEAL = [
        'symbol'  => '✦',
        'variant' => 'wax',
        'size'    => 'medium',
    ];

    /**
     * Registered class seals.
     *
     * @var array<string, array<string, string>>
     */
    private array $seals = [
        'artificer' => [
            'symbol'  => '⚙',
            'variant' => 'gold',
        ],
        'barbarian' => [
            'symbol'  => '◆',
            'variant' => 'wax',
        ],
        'bard' => [
            'symbol'  => '♪',
            'variant' => 'purple',
        ],
        'cleric' => [
            'symbol'  => '✚',
            'variant' => 'gold',
        ],
        'druid' => [
            'symbol'  => '❦',
            'variant' => 'ink',
        ],
        'fighter' => [
            'symbol'  => '⚔',
            'variant' => 'wax',
        ],
        'monk' => [
            'symbol'  => '☯',
            'variant' => 'gold',
        ],
        'paladin' => [
            'symbol'  => '♜',
            'variant' => 'wax',
        ],
        'ranger' => [
            'symbol'  => '➶',
            'variant' => 'ink',
        ],
        'rogue' => [
            'symbol'  => '♦',
            'variant' => 'ink',
        ],
        'sorcerer' => [
            'symbol'  => '✧',
            'variant' => 'purple',
        ],
        'warlock' => [
            'symbol'  => '☾',
            'variant' => 'purple',
        ],
        'wizard' => [
            'symbol'  => '✦',
            'variant' => 'purple',
        ],
    ];

    /**
     * Find the seal configuration for an adventurer class.
     *
     * @return array<string, string>
     */
    public function for(string $class): array
    {
        $className = trim($class);
        $key       = $this->normalise($className);

        $seal = array_merge(
            self::DEFAULT_SEAL,
            $this->seals[$key] ?? []
        );

        $seal['label'] = $className !== ''
            ? sprintf('%s Guild Seal', $className)
            : 'Adventurer Guild Seal';

        $seal['class'] = $key !== ''
            ? 'guild-seal--' . $key
            : 'guild-seal--adventurer';

        return $seal;
    }

    /**
     * Register or replace a class seal.
     *
     * @param array<string, string> $seal
     */
    public function register(
        string $class,
        array $seal
    ): void {
        $key = $this->normalise($class);

        if ($key === '') {
            return;
        }

        $this->seals[$key] = array_merge(
            $this->seals[$key] ?? [],
            $this->sanitizeSeal($seal)
        );
    }

    /**
     * Determine whether a class has a registered seal.
     */
    public function has(string $class): bool
    {
        return isset(
            $this->seals[$this->normalise($class)]
        );
    }

    /**
     * Convert a class name into a registry key.
     */
    private function normalise(string $class): string
    {
        $class = strtolower(trim($class));

        $class = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $class
        );

        return trim((string) $class, '-');
    }

    /**
     * Allow only supported seal properties.
     *
     * @param array<string, string> $seal
     *
     * @return array<string, string>
     */
    private function sanitizeSeal(array $seal): array
    {
        return array_intersect_key(
            $seal,
            array_flip([
                'symbol',
                'variant',
                'size',
            ])
        );
    }
}
