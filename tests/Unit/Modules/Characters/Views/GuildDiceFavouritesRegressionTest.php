<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceFavouritesRegressionTest extends TestCase
{
    public function testLedgerExposesPersistentQuickRollControls(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'data-guild-dice-launcher',
            $view
        );
        self::assertStringContainsString(
            'Quick Rolls',
            $view
        );
        self::assertStringContainsString(
            'data-guild-favourite-toggle',
            $view
        );
        self::assertStringContainsString(
            'data-guild-quick-rolls',
            $view
        );
        self::assertStringContainsString(
            'data-guild-quick-roll-list',
            $view
        );
        self::assertStringContainsString(
            'data-guild-free-roll-pin',
            $view
        );
        self::assertStringContainsString(
            'Save as Quick Roll',
            $view
        );
    }

    public function testCharacterFavouritesResolveCurrentLedgerTriggers(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const MAX_FAVOURITES = 8;',
            $script
        );
        self::assertStringContainsString(
            "'gmrc:guild-dice:favourites:' + characterId",
            $script
        );
        self::assertStringContainsString(
            'window.localStorage.getItem(favouritesKey)',
            $script
        );
        self::assertStringContainsString(
            'window.localStorage.setItem(',
            $script
        );
        self::assertStringContainsString(
            'const triggerReference = function (trigger)',
            $script
        );
        self::assertStringContainsString(
            'const findTriggerByReference = function (reference)',
            $script
        );
        self::assertStringContainsString(
            'trigger.dataset.rollModifier',
            $script
        );
        self::assertStringContainsString(
            "openTray(trigger);",
            $script
        );
        self::assertStringContainsString(
            "perform('normal');",
            $script
        );
    }

    public function testFreeRollFavouritesPersistTheirDesignedFormula(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const freeRollDefinition = function ()',
            $script
        );
        self::assertStringContainsString(
            "const key = 'free|' + formula + '|' + modifier;",
            $script
        );
        self::assertStringContainsString(
            "type: 'free'",
            $script
        );
        self::assertStringContainsString(
            'quantity: quantity',
            $script
        );
        self::assertStringContainsString(
            'sides: safeSides',
            $script
        );
        self::assertStringContainsString(
            'modifier: modifier',
            $script
        );
        self::assertStringContainsString(
            "freeRollPin.addEventListener('click', addFreeFavourite);",
            $script
        );
        self::assertStringContainsString(
            'performFreeRoll();',
            $script
        );
    }

    public function testQuickRollsCanBeRemovedAndStorageFailureDoesNotBreakDice(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const removeFavourite = function (key)',
            $script
        );
        self::assertStringContainsString(
            'favourites.splice(index, 1);',
            $script
        );
        self::assertStringContainsString(
            'persistFavourites();',
            $script
        );
        self::assertStringContainsString(
            'Favourites are optional; rolling must remain available.',
            $script
        );
        self::assertStringContainsString(
            'Quick Rolls can hold up to ',
            $script
        );
    }
}
