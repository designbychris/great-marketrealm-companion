<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Bard;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardCollegeGiftLedgerPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardCollegeRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardPerformancePolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\BardProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\BardSpellcastingProgression;
use PHPUnit\Framework\TestCase;

final class BardFinalCertificationRegressionTest extends TestCase
{
    public function testBardRemainsSpecialistSpellcastingPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('bard')
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

    public function testBardFoundationsRemainSpellcastingAndInspiration(): void
    {
        self::assertSame(
            ['spellcasting', 'bardic-inspiration'],
            array_column(
                (
                    new BardProgression()
                )->foundations(
                    CharacterClass::fromString('bard')
                ),
                'key'
            )
        );
    }

    public function testCollegeSelectionAndSevenCanonCollegesRemainSealed(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('bard')
        );

        self::assertIsArray($definition);
        self::assertSame(3, $definition['selection_level']);
        self::assertSame('Bard College', $definition['label']);

        self::assertSame(
            $this->colleges(),
            array_column(
                (
                    new PathCandidateCatalogue()
                )->forClass(
                    CharacterClass::fromString('bard')
                ),
                'key'
            )
        );
    }

    public function testCollegeGiftCadencesRemainSourceFaithful(): void
    {
        $catalogue = new PathGiftCatalogue();

        $expected = [
            'college-of-the-seasoned-song' => [3, 3, 6, 14],
            'college-of-nostalgia' => [3, 3, 6],
            'college-of-preservation' => [3, 3, 6, 14],
            'charcutaire' => [3, 3, 6, 14],
            'college-of-culinary-crescendo' => [3, 3, 6, 14],
            'college-of-confection' => [3, 6, 14],
            'college-of-churned-verse' => [3, 3, 6, 14],
        ];

        foreach ($expected as $college => $levels) {
            self::assertTrue(
                $catalogue->supports($college)
            );
            self::assertSame(
                $levels,
                array_column(
                    $catalogue->all($college),
                    'level'
                )
            );
        }
    }

    public function testBardCollegeCatalogueRetainsTwentySixUniqueGifts(): void
    {
        $catalogue = new PathGiftCatalogue();
        $keys = [];

        foreach ($this->colleges() as $college) {
            foreach ($catalogue->all($college) as $gift) {
                $keys[] = (string) ($gift['key'] ?? '');
            }
        }

        self::assertCount(26, $keys);
        self::assertCount(26, array_unique($keys));
    }

    public function testBardRemainsKnownSpellCharismaFullCaster(): void
    {
        $entry = (
            new BardSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('bard'),
            10
        );

        self::assertSame('known-spells', $entry['model']);
        self::assertSame(14, $entry['spells_known']);
        self::assertSame(4, $entry['cantrips_known']);
        self::assertSame(5, $entry['maximum_spell_level']);

        self::assertSame(
            9,
            (
                new BardSpellcastingProgression()
            )->forLevel(
                CharacterClass::fromString('bard'),
                17
            )['maximum_spell_level']
        );
    }

    public function testBardicInspirationThresholdsRemainD6D8D10D12(): void
    {
        $policy = new BardPerformancePolicy();

        self::assertSame('d6', $policy->inspirationDie($this->bard(4)));
        self::assertSame('d8', $policy->inspirationDie($this->bard(5)));
        self::assertSame('d10', $policy->inspirationDie($this->bard(10)));
        self::assertSame('d12', $policy->inspirationDie($this->bard(15)));
        self::assertSame(
            'long-rest',
            $policy->inspirationRefresh($this->bard(4))
        );
        self::assertSame(
            'short-or-long-rest',
            $policy->inspirationRefresh($this->bard(5))
        );
    }

    public function testSongOfRestThresholdsRemainD6D8D10D12(): void
    {
        $policy = new BardPerformancePolicy();

        self::assertNull(
            $policy->songOfRestDie($this->bard(1))
        );
        self::assertSame('d6', $policy->songOfRestDie($this->bard(2)));
        self::assertSame('d8', $policy->songOfRestDie($this->bard(9)));
        self::assertSame('d10', $policy->songOfRestDie($this->bard(13)));
        self::assertSame('d12', $policy->songOfRestDie($this->bard(17)));
    }

    public function testSeasonedSongStillAdvertisesItsLevelFourteenCollegeGift(): void
    {
        $register = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(
                13,
                'college-of-the-seasoned-song'
            )
        );

        self::assertSame(
            14,
            $register['next_milestone']['level']
        );
        self::assertSame(
            'Final College Gift & Magical Secrets',
            $register['next_milestone']['label']
        );
    }

    public function testNostalgiaDoesNotInventALevelFourteenCollegeGift(): void
    {
        $register = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(
                13,
                'college-of-nostalgia'
            )
        );

        self::assertSame(
            14,
            $register['next_milestone']['level']
        );
        self::assertSame(
            'Magical Secrets',
            $register['next_milestone']['label']
        );
        self::assertStringContainsString(
            'no additional supplied Level 14 Gift',
            $register['next_milestone']['detail']
        );

        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                13,
                'college-of-nostalgia'
            )
        );

        self::assertTrue($ledger['complete']);
        self::assertNull($ledger['next_level']);
        self::assertSame([], $ledger['next_gifts']);
    }

    public function testLivingLedgerAndControllerKeepBardSurfacesTogether(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'BardCollegeRegisterPresenter',
            $controller
        );
        self::assertStringContainsString(
            'BardCollegeGiftLedgerPresenter',
            $controller
        );
        self::assertStringContainsString(
            "'collegeRegister' => \$collegeRegister",
            $controller
        );
        self::assertStringContainsString(
            "'collegeGifts' => \$collegeGifts",
            $controller
        );
        self::assertStringContainsString(
            'data-college-register',
            $view
        );
        self::assertStringContainsString(
            'data-bard-college-gifts',
            $view
        );
    }

    public function testFinalBardLedgerHardeningRemainsResponsiveAndAccessible(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/arcane-pantry.css'
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
        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-college-gifts__grid',
            $css
        );
    }

    /** @return array<int,string> */
    private function colleges(): array
    {
        return [
            'college-of-the-seasoned-song',
            'college-of-nostalgia',
            'college-of-preservation',
            'charcutaire',
            'college-of-culinary-crescendo',
            'college-of-confection',
            'college-of-churned-verse',
        ];
    }

    private function bard(
        int $level,
        string $college = ''
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Final Bard Certification Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString('bard'),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $college
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
