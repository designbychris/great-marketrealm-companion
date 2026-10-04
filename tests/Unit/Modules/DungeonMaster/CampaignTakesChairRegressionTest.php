<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class CampaignTakesChairRegressionTest extends TestCase
{
    public function testDeskIsCampaignFirstAndControllerLoadsOwnedCampaigns(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/index.php');
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/DungeonMasterController.php');
        $provider = $this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');
        self::assertStringContainsString('Your Campaigns', $view);
        self::assertStringContainsString('Start New Campaign', $view);
        self::assertStringNotContainsString('Choose Campaign', $view);
        self::assertStringContainsString("'campaigns' => \$this->campaigns->allForOwner(get_current_user_id())", $controller);
        self::assertStringContainsString('$c->make(CampaignRepository::class)', $provider);
    }

    public function testCommandCentreOwnsCampaignScopedInstruments(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');
        foreach (['Session Ledger','Encounter Board','Player Roster','Monster Ledger','Campaign Journal','Keeper’s Gazetteer','Dramatis Personae','Evidence Register','Conspiracy Board','Cartographer’s Bench'] as $label) {
            self::assertStringContainsString($label, $view);
        }
        self::assertStringContainsString("\$campaignPath . '/locations'", $view);
        self::assertStringContainsString("\$campaignPath . '/personae'", $view);
        self::assertStringContainsString("\$campaignPath . '/evidence'", $view);
        self::assertStringContainsString('No campaign selection required.', $view);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
