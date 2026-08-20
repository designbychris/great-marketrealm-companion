<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Cleric;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericDomainSpellCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\ClericDomainGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ClericSacredDomainsRegressionTest extends TestCase
{
    /** @return array<int,string> */
    private function domains(): array
    {
        return [
            'domain-of-sweetness',
            'domain-of-the-golden-arches',
            'domain-of-dairy',
            'domain-of-seasoning',
            'domain-of-cultivation',
            'domain-of-fermentation',
        ];
    }

    public function testAllSixDomainsAreCertified(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('cleric')
        );

        self::assertSame(
            $this->domains(),
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testEveryDomainNowHasCompleteClericMilestoneCadence(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ($this->domains() as $domain) {
            self::assertTrue(
                $catalogue->supports($domain)
            );

            self::assertSame(
                [1, 2, 6, 8, 17],
                array_column(
                    $catalogue->all($domain),
                    'level'
                )
            );
        }
    }

    public function testEveryDomainDefinitionSatisfiesSharedGiftContract(): void
    {
        $definitions =
            ClericDomainGiftProgression::allDefinitions();

        self::assertCount(
            6,
            $definitions
        );

        foreach ($definitions as $definition) {
            self::assertInstanceOf(
                PathGiftProgressionDefinitionInterface::class,
                $definition
            );

            self::assertNotSame(
                '',
                $definition->pathKey()
            );

            self::assertTrue(
                $definition->supports(
                    $definition->pathKey()
                )
            );

            self::assertCount(
                5,
                $definition->gifts()
            );
        }
    }

    public function testSweetnessPreservesItsSuppliedFeatureChain(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-sweetness'
        );

        self::assertSame(
            [
                'bonus-cantrips-and-sweet-sanctuary',
                'sugarburst',
                'sticky-ward',
                'sticky-smite',
                'ascension-of-the-sugarcloud',
            ],
            array_column(
                $gifts,
                'key'
            )
        );

        self::assertStringContainsString(
            '1d6 temporary hit points',
            $gifts[1]['detail']
        );

        self::assertStringContainsString(
            '2d8',
            $gifts[3]['detail']
        );

        self::assertStringContainsString(
            '60-foot flying speed',
            $gifts[4]['detail']
        );
    }

    public function testGoldenArchesNormalizesSparseOlderProgression(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-the-golden-arches'
        );

        self::assertSame(
            'Channel Divinity: Order Up',
            $gifts[1]['label']
        );

        self::assertTrue(
            $gifts[2]['editorial']
        );

        self::assertSame(
            'Express Blessing',
            $gifts[2]['label']
        );

        self::assertSame(
            'Divine Strike: Golden Fry',
            $gifts[3]['label']
        );

        self::assertStringContainsString(
            '1 minute',
            $gifts[4]['detail']
        );
    }

    public function testDairyCorrectsGreaseTerminologyAndMovesChannelFeatureToTwo(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-dairy'
        );

        self::assertStringContainsString(
            'Grease is a 1st-level spell, not a cantrip',
            $gifts[0]['detail']
        );

        self::assertSame(
            2,
            $gifts[1]['level']
        );

        self::assertSame(
            'Channel Divinity: Curdled Blessing',
            $gifts[1]['label']
        );
    }

    public function testDairyReceivesRestrainedLevelEightEditorialFeature(): void
    {
        $gift = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-dairy'
        )[3];

        self::assertSame(
            'Divine Strike: Cultured Smite',
            $gift['label']
        );

        self::assertTrue(
            $gift['editorial']
        );

        self::assertStringContainsString(
            'radiant or cold damage',
            $gift['detail']
        );
    }

    public function testSeasoningPreservesExistingOneTwoSixEightAndGetsCapstone(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-seasoning'
        );

        self::assertSame(
            [
                'flavourful-touch',
                'salt-the-earth',
                'searing-seasoning',
                'seasoned-divine-strike',
                'perfect-balance',
            ],
            array_column(
                $gifts,
                'key'
            )
        );

        self::assertStringContainsString(
            '+1 to attack and damage',
            $gifts[0]['detail']
        );

        self::assertStringContainsString(
            'ignores resistance',
            $gifts[4]['detail']
        );

        self::assertStringContainsString(
            'Immunity is not bypassed',
            $gifts[4]['detail']
        );
    }

    public function testCultivationIsExpandedWithoutBecomingFermentationClone(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-cultivation'
        );

        self::assertSame(
            [
                'cultivator-proficiencies',
                'blessed-brine',
                'patient-culture',
                'cultivated-potency',
                'sacred-vintage',
            ],
            array_column(
                $gifts,
                'key'
            )
        );

        self::assertStringContainsString(
            'Nature and brewer’s supplies',
            $gifts[0]['detail']
        );

        self::assertStringContainsString(
            'any Cleric cantrip',
            $gifts[3]['detail']
        );
    }

    public function testFermentationPreservesItsRichSuppliedProgression(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->all(
            'domain-of-fermentation'
        );

        self::assertSame(
            [
                'ferment-touch-and-proficiencies',
                'funk-of-the-divine',
                'spiritual-brine',
                'pickled-spirits',
                'mother-culture',
            ],
            array_column(
                $gifts,
                'key'
            )
        );

        self::assertStringContainsString(
            '4d8 at 17',
            $gifts[0]['detail']
        );

        self::assertStringContainsString(
            '2d10 + your Cleric level',
            $gifts[1]['detail']
        );

        self::assertStringContainsString(
            '2d6 hit points',
            $gifts[4]['detail']
        );

        self::assertStringContainsString(
            '4d6 radiant or poison damage',
            $gifts[4]['detail']
        );
    }

    public function testFiveCompleteSuppliedDomainSpellTablesRemainExact(): void
    {
        $catalogue =
            new ClericDomainSpellCatalogue();

        foreach ([
            'domain-of-sweetness',
            'domain-of-dairy',
            'domain-of-seasoning',
            'domain-of-cultivation',
            'domain-of-fermentation',
        ] as $domain) {
            self::assertSame(
                [1, 3, 5, 7, 9],
                array_column(
                    $catalogue->forDomain(
                        $domain
                    ),
                    'level'
                )
            );
        }
    }

    public function testGoldenArchesDoesNotInventMissingDomainSpellTable(): void
    {
        self::assertSame(
            [
                [
                    'level' => 1,
                    'spells' => ['Grease'],
                ],
                [
                    'level' => 5,
                    'spells' => [
                        'Create Food and Water',
                    ],
                ],
            ],
            (
                new ClericDomainSpellCatalogue()
            )->forDomain(
                'domain-of-the-golden-arches'
            )
        );
    }

    public function testDomainSpellsUnlockByClericLevel(): void
    {
        self::assertSame(
            [
                'Goodberry',
                'Faerie Fire',
                'Misty Step',
                'Enlarge/Reduce',
            ],
            (
                new ClericDomainSpellCatalogue()
            )->unlocked(
                'domain-of-fermentation',
                3
            )
        );
    }

    public function testDomainCandidatesNowCarryGuidanceAndGiftPreviews(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('cleric')
        );

        foreach ($candidates as $candidate) {
            self::assertNotSame(
                '',
                $candidate['identity']
            );

            self::assertNotSame(
                '',
                $candidate['playstyle']
            );

            self::assertNotSame(
                '',
                $candidate['best_for']
            );

            self::assertCount(
                4,
                $candidate['gift_preview']
            );
        }
    }

    public function testEditorialAdditionsRemainExplicitlyMarked(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $editorialCount = 0;

        foreach ($this->domains() as $domain) {
            foreach (
                $catalogue->all($domain)
                as $gift
            ) {
                if (
                    ! empty(
                        $gift['editorial']
                    )
                ) {
                    $editorialCount++;
                }
            }
        }

        self::assertGreaterThanOrEqual(
            8,
            $editorialCount
        );
    }

    public function testUnknownDomainFactoryIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ClericDomainGiftProgression::forDomain(
            'domain-of-mystery-gravy'
        );
    }
}
