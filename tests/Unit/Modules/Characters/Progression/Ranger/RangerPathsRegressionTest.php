<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Ranger;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class RangerPathsRegressionTest extends TestCase
{
    /** @return array<string,string> */
    private function expected(): array
    {
        return [
            'aislewarden-conclave' => 'Aislewarden Conclave',
            'deep-root-warden' => 'Deep-Root Warden',
            'cold-vault-stalker' => 'Cold Vault Stalker',
            'conclave-of-the-forager' => 'Conclave of the Forager',
            'spice-trail-hunter' => 'Spice Trail Hunter',
            'rindrunner' => 'Rindrunner',
            'seedshot-conclave' => 'Seedshot Conclave',
            'expiry-hunter' => 'Expiry Hunter',
        ];
    }

    public function testCatalogueContainsExactlyEightRangerPaths(): void
    {
        $paths = array_values(array_filter(
            (new CharacterCatalogueRepository())->subclasses(),
            static fn (array $item): bool =>
                ($item['parent'] ?? '') === 'ranger'
        ));

        self::assertCount(8, $paths);

        self::assertSame(
            array_keys($this->expected()),
            array_column($paths, 'key')
        );

        self::assertSame(
            array_values($this->expected()),
            array_column($paths, 'name')
        );
    }

    public function testRangerPathChoiceBeginsAtLevelThree(): void
    {
        $definition = (new PathProgressionCatalogue())->forClass(
            CharacterClass::fromString('ranger')
        );

        self::assertIsArray($definition);
        self::assertSame('Ranger Path', $definition['label']);
        self::assertSame('Field Path Folio', $definition['folio_label']);
        self::assertSame('ranger-path', $definition['choice_key']);
        self::assertSame(3, $definition['selection_level']);
    }

    public function testEveryRangerPathHasPlayerFacingGuidance(): void
    {
        $candidates = (new PathCandidateCatalogue())->forClass(
            CharacterClass::fromString('ranger')
        );

        self::assertCount(8, $candidates);

        foreach ($candidates as $candidate) {
            self::assertNotSame('', $candidate['detail']);
            self::assertNotSame('', $candidate['identity']);
            self::assertNotSame('', $candidate['playstyle']);
            self::assertNotSame('', $candidate['best_for']);
        }
    }

    public function testEveryRangerPathHasFourCanonFeatureMilestones(): void
    {
        $gifts = new PathGiftCatalogue();

        foreach ($this->expected() as $key => $label) {
            self::assertTrue($gifts->supports($key));
            self::assertSame($label, $gifts->pathLabel($key));

            $features = $gifts->all($key);

            self::assertCount(4, $features);
            self::assertSame(
                [3, 7, 11, 15],
                array_column($features, 'level')
            );

            foreach ($features as $feature) {
                self::assertSame('automatic', $feature['mode']);
                self::assertNotSame('', $feature['summary']);
                self::assertNotSame('', $feature['detail']);
            }
        }
    }

    public function testDistinctiveRangerMechanicsRemainInCanonData(): void
    {
        $gifts = new PathGiftCatalogue();

        self::assertStringContainsString(
            '1d6',
            $gifts->all('aislewarden-conclave')[0]['detail']
        );

        self::assertStringContainsString(
            'proficiency bonus per long rest',
            $gifts->all('deep-root-warden')[0]['detail']
        );

        self::assertStringContainsString(
            '2d6 cold damage',
            $gifts->all('cold-vault-stalker')[2]['detail']
        );

        self::assertStringContainsString(
            'Mintleaf Draught',
            $gifts->all('conclave-of-the-forager')[0]['detail']
        );

        self::assertStringContainsString(
            '2d6 fire',
            $gifts->all('spice-trail-hunter')[3]['detail']
        );

        self::assertStringContainsString(
            'Wisdom modifier per long rest',
            $gifts->all('rindrunner')[3]['detail']
        );

        self::assertStringContainsString(
            '30-foot-radius miniature enchanted forest',
            $gifts->all('seedshot-conclave')[3]['detail']
        );

        self::assertStringContainsString(
            'reduce the healing to 0',
            $gifts->all('expiry-hunter')[2]['detail']
        );
    }

    public function testCandidatePreviewShowsFirstFourPathFeatures(): void
    {
        $candidates = (new PathCandidateCatalogue())->forClass(
            CharacterClass::fromString('ranger')
        );

        foreach ($candidates as $candidate) {
            self::assertCount(4, $candidate['gift_preview']);
            self::assertSame(
                [3, 7, 11, 15],
                array_column($candidate['gift_preview'], 'level')
            );
        }
    }

    public function testCatalogueSnapshotVersionMovesToThreeSevenFour(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 6)
            . '/app/Modules/Characters/Catalogue/Repositories/'
            . 'CharacterCatalogueRepository.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            "private const VERSION = '3.7.4';",
            $source
        );

        $catalogue = json_decode(
            (string) file_get_contents(
                dirname(__DIR__, 6)
                . '/resources/catalogue/players-handbook.v1.json'
            ),
            true
        );

        self::assertSame('3.7.4', $catalogue['version']);
    }

    public function testFieldRegisterNowReportsAvailableRangerPaths(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 6)
            . '/app/Modules/Characters/Progression/Ranger/Services/'
            . 'RangerFieldRegisterPresenter.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'Eight Ranger Paths available',
            $source
        );
    }
}
