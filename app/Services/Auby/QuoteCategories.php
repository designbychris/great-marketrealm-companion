<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Auby;

final class QuoteCategories
{
    public const GENERAL = 'general';
    public const REGISTER = 'register';
    public const RECIPES = 'recipes';
    public const PANTRY = 'pantry';
    public const BESTIARY = 'bestiary';
    public const CAMPAIGNS = 'campaigns';
    public const ACHIEVEMENTS = 'achievements';
    public const SETTINGS = 'settings';

    /*
     * Character Creator guidance.
     */
    public const CHARACTER_CREATOR = 'character-creator';
    public const CHARACTER_NAME = 'character-name';
    public const CHARACTER_RACE = 'character-race';
    public const CHARACTER_CLASS = 'character-class';
    public const CHARACTER_READY = 'character-ready';

    /**
     * Return every supported quote category.
     *
     * @return array<int,string>
     */
    public static function all(): array
    {
        return [
            self::GENERAL,
            self::REGISTER,
            self::RECIPES,
            self::PANTRY,
            self::BESTIARY,
            self::CAMPAIGNS,
            self::ACHIEVEMENTS,
            self::SETTINGS,
            self::CHARACTER_CREATOR,
            self::CHARACTER_NAME,
            self::CHARACTER_RACE,
            self::CHARACTER_CLASS,
            self::CHARACTER_READY,
        ];
    }

    /**
     * Determine whether a quote category is supported.
     */
    public static function isValid(
        string $category
    ): bool {
        return in_array(
            $category,
            self::all(),
            true
        );
    }

    private function __construct()
    {
    }
}
