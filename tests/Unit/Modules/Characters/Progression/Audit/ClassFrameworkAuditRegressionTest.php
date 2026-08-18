<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Audit;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassFrameworkAudit;
use PHPUnit\Framework\TestCase;

final class ClassFrameworkAuditRegressionTest extends TestCase
{
    public function testAuditIncludesEveryRegisteredCalling(): void
    {
        $report = (new ClassFrameworkAudit())
            ->report();

        self::assertSame(
            count(CharacterClass::identifiers()),
            $report['registered']
        );

        self::assertCount(
            count(CharacterClass::identifiers()),
            $report['classes']
        );
    }

    public function testWizardIsCurrentlySpecialistCalling(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('wizard')
            );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );

        self::assertTrue(
            $profile->hasSpecialistAdvancement()
        );

        self::assertTrue(
            $profile->hasSpellcastingProgression()
        );

        self::assertTrue(
            $profile->hasCallingPathProgression()
        );
    }

    public function testFighterIsCurrentlyFoundationCalling(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('fighter')
            );

        self::assertSame(
            ClassCapabilityProfile::FOUNDATION,
            $profile->implementationState()
        );

        self::assertSame(
            'registered',
            $profile->advancementStatus()
        );

        self::assertFalse(
            $profile->hasSpellcastingProgression()
        );

        self::assertFalse(
            $profile->hasCallingPathProgression()
        );
    }

    public function testGreatMarketrealmCallingsAreAuditedWithoutInventingProgression(): void
    {
        $catalogue = new ClassCapabilityCatalogue();

        foreach ([
            'grocer',
            'cleaver-saint',
        ] as $calling) {
            $profile = $catalogue->forClass(
                CharacterClass::fromString(
                    $calling
                )
            );

            self::assertSame(
                'registered',
                $profile->advancementStatus()
            );

            self::assertSame(
                ClassCapabilityProfile::FOUNDATION,
                $profile->implementationState()
            );
        }
    }

    public function testAuditCurrentlyFindsOneSpecialistCalling(): void
    {
        $catalogue = new ClassCapabilityCatalogue();

        self::assertCount(
            1,
            $catalogue->specialist()
        );

        self::assertSame(
            'wizard',
            $catalogue
                ->specialist()[0]
                ->class()
                ->value()
        );
    }

    public function testRemainingRegisteredCallingsStayVisibleAsFoundationWork(): void
    {
        $catalogue = new ClassCapabilityCatalogue();

        self::assertCount(
            count(CharacterClass::identifiers()) - 1,
            $catalogue->foundation()
        );
    }

    public function testProfileSerialisesImplementationCapabilities(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('wizard')
            )
            ->toArray();

        self::assertSame(
            'wizard',
            $profile['class']
        );

        self::assertSame(
            'Wizard',
            $profile['label']
        );

        self::assertArrayHasKey(
            'advancement',
            $profile
        );

        self::assertArrayHasKey(
            'spellcasting',
            $profile
        );

        self::assertArrayHasKey(
            'calling_path',
            $profile
        );

        self::assertArrayHasKey(
            'implementation_state',
            $profile
        );
    }

    public function testAuditDerivesCapabilitiesFromExistingCatalogues(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Progression/Audit/'
            . 'ClassCapabilityCatalogue.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'ClassProgressionCatalogue',
            $source
        );

        self::assertStringContainsString(
            'SpellcastingProgressionCatalogue',
            $source
        );

        self::assertStringContainsString(
            'PathProgressionCatalogue',
            $source
        );

        self::assertStringNotContainsString(
            "class->value() === 'wizard'",
            $source
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
