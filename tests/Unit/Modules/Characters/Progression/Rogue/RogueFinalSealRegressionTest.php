<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Rogue;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RoguePrecisionPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RoguePrecisionReactionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\RogueProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RogueFinalSealRegressionTest extends TestCase
{
    public function testRogueRemainsSpecialistWithoutBaselineSpellcasting(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('rogue')
            );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );

        self::assertTrue(
            $profile->hasSpecialistAdvancement()
        );

        self::assertTrue(
            $profile->hasCallingPathProgression()
        );

        self::assertFalse(
            $profile->hasSpellcastingProgression()
        );
    }

    public function testCoreRogueMilestonesRemainStable(): void
    {
        $progression = new RogueProgression();
        $rogue = CharacterClass::fromString('rogue');

        $expected = [
            2 => ['cunning-action'],
            5 => ['sneak-attack', 'uncanny-dodge'],
            6 => ['expertise'],
            7 => ['sneak-attack', 'evasion'],
            11 => ['sneak-attack', 'reliable-talent'],
            14 => ['blindsense'],
            15 => ['sneak-attack', 'slippery-mind'],
            18 => ['elusive'],
            20 => ['stroke-of-luck'],
        ];

        foreach ($expected as $level => $keys) {
            self::assertSame(
                $keys,
                array_column(
                    $progression
                        ->forLevel(
                            $rogue,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthAndArchetypeDelegationsRemainSeparated(): void
    {
        $progression = new RogueProgression();
        $rogue = CharacterClass::fromString('rogue');

        foreach ([4, 8, 10, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $rogue,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }

        foreach ([3, 9, 13, 17] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $rogue,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testLevelThreeOwnsArchetypeAndFirstGift(): void
    {
        $entry = (new RogueProgression())
            ->forLevel(
                CharacterClass::fromString('rogue'),
                3
            );

        self::assertSame(
            ['path', 'path-gifts'],
            array_column(
                $entry['delegated'],
                'folio'
            )
        );

        $path = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('rogue')
        );

        self::assertIsArray($path);

        self::assertSame(
            3,
            $path['selection_level']
        );
    }

    public function testAllSixArchetypesKeepFourGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->paths() as $path) {
            self::assertTrue(
                $catalogue->supports($path)
            );

            self::assertSame(
                [3, 9, 13, 17],
                array_column(
                    $catalogue->all($path),
                    'level'
                )
            );
        }
    }

    public function testEveryArchetypeKeepsChoiceGuidance(): void
    {
        $choices = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('rogue')
        );

        self::assertCount(6, $choices);

        foreach ($choices as $choice) {
            self::assertNotSame(
                '',
                $choice['identity']
            );

            self::assertNotSame(
                '',
                $choice['playstyle']
            );

            self::assertNotSame(
                '',
                $choice['best_for']
            );

            self::assertCount(
                4,
                $choice['gift_preview']
            );
        }
    }

    public function testSneakAttackHasSingleCertifiedScalingAuthority(): void
    {
        $policy = new RoguePrecisionPolicy();

        self::assertSame(
            '1d6',
            $policy->sneakAttackDice(
                $this->rogue(1)
            )
        );

        self::assertSame(
            '3d6',
            $policy->sneakAttackDice(
                $this->rogue(5)
            )
        );

        self::assertSame(
            '10d6',
            $policy->sneakAttackDice(
                $this->rogue(20)
            )
        );

        self::assertStringNotContainsString(
            'private function sneakAttackDice',
            $this->source(
                'app/Modules/Characters/Progression/'
                . 'Cunning/Services/'
                . 'RogueCunningRegisterPresenter.php'
            )
        );

        self::assertStringNotContainsString(
            'ceil(',
            $this->source(
                'app/Modules/Characters/Progression/'
                . 'Cunning/Services/'
                . 'RoguePrecisionReactionPresenter.php'
            )
        );
    }

    public function testPrecisionPolicyRejectsAnotherCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new RoguePrecisionPolicy())
            ->sneakAttackDice(
                $this->character('fighter', 5)
            );
    }

    public function testCunningActionRemainsEveryTurnNotFiniteResource(): void
    {
        $state = (
            new RogueCunningActionPresenter()
        )->present(
            $this->rogue(5)
        );

        self::assertSame(
            'Bonus action',
            $state['cost']
        );

        self::assertSame(
            'Every turn',
            $state['refresh']
        );

        foreach ($state['actions'] as $action) {
            self::assertArrayNotHasKey(
                'remaining',
                $action
            );

            self::assertArrayNotHasKey(
                'uses',
                $action
            );
        }
    }

    public function testHideStillUsesRealCharacterStealth(): void
    {
        $rogue = $this->rogue(5);

        $hide = (
            new RogueCunningActionPresenter()
        )->present($rogue)['actions'][2];

        self::assertSame(
            $rogue
                ->skills()
                ->stealth()
                ->modifier(),
            $hide['roll']['modifier']
        );

        self::assertSame(
            'Dexterity (Stealth) check',
            $hide['roll']['result_suffix']
        );
    }

    public function testPrecisionAndReactionUnlocksRemainStable(): void
    {
        $presenter =
            new RoguePrecisionReactionPresenter();

        self::assertFalse(
            $presenter
                ->present(
                    $this->rogue(4)
                )['uncanny_dodge']['unlocked']
        );

        self::assertTrue(
            $presenter
                ->present(
                    $this->rogue(5)
                )['uncanny_dodge']['unlocked']
        );

        self::assertFalse(
            $presenter
                ->present(
                    $this->rogue(6)
                )['evasion']['unlocked']
        );

        self::assertTrue(
            $presenter
                ->present(
                    $this->rogue(7)
                )['evasion']['unlocked']
        );
    }

    public function testSneakAttackKeepsQualificationAtTheTable(): void
    {
        $state = (
            new RoguePrecisionReactionPresenter()
        )->present(
            $this->rogue(5)
        )['sneak_attack'];

        self::assertSame(
            'Once per turn',
            $state['frequency']
        );

        self::assertStringContainsString(
            'does not decide',
            $state['qualification'][1]
        );

        self::assertArrayNotHasKey(
            'qualifies',
            $state
        );
    }

    public function testCunningRegisterShowsOnlyPersistedArchetypeGifts(): void
    {
        $state = (
            new RogueCunningRegisterPresenter()
        )->present(
            $this->rogue(
                13,
                'mastermind-of-the-aisles',
                [
                    'aisle-scheme',
                    'planned-distraction',
                ]
            )
        );

        self::assertSame(
            [
                'aisle-scheme',
                'planned-distraction',
            ],
            array_column(
                $state['archetype']['gifts'],
                'key'
            )
        );

        self::assertNotContains(
            'three-aisles-ahead',
            array_column(
                $state['archetype']['gifts'],
                'key'
            )
        );
    }

    public function testRogueSystemsRemainIsolatedFromFighter(): void
    {
        $fighter = $this->character(
            'fighter',
            7
        );

        self::assertFalse(
            (
                new RogueCunningRegisterPresenter()
            )->present(
                $fighter
            )['supported']
        );

        self::assertFalse(
            (
                new RogueCunningActionPresenter()
            )->present(
                $fighter
            )['supported']
        );

        self::assertFalse(
            (
                new RoguePrecisionReactionPresenter()
            )->present(
                $fighter
            )['supported']
        );
    }

    public function testTurnStateRemainsBrowserLocalAndExplicitlyResettable(): void
    {
        $script = $this->source(
            'assets/js/modules/characters/'
            . 'rogue-precision-reactions.js'
        );

        self::assertStringContainsString(
            '[data-rogue-new-turn]',
            $script
        );

        self::assertStringContainsString(
            'setSneakUsed(false)',
            $script
        );

        self::assertStringNotContainsString(
            'fetch(',
            $script
        );

        self::assertStringNotContainsString(
            'localStorage',
            $script
        );
    }

    public function testCunningDeclarationsRemainBrowserLocal(): void
    {
        $script = $this->source(
            'assets/js/modules/characters/'
            . 'rogue-cunning-actions.js'
        );

        self::assertStringContainsString(
            '[data-cunning-declare]',
            $script
        );

        self::assertStringNotContainsString(
            'fetch(',
            $script
        );

        self::assertStringNotContainsString(
            'localStorage',
            $script
        );
    }

    public function testRogueDiceBackedActionsKeepSharedDiceworksContracts(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-sneak-attack-roll',
            $view
        );

        self::assertStringContainsString(
            'data-guild-roll="damage"',
            $view
        );

        self::assertStringContainsString(
            'Roll Hide',
            $view
        );

        self::assertStringContainsString(
            'gmrc-guild-roll-trigger',
            $view
        );
    }

    public function testRogueScriptsRemainEnqueuedAfterGuildDice(): void
    {
        $provider = $this->source(
            'app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            "'gmrc-rogue-cunning-actions'",
            $provider
        );

        self::assertStringContainsString(
            "'gmrc-rogue-precision-reactions'",
            $provider
        );

        self::assertGreaterThanOrEqual(
            2,
            substr_count(
                $provider,
                "['gmrc-guild-dice']"
            )
        );
    }

    public function testFinalRogueSurfaceKeepsAccessibilityBoundaries(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-cunning-register-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-cunning-actions-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-rogue-precision-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-live="polite"',
            $view
        );

        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            ':focus-visible',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    /**
     * @return array<int,string>
     */
    private function paths(): array
    {
        return [
            'the-cheetoblade',
            'spiceblade',
            'the-breadknife',
            'mastermind-of-the-aisles',
            'aisle-stalker',
            'taffy-trickster',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function rogue(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Final Seal Rogue'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString('rogue'),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $path
                ),
            pathGifts:
                PathGifts::fromArray(
                    $gifts
                )
        );
    }

    private function character(
        string $class,
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Rogue Isolation Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average()
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
