<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Honours;

use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\Honours\Services\GuildHonourRegistry;
use PHPUnit\Framework\TestCase;

final class GuildHonoursFoundationRegressionTest extends TestCase
{
    public function testCanonicalRegistryBeginsWithSixGuildHonours(): void
    {
        $honours = (new GuildHonourRegistry())->all();

        self::assertCount(6, $honours);
        self::assertSame(
            ['first-inscription', 'company-of-heroes', 'campaign-table', 'fellowship-oath', 'tale-completed', 'campaign-steward'],
            array_column($honours, 'key')
        );
    }

    public function testCampaignStewardHonourIsDungeonMasterSpecific(): void
    {
        $honours = (new GuildHonourRegistry())->all();
        $steward = $honours[5];

        self::assertSame([AccountType::DM], $steward['accountTypes']);
    }

    public function testBookOfDeedsUsesCertifiedMembershipSummaryRatherThanParallelRelationshipQueries(): void
    {
        $source = $this->source('app/Modules/Honours/Services/BookOfDeeds.php');

        self::assertStringContainsString('GuildMembershipSummary', $source);
        self::assertStringContainsString("'characters'", $source);
        self::assertStringContainsString("'campaigns'", $source);
        self::assertStringContainsString("'fellowships'", $source);
    }

    public function testHonourLedgerIsAppendOnlyOnceADeedIsCertified(): void
    {
        $source = $this->source('app/Modules/Honours/Services/GuildHonourLedger.php');

        self::assertStringContainsString("private const META_KEY = '_gmrc_guild_honours'", $source);
        self::assertStringContainsString('isset($honours[$key])', $source);
        self::assertStringContainsString("\$honours[\$key] = gmdate('c');", $source);
        self::assertStringNotContainsString('delete_user_meta', $source);
    }

    public function testGuildHonoursRouteIsReadOnlyAndRegisteredThroughItsOwnKingdom(): void
    {
        $routes = $this->source('app/Modules/Honours/Routes.php');
        $kingdom = $this->source('app/Kingdoms/HonoursKingdom.php');

        self::assertStringContainsString("\$router->get('/guild-honours'", $routes);
        self::assertStringNotContainsString('$router->post(', $routes);
        self::assertStringContainsString('HonoursServiceProvider::class', $kingdom);
        self::assertStringContainsString("'guild-honours'", $kingdom);
    }

    public function testInstalledKingdomRegistryIncludesGuildHonours(): void
    {
        $provider = $this->source('app/Providers/KingdomServiceProvider.php');

        self::assertStringContainsString('use GreatMarketrealmCompanion\\Kingdoms\\HonoursKingdom;', $provider);
        self::assertStringContainsString('new HonoursKingdom($this->app)', $provider);
    }

    public function testGuildHallNoLongerTreatsHonoursAsAPlannedRoom(): void
    {
        $directory = $this->source('app/Modules/Dashboard/Services/GuildHallDirectory.php');

        self::assertStringContainsString("'guild-honours'", $directory);
        self::assertStringContainsString("[['route' => 'guild-honours', 'label' => 'Open the Book of Deeds']]", $directory);
        self::assertStringNotContainsString('plannedHonoursRoom', $directory);
    }

    public function testBookOfDeedsViewDistinguishesCertifiedAndUnwitnessedHonours(): void
    {
        $view = $this->source('app/Modules/Honours/Views/index.php');

        self::assertStringContainsString('Certified honour', $view);
        self::assertStringContainsString('Deed yet to be witnessed', $view);
        self::assertStringContainsString('Entered in the Book:', $view);
        self::assertStringContainsString('account-level Guild Honours', $view);
    }

    public function testCharacterLedgerRetiresStaleRoadAheadClaimsAndLinksToBookOfDeeds(): void
    {
        $view = $this->source('app/Modules/Characters/Views/show.php');

        self::assertStringContainsString('Open the Book of Deeds', $view);
        self::assertStringContainsString("'guild-honours'", $view);
        self::assertStringNotContainsString('Inventory and equipment will be recorded here.', $view);
        self::assertStringNotContainsString('Guild achievements will become stamps within the Ledger.', $view);
    }

    public function testBookOfDeedsPresentationIncludesResponsiveAndAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/honours/book-of-deeds.css');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString('@media(max-width:760px)', $css);
        self::assertStringContainsString('@media(prefers-reduced-motion:reduce)', $css);
        self::assertStringContainsString('@media(forced-colors:active)', $css);
        self::assertStringContainsString("'handle' => 'gmrc-book-of-deeds'", $frontend);
    }

    private function source(string $relativePath): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $relativePath);
        self::assertIsString($source);

        return $source;
    }
}
