<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Auby;

final class QuoteCategories
{
    public const GENERAL      = 'general';
    public const REGISTER     = 'register';
    public const RECIPES      = 'recipes';
    public const PANTRY       = 'pantry';
    public const BESTIARY     = 'bestiary';
    public const CAMPAIGNS    = 'campaigns';
    public const ACHIEVEMENTS = 'achievements';
    public const SETTINGS     = 'settings';

    /**
     * @return array<string>
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
        ];
    }

    public static function isValid(string $category): bool
    {
        return in_array($category, self::all(), true);
    }

    private function __construct()
    {
    }
}
