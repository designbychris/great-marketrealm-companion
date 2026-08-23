<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class CampaignCommandCentreRegressionTest extends TestCase
{
    public function testCommandCentreAggregatesExistingCampaignLedgers(): void
    {
        $service = $this->read('app/Modules/DungeonMaster/Services/CampaignCommandCentre.php');
        foreach (['CampaignRosterRepository','SessionRepository','EncounterRepository','JournalRepository'] as $repository) {
            self::assertStringContainsString($repository, $service);
        }
        self::assertStringContainsString("'liveEncounter'", $service);
        self::assertStringContainsString("'preparedEncounter'", $service);
        self::assertStringContainsString("'pinnedJournal'", $service);
    }

    public function testCampaignShowBecomesCommandCentreWithoutSecondCampaignStore(): void
    {
        $controller = $this->read('app/Modules/DungeonMaster/Controllers/CampaignController.php');
        $view = $this->read('app/Modules/DungeonMaster/Views/campaigns/show.php');
        self::assertStringContainsString('CampaignCommandCentre', $controller);
        self::assertStringContainsString("'commandCentre'=>\$this->commandCentre->build(\$campaign)", $controller);
        self::assertStringContainsString('Campaign Command Centre', $view);
        self::assertStringContainsString('Continue combat', $view);
        self::assertStringContainsString('Run encounter', $view);
        self::assertStringContainsString('Plan first session', $view);
        self::assertStringContainsString('Write campaign note', $view);
    }

    public function testCommandCentreHasResponsiveAccessibleVisualTreatment(): void
    {
        $css = $this->read('assets/css/modules/dungeon-master/command-centre.css');
        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('@media (max-width:800px)', $css);
        self::assertStringContainsString('prefers-reduced-transparency:reduce', $css);
        self::assertStringContainsString('@media (forced-colors:active)', $css);
    }

    private function read(string $path): string
    {
        $root = dirname(__DIR__, 4);
        $contents = file_get_contents($root . '/' . $path);
        self::assertIsString($contents);
        return $contents;
    }
}
