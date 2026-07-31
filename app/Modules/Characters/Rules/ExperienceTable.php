<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Rules;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Character experience progression table.
 *
 * Acts as the single source of truth for the XP required
 * to reach each character level.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class ExperienceTable
{
    /**
     * Experience required to reach each level.
     *
     * Based on the D&D 5e SRD progression.
     *
     * @var array<int,int>
     */
    private const TABLE = [
        1  => 0,
        2  => 300,
        3  => 900,
        4  => 2700,
        5  => 6500,
        6  => 14000,
        7  => 23000,
        8  => 34000,
        9  => 48000,
        10 => 64000,
        11 => 85000,
        12 => 100000,
        13 => 120000,
        14 => 140000,
        15 => 165000,
        16 => 195000,
        17 => 225000,
        18 => 265000,
        19 => 305000,
        20 => 355000,
    ];

    /**
     * Experience required to reach a level.
     */
    public static function requiredFor(Level $level): int
    {
        return self::TABLE[
            $level->value()
        ];
    }

    /**
     * Determine the level represented by the supplied experience.
     */
    public static function levelForExperience(int $experience): Level
    {
        if ($experience < 0) {
            throw new InvalidArgumentException(
                'Experience cannot be negative.'
            );
        }

        foreach (array_reverse(self::TABLE, true) as $level => $required) {
            if ($experience >= $required) {
                return Level::fromInt($level);
            }
        }

        return Level::one();
    }

    /**
     * Experience required for the next level.
     */
    public static function requiredForNext(Level $level): ?int
    {
        if ($level->isMaximum()) {
            return null;
        }

        return self::requiredFor(
            $level->next()
        );
    }

    /**
     * Maximum supported level.
     */
    public static function maximumLevel(): Level
    {
        return Level::fromInt(
            max(array_keys(self::TABLE))
        );
    }
}
