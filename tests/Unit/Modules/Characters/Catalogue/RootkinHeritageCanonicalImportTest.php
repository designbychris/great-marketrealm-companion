<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Catalogue;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\HeritageGuidance;
use PHPUnit\Framework\TestCase;

final class RootkinHeritageCanonicalImportTest extends TestCase
{
    public function testRootkinExposeExactlyFiveCanonicalHeritages(): void
    {
        $heritages = array_values(array_filter(
            $this->catalogue()['heritages'],
            static fn (array $heritage): bool =>
                ($heritage['parent'] ?? '') === 'rootkin'
        ));

        self::assertSame(
            ['carrotfolk', 'potatofolk', 'onionfolk', 'garlicfolk', 'parsnipfolk'],
            array_column($heritages, 'key')
        );
    }

    public function testCarrotfolkCarryKeenrootMechanics(): void
    {
        $guidance = HeritageGuidance::normalize($this->heritage('carrotfolk'));

        self::assertSame(2, $guidance['ability_modifiers']['dexterity']);
        self::assertSame(1, $guidance['ability_modifiers']['wisdom']);
        self::assertSame('30 ft', $guidance['speed']);
        self::assertSame(['Perception'], $guidance['skill_proficiencies']);
        self::assertContains('Spring from the Soil', array_column($guidance['features'], 'name'));
    }

    public function testOtherRootkinHeritagesKeepTheirCanonicalIdentity(): void
    {
        self::assertContains(
            'Potato Resilience',
            array_column(
                HeritageGuidance::normalize($this->heritage('potatofolk'))['features'],
                'name'
            )
        );

        self::assertSame(
            ['Layered Defence', 'Eye-Watering Defence', 'Peel Away'],
            array_column(
                HeritageGuidance::normalize($this->heritage('onionfolk'))['features'],
                'name'
            )
        );

        $garlic = HeritageGuidance::normalize($this->heritage('garlicfolk'));
        self::assertSame(['Intimidation'], $garlic['skill_proficiencies']);
        self::assertContains('Graveward', array_column($garlic['features'], 'name'));
    }

    public function testParsnipfolkExposeNatureOrHistoryChoice(): void
    {
        $guidance = HeritageGuidance::normalize($this->heritage('parsnipfolk'));

        self::assertSame(2, $guidance['ability_modifiers']['wisdom']);
        self::assertSame(1, $guidance['ability_modifiers']['intelligence']);
        self::assertSame(['Nature', 'History'], $guidance['proficiency_choices'][0]['from']);
        self::assertContains('Root Magic', array_column($guidance['features'], 'name'));
    }

    /** @return array<string,mixed> */
    private function catalogue(): array
    {
        $root = dirname(__DIR__, 5);
        $catalogue = json_decode(
            (string) file_get_contents($root . '/resources/catalogue/players-handbook.v1.json'),
            true
        );

        self::assertIsArray($catalogue);
        self::assertSame('3.7.6', $catalogue['version']);

        return $catalogue;
    }

    /** @return array<string,mixed> */
    private function heritage(string $key): array
    {
        foreach ($this->catalogue()['heritages'] as $heritage) {
            if (($heritage['key'] ?? '') === $key) {
                return $heritage;
            }
        }

        self::fail('Missing Rootkin Heritage: ' . $key);
    }
}
