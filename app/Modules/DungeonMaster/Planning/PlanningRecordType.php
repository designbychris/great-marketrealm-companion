<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Planning;

defined('ABSPATH') || exit;

/**
 * Shared vocabulary for campaign-planning records that will grow from the
 * Dungeon Master's Desk. Keeping these stable prevents the future visual
 * boards and Tabletop bridge from inventing their own incompatible names.
 */
final class PlanningRecordType
{
    public const LOCATION = 'location';
    public const PERSON = 'person';
    public const FACTION = 'faction';
    public const EVIDENCE = 'evidence';
    public const THREAD = 'thread';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::LOCATION,
            self::PERSON,
            self::FACTION,
            self::EVIDENCE,
            self::THREAD,
        ];
    }

    private function __construct()
    {
    }
}
