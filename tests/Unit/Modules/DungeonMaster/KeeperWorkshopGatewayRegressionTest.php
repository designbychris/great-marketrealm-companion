<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class KeeperWorkshopGatewayRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testWorkshopFormsAndGatewayShareCampaignScopedNonceContracts(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        foreach ([
            'gmrc_dm_gazetteer_',
            'gmrc_dm_personae_',
            'gmrc_dm_evidence_',
        ] as $noncePrefix) {
            self::assertStringContainsString($noncePrefix, $frontend);
        }

        $forms = [
            'app/Modules/DungeonMaster/Views/locations/_form.php' => 'gmrc_dm_gazetteer_',
            'app/Modules/DungeonMaster/Views/personae/_form.php' => 'gmrc_dm_personae_',
            'app/Modules/DungeonMaster/Views/evidence/_form.php' => 'gmrc_dm_evidence_',
        ];

        foreach ($forms as $path => $noncePrefix) {
            $form = $this->source($path);
            self::assertStringContainsString('value="gmrc_app_request"', $form);
            self::assertStringContainsString($noncePrefix, $form);
        }
    }

    public function testWorkshopGatewayCoversCreateUpdateAndArchiveRouteFamilies(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $compact = preg_replace('/\\s+/', '', $frontend);
        self::assertIsString($compact);

        foreach (['locations', 'personae', 'evidence'] as $register) {
            self::assertMatchesRegularExpression(
                '/dungeon-master\\/campaigns.*' . preg_quote($register, '/') . '.*archive/s',
                $compact
            );
        }

        self::assertStringContainsString("in_array(\u0000method", str_replace('$', "\u0000", $compact));
        self::assertStringContainsString("['POST','PUT']", $compact);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
