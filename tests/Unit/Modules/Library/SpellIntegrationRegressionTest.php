<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\CanonicalSpellReferenceResolver;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcanePantryPresenter;
use PHPUnit\Framework\TestCase;

final class SpellIntegrationRegressionTest extends TestCase
{
    public function testExplicitLegacyAliasesResolveToCanonicalSpellbookNames(): void
    {
        $catalogue = new ArcaneAbilityCatalogue();
        $resolver = new CanonicalSpellReferenceResolver();

        $expected = [
            'restorative-preserve' => 'Cure Meats',
            'market-missile' => 'Mystery Mustard Missile',
            'aisle-lightning' => 'Lightning Lemonade',
            'stockroom-fireball' => 'Flame-Grilled Fireball',
        ];

        foreach ($expected as $legacyId => $canonicalName) {
            $ability = $this->findAbility(
                $catalogue,
                $legacyId
            );

            $reference = $resolver->resolve($ability);

            self::assertSame('canonical', $reference['status']);
            self::assertSame($legacyId, $reference['stable_id']);
            self::assertSame($canonicalName, $reference['label']);
        }
    }

    public function testUnmatchedLegacyArcanaRemainUntouchedRatherThanGuessed(): void
    {
        $catalogue = new ArcaneAbilityCatalogue();
        $resolver = new CanonicalSpellReferenceResolver();

        $ability = $this->findAbility(
            $catalogue,
            'pantry-ward'
        );

        $reference = $resolver->resolve($ability);

        self::assertSame('unmatched', $reference['status']);
        self::assertNull($reference['canonical_key']);
        self::assertSame('Pantry Ward', $reference['label']);
    }

    public function testStableCharacterSpellbookIdsAreNotRewrittenByResolver(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/Services/CanonicalSpellReferenceResolver.php'
        );

        self::assertStringContainsString(
            "'stable_id' => \$ability->id()",
            $source
        );
        self::assertStringNotContainsString(
            '->learn(',
            $source
        );
        self::assertStringNotContainsString(
            'CharacterRepository',
            $source
        );
    }

    public function testArcanePantryUsesCanonicalDisplayReferenceWithoutChangingAbilityId(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/Services/ArcanePantryPresenter.php'
        );

        self::assertStringContainsString(
            'CanonicalSpellReferenceResolver',
            $source
        );
        self::assertStringContainsString(
            "'id' => \$ability->id()",
            $source
        );
        self::assertStringContainsString(
            "'canonical_spell_key' => \$reference['canonical_key']",
            $source
        );
        self::assertStringContainsString(
            "'label' => (string) \$reference['label']",
            $source
        );
    }

    public function testWizardSpellbookFolioUsesCanonicalPresentationButStableKeys(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/Folios/SpellbookFolio.php'
        );

        self::assertStringContainsString(
            "'key' => \$ability->id()",
            $source
        );
        self::assertStringContainsString(
            'CanonicalSpellReferenceResolver',
            $source
        );
        self::assertStringContainsString(
            "->resolve(\$ability)['label']",
            $source
        );
    }

    public function testCantripFolioUsesSameCompatibilityResolver(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/Folios/CantripFolio.php'
        );

        self::assertStringContainsString(
            'CanonicalSpellReferenceResolver',
            $source
        );
        self::assertStringContainsString(
            "'key' => \$ability->id()",
            $source
        );
    }

    public function testIntegrationDoesNotChangeSpellbookPersistenceValueObject(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Models/ValueObjects/Spellbook.php'
        );

        self::assertStringNotContainsString(
            'HandbookSpellRegister',
            $source
        );
        self::assertStringNotContainsString(
            'CanonicalSpellReferenceResolver',
            $source
        );
    }

    public function testIntegrationDoesNotChangeSpellSlotMechanics(): void
    {
        $source = $this->source(
            'app/Modules/Characters/ActivePlay/Services/SharedSpellSlotReserveService.php'
        );

        self::assertStringNotContainsString(
            'HandbookSpellRegister',
            $source
        );
        self::assertStringNotContainsString(
            'CanonicalSpellReferenceResolver',
            $source
        );
    }

    public function testAliasMapIsDeliberatelySmallAndExplicit(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Arcana/Services/CanonicalSpellReferenceResolver.php'
        );

        self::assertStringContainsString(
            "'restorative-preserve' => 'cure-meats'",
            $source
        );
        self::assertStringContainsString(
            "'market-missile' => 'mystery-mustard-missile'",
            $source
        );
        self::assertStringContainsString(
            "'aisle-lightning' => 'lightning-lemonade'",
            $source
        );
        self::assertStringContainsString(
            "'stockroom-fireball' => 'flame-grilled-fireball'",
            $source
        );
    }


    private function findAbility(
        ArcaneAbilityCatalogue $catalogue,
        string $id
    ): object {
        foreach ([
            'wizard',
            'sorcerer',
            'cleric',
            'druid',
            'bard',
            'artificer',
            'warlock',
            'paladin',
            'ranger',
        ] as $class) {
            foreach (
                $catalogue->forClass($class)
                as $ability
            ) {
                if ($ability->id() === $id) {
                    return $ability;
                }
            }
        }

        self::fail(
            sprintf(
                'Arcane ability "%s" was not found.',
                $id
            )
        );
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root() . '/' . $relative);
        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 4);
    }
}
