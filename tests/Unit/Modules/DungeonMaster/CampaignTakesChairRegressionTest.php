<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class CampaignTakesChairRegressionTest extends TestCase
{
    public function testDeskKeepsCertifiedLedgerGatewaysWhileWorkshopGrows(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/index.php');

        self::assertStringContainsString('Plan adventures. Guide legends. Shape the Marketrealm.', $view);
        self::assertStringContainsString('Session Ledger', $view);
        self::assertStringContainsString('Encounter Board', $view);
        self::assertStringContainsString('Player Roster', $view);
        self::assertStringContainsString('Monster Ledger', $view);
        self::assertStringContainsString('Campaign Journal', $view);
        self::assertStringContainsString('Choose Campaign', $view);
    }

    public function testCampaignCommandCentreKeepsDirectEvidenceAndPlanningLinks(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');

        foreach ([
            'Open Session Ledger',
            'Open Encounter Board',
            'Open Player Roster',
            'Open Campaign Journal',
            'Open Gazetteer',
            'Open Dramatis Personae',
            'Open Evidence Register',
        ] as $label) {
            self::assertStringContainsString($label, $view);
        }

        self::assertStringContainsString("\$campaignPath . '/locations'", $view);
        self::assertStringContainsString("\$campaignPath . '/personae'", $view);
        self::assertStringContainsString("\$campaignPath . '/evidence'", $view);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);

        return $source;
    }
}
