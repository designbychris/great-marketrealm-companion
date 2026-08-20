<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Artificer;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services\ArtificerSpecialisationRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\ArtificerSpecialisationProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ArtificerSpecialisationRegisterRegressionTest extends TestCase
{
    public function testSpecialisationSelectionBeginsAtLevelThree(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('artificer')
        );

        self::assertIsArray($definition);
        self::assertSame(
            'Artificer Specialisation',
            $definition['label']
        );
        self::assertSame(
            'Specialist Workshop Folio',
            $definition['folio_label']
        );
        self::assertSame(
            'artificer-specialisation',
            $definition['choice_key']
        );
        self::assertSame(
            3,
            $definition['selection_level']
        );
    }

    public function testFourCanonicalMarketrealmSpecialisationsAreLegalCandidates(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('artificer')
        );

        self::assertCount(4, $candidates);

        self::assertSame(
            [
                'the-spice-engineer',
                'the-cheesemonger',
                'the-sous-sorcerer',
                'the-culinary-engineer',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testSpecialisationGiftsAreCertifiedByPhaseThirteenB(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ([
            'the-spice-engineer',
            'the-cheesemonger',
            'the-sous-sorcerer',
            'the-culinary-engineer',
        ] as $specialisation) {
            self::assertTrue(
                $catalogue->supports(
                    $specialisation
                )
            );

            self::assertNotSame(
                [],
                $catalogue->all(
                    $specialisation
                )
            );
        }
    }

    public function testForeignCallingIsUnsupportedByRegister(): void
    {
        self::assertFalse(
            (
                new ArtificerSpecialisationRegisterPresenter()
            )->present(
                $this->character(
                    'wizard',
                    3,
                    ''
                )
            )['supported']
        );
    }

    public function testLevelTwoShowsUpcomingSpecialisationAndActiveWorkshop(): void
    {
        $register = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $this->artificer(2)
        );

        self::assertTrue(
            $register['supported']
        );

        self::assertFalse(
            $register[
                'specialisation'
            ]['available']
        );

        self::assertSame(
            4,
            $register[
                'specialisation'
            ]['candidate_count']
        );

        self::assertTrue(
            $register[
                'workshop'
            ]['infusions_unlocked']
        );

        self::assertSame(
            2,
            $register[
                'spellcasting'
            ]['cantrips_known']
        );

        self::assertSame(
            1,
            $register[
                'spellcasting'
            ]['maximum_spell_level']
        );
    }

    public function testChosenSpecialisationUsesCanonicalCatalogueLabel(): void
    {
        $specialisation = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $this->artificer(
                3,
                'the-spice-engineer'
            )
        )['specialisation'];

        self::assertTrue(
            $specialisation['available']
        );

        self::assertTrue(
            $specialisation['chosen']
        );

        self::assertSame(
            'The Spice Engineer',
            $specialisation['label']
        );
    }

    public function testRegisterReflectsCertifiedThirteenBGifts(): void
    {
        $specialisation = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $this->artificer(
                3,
                'the-culinary-engineer'
            )
        )['specialisation'];

        self::assertSame(
            5,
            $specialisation['gift_count']
        );

        self::assertSame(
            'Specialisation Gifts certified',
            $specialisation['gift_status']
        );
    }

    public function testLevelTwoNextMilestoneIsSpecialisationSelection(): void
    {
        $milestone = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $this->artificer(2)
        )['next_milestone'];

        self::assertSame(
            3,
            $milestone['level']
        );

        self::assertSame(
            'Artificer Specialisation & The Right Tool for the Job',
            $milestone['label']
        );
    }

    public function testLevelThreeNextMilestoneIsSpecialisationGift(): void
    {
        $milestone = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $this->artificer(
                3,
                'the-cheesemonger'
            )
        )['next_milestone'];

        self::assertSame(
            5,
            $milestone['level']
        );

        self::assertSame(
            'Specialisation Gift',
            $milestone['label']
        );
    }

    public function testControllerSuppliesArtificerRegisterToLivingLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'ArtificerSpecialisationRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'artificerRegister' => \$artificerRegister",
            $controller
        );
    }

    public function testLedgerRendersReadOnlySpecialisationRegister(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'The Artificer’s Specialisation Register',
            $view
        );

        self::assertStringContainsString(
            'data-artificer-register',
            $view
        );

        self::assertStringContainsString(
            'Specialisation selection opens at Level 3',
            $view
        );

        self::assertStringNotContainsString(
            'data-artificer-specialisation-spend',
            $view
        );
    }

    public function testRegisterPresentationIsResponsiveAndAccessible(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-artificer-register',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 460px)',
            $css
        );

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testSpecialisationProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new ArtificerSpecialisationProgression()
        )->definition(
            CharacterClass::fromString(
                'bard'
            )
        );
    }

    private function artificer(
        int $level,
        string $specialisation = ''
    ): Character {
        return $this->character(
            'artificer',
            $level,
            $specialisation
        );
    }

    private function character(
        string $class,
        int $level,
        string $specialisation
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Workshop Register Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $specialisation
                )
        );
    }

    private function source(
        string $relative
    ): string {
        $source = file_get_contents(
            $this->root()
            . '/'
            . $relative
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}
