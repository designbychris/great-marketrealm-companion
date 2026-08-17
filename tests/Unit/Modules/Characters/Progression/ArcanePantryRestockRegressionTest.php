<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use PHPUnit\Framework\TestCase;

final class ArcanePantryRestockRegressionTest extends TestCase
{
    public function testRestockedWizardCatalogueContainsThirdCircleChoices(): void
    {
        $catalogue = new ArcaneAbilityCatalogue();
        $thirdCircle = [];

        foreach ($catalogue->forClass('wizard') as $ability) {
            if (
                $ability->kind() === 'spell'
                && $ability->spellLevel() === 3
            ) {
                $thirdCircle[$ability->id()] = $ability;
            }
        }

        self::assertCount(8, $thirdCircle);
        self::assertArrayHasKey(
            'aisle-lightning',
            $thirdCircle
        );
        self::assertArrayHasKey(
            'stockroom-fireball',
            $thirdCircle
        );
        self::assertArrayHasKey(
            'closing-time-haste',
            $thirdCircle
        );
        self::assertArrayHasKey(
            'pallet-wall',
            $thirdCircle
        );
        self::assertSame(
            '3rd-level slot',
            $thirdCircle['stockroom-fireball']->uses()
        );
        self::assertSame(
            '8d6',
            $thirdCircle['stockroom-fireball']->formula()
        );
    }

    public function testRestockDoesNotWeakenChooseNProgressionRule(): void
    {
        $root = dirname(__DIR__, 5);
        $definition = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Spellcasting/Definitions/'
            . 'WizardSpellcastingProgression.php'
        );
        $resolver = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Services/'
            . 'AdvancementChoiceRequirementResolver.php'
        );

        self::assertIsString($definition);
        self::assertIsString($resolver);
        self::assertStringContainsString(
            "'spells_learned' => 2",
            $definition
        );
        self::assertStringContainsString(
            'Do not silently weaken a progression rule',
            $resolver
        );
    }
}
