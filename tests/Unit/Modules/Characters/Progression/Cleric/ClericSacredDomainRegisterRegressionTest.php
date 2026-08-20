<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Cleric;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericSacredDomainRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericSacredPolicy;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ClericSacredDomainRegisterRegressionTest extends TestCase
{
    public function testForeignCallingIsUnsupported(): void
    {
        self::assertFalse(
            (
                new ClericSacredDomainRegisterPresenter()
            )->present(
                $this->character(
                    'wizard',
                    1,
                    ''
                )
            )['supported']
        );
    }

    public function testLevelOneClericAlreadyShowsPreparedSpellcasting(): void
    {
        $register = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(1)
        );

        self::assertTrue(
            $register['supported']
        );

        self::assertTrue(
            $register[
                'spellcasting'
            ]['unlocked']
        );

        self::assertSame(
            'prepared-spells',
            $register[
                'spellcasting'
            ]['model']
        );

        self::assertSame(
            1,
            $register[
                'spellcasting'
            ]['maximum_spell_level']
        );

        self::assertSame(
            3,
            $register[
                'spellcasting'
            ]['cantrips_known']
        );
    }

    public function testLevelOneClericHasFirstCircleSpellSlots(): void
    {
        $slots = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(1)
        )['spellcasting']['slots'];

        self::assertNotSame(
            [],
            $slots
        );

        self::assertSame(
            1,
            $slots[0]['level']
        );

        self::assertSame(
            2,
            $slots[0]['total']
        );

        self::assertSame(
            2,
            $slots[0]['remaining']
        );
    }

    public function testLevelOneDomainSelectionIsAlreadyAvailable(): void
    {
        $domain = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(1)
        )['domain'];

        self::assertSame(
            1,
            $domain['selection_level']
        );

        self::assertFalse(
            $domain['chosen']
        );

        self::assertSame(
            'Domain not yet chosen',
            $domain['label']
        );
    }

    public function testSixExistingDomainsAreVisibleInRegister(): void
    {
        $domain = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(1)
        )['domain'];

        self::assertSame(
            6,
            $domain['candidate_count']
        );

        self::assertSame(
            [
                'domain-of-sweetness',
                'domain-of-the-golden-arches',
                'domain-of-dairy',
                'domain-of-seasoning',
                'domain-of-cultivation',
                'domain-of-fermentation',
            ],
            array_column(
                $domain['candidates'],
                'key'
            )
        );
    }

    public function testChosenDomainUsesCatalogueLabel(): void
    {
        $domain = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(
                1,
                'domain-of-fermentation'
            )
        )['domain'];

        self::assertTrue(
            $domain['chosen']
        );

        self::assertSame(
            'Domain of Fermentation',
            $domain['label']
        );
    }

    public function testDomainGiftBoundaryRemainsVisibleUntilDedicatedPhase(): void
    {
        $domain = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(
                1,
                'domain-of-fermentation'
            )
        )['domain'];

        self::assertSame(
            0,
            $domain['gift_count']
        );

        self::assertSame(
            'Domain Gifts await their dedicated phase',
            $domain['gift_status']
        );
    }

    public function testLevelOneChannelDivinityIsCorrectlyUpcoming(): void
    {
        $channel = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(1)
        )['channel_divinity'];

        self::assertFalse(
            $channel['unlocked']
        );

        self::assertSame(
            0,
            $channel['maximum']
        );

        self::assertSame(
            2,
            $channel['next_improvement_level']
        );
    }

    public function testChannelDivinityUsesScaleAtTwoSixEighteen(): void
    {
        $policy =
            new ClericSacredPolicy();

        self::assertSame(
            1,
            $policy->channelDivinityMaximum(
                $this->cleric(2)
            )
        );

        self::assertSame(
            2,
            $policy->channelDivinityMaximum(
                $this->cleric(6)
            )
        );

        self::assertSame(
            3,
            $policy->channelDivinityMaximum(
                $this->cleric(18)
            )
        );
    }

    public function testDestroyUndeadThresholdsAppearAtCorrectLevels(): void
    {
        $policy =
            new ClericSacredPolicy();

        self::assertNull(
            $policy->destroyUndeadThreshold(
                $this->cleric(4)
            )
        );

        self::assertSame(
            'CR 1/2',
            $policy->destroyUndeadThreshold(
                $this->cleric(5)
            )
        );

        self::assertSame(
            'CR 4',
            $policy->destroyUndeadThreshold(
                $this->cleric(17)
            )
        );
    }

    public function testPreparedSpellMaximumUsesLevelPlusWisdomModifier(): void
    {
        self::assertSame(
            6,
            (
                new ClericSacredPolicy()
            )->preparedSpellMaximum(
                $this->cleric(
                    3,
                    '',
                    16
                )
            )
        );
    }

    public function testPreparedSpellMaximumNeverFallsBelowOne(): void
    {
        self::assertSame(
            1,
            (
                new ClericSacredPolicy()
            )->preparedSpellMaximum(
                $this->cleric(
                    1,
                    '',
                    6
                )
            )
        );
    }

    public function testSpellSaveAndAttackUseWisdom(): void
    {
        $cleric = $this->cleric(
            5,
            '',
            16
        );

        $policy =
            new ClericSacredPolicy();

        $wisdom = $cleric
            ->abilityScores()
            ->wisdom()
            ->modifier();

        $proficiency = $cleric
            ->proficiencyBonus()
            ->value();

        self::assertSame(
            8 + $proficiency + $wisdom,
            $policy->spellSaveDc($cleric)
        );

        self::assertSame(
            $proficiency + $wisdom,
            $policy->spellAttackBonus($cleric)
        );
    }

    public function testRegisterCarriesSharedSpellSlotExpenditure(): void
    {
        $slots = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(3),
            ActiveClassResourceState::fromArray([
                'spell-slot-1' => 1,
            ])
        )['spellcasting']['slots'];

        $first = array_values(
            array_filter(
                $slots,
                static fn (
                    array $slot
                ): bool =>
                    (int) (
                        $slot['level']
                        ?? 0
                    ) === 1
            )
        )[0];

        self::assertSame(
            1,
            $first['expended']
        );

        self::assertSame(
            $first['total'] - 1,
            $first['remaining']
        );
    }

    public function testLevelOneNextMilestoneIsChannelDivinity(): void
    {
        $milestone = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $this->cleric(1)
        )['next_milestone'];

        self::assertSame(
            2,
            $milestone['level']
        );

        self::assertSame(
            'Channel Divinity & Turn Undead',
            $milestone['label']
        );
    }

    public function testControllerSuppliesDomainRegisterToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'ClericSacredDomainRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'domainRegister' => \$domainRegister",
            $controller
        );
    }

    public function testLedgerRendersSacredDomainRegisterAndLevelOneGuidance(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'The Cleric’s Sacred Domain Register',
            $view
        );

        self::assertStringContainsString(
            'data-domain-register',
            $view
        );

        self::assertStringContainsString(
            'Channel Divinity',
            $view
        );

        self::assertStringContainsString(
            'awakens at Level 2',
            $view
        );
    }

    public function testSacredDomainRegisterIsReadOnlyAndResponsive(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringNotContainsString(
            'data-channel-divinity-spend',
            $view
        );

        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-domain-register',
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

    public function testSacredPolicyRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new ClericSacredPolicy()
        )->channelDivinityMaximum(
            $this->character(
                'wizard',
                5,
                ''
            )
        );
    }

    private function cleric(
        int $level,
        string $domain = '',
        int $wisdom = 10
    ): Character {
        return $this->character(
            'cleric',
            $level,
            $domain,
            $wisdom
        );
    }

    private function character(
        string $class,
        int $level,
        string $domain,
        int $wisdom = 10
    ): Character {
        $scores = AbilityScores::average()
            ->withWisdom(
                AbilityScore::fromInt(
                    $wisdom
                )
            );

        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Sacred Register Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            $scores,
            callingPath:
                CallingPath::fromString(
                    $domain
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
