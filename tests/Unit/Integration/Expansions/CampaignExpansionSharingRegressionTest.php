<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Integration\Expansions;

use PHPUnit\Framework\TestCase;

final class CampaignExpansionSharingRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testCampaignHasDedicatedAlmanacSharingRouteAndNonce(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $view = $this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');

        self::assertStringContainsString(
            "'/dungeon-master/campaigns/{id}/almanacs'",
            $routes
        );
        self::assertStringContainsString('CampaignExpansionController::class', $routes);
        self::assertStringContainsString('gmrc_dm_campaign_almanacs_', $frontend);
        self::assertStringContainsString('gmrc_dm_campaign_almanacs_', $view);
    }

    public function testCampaignRepositoryStoresOnlyCanonicalExpansionKeys(): void
    {
        $repository = $this->source(
            'app/Modules/DungeonMaster/Repositories/CampaignExpansionRepository.php'
        );

        self::assertStringContainsString('_gmrc_campaign_expansions', $repository);
        self::assertStringContainsString('postIdForOwner(', $repository);
        self::assertStringContainsString('update_post_meta(', $repository);
        self::assertStringNotContainsString('ContentDefinition', $repository);
        self::assertStringNotContainsString('canonical_id', $repository);
    }

    public function testCampaignAccessSeparatesAvailableConfiguredAndConsumableBooks(): void
    {
        $service = $this->source(
            'app/Modules/DungeonMaster/Services/CampaignExpansionAccess.php'
        );

        self::assertStringContainsString('availableToDungeonMaster(', $service);
        self::assertStringContainsString('configuredForCampaign(', $service);
        self::assertStringContainsString('activeForCampaign(', $service);
        self::assertStringContainsString('array_intersect(', $service);
        self::assertStringContainsString('unavailableConfigured', $service);
        self::assertStringContainsString('temporarily unavailable', strtolower($service));
    }

    public function testPlayersInheritTheUnionOfAlmanacsSharedByTheirActiveCampaigns(): void
    {
        $service = $this->source(
            'app/Modules/DungeonMaster/Services/CampaignExpansionAccess.php'
        );

        self::assertStringContainsString('allForPlayer($userId)', $service);
        self::assertStringContainsString('$campaign->isArchived()', $service);
        self::assertStringContainsString('activeForCampaign($campaign)', $service);
        self::assertStringContainsString('array_keys($keys)', $service);
        self::assertStringContainsString('user_can($userId, GuildRoleRegistrar::MANAGE_CAMPAIGNS)', $service);
    }

    public function testCompanionExpansionProjectionIsScopedThroughOnePlayerAccessFilter(): void
    {
        $adapter = $this->source(
            'app/Integration/Expansions/ExpansionCharacterCatalogue.php'
        );
        $provider = $this->source(
            'app/Modules/DungeonMaster/DungeonMasterServiceProvider.php'
        );

        self::assertStringContainsString('gmrc_expansion_character_scope', $adapter);
        self::assertStringContainsString('currentExpansionScope()', $adapter);
        self::assertStringContainsString('gmrc_expansion_character_scope', $provider);
        self::assertStringContainsString('expansionKeysForUser($userId)', $provider);
        self::assertStringContainsString('activeExpansionKeys()', $adapter);
    }

    public function testCampaignCommandCentreLetsTheDmShareBooksWithoutCopyingThem(): void
    {
        $view = $this->source(
            'app/Modules/DungeonMaster/Views/campaigns/show.php'
        );
        $controller = $this->source(
            'app/Modules/DungeonMaster/Controllers/CampaignExpansionController.php'
        );

        self::assertStringContainsString('Campaign Almanacs', $view);
        self::assertStringContainsString('Shared ≠ copied.', $view);
        self::assertStringContainsString('expansions[]', $view);
        self::assertStringContainsString('Share selected Almanacs', $view);
        self::assertStringContainsString('saveCampaignSelection(', $controller);
        self::assertStringContainsString('Campaign Almanac controls are sealed to Dungeon Masters.', $controller);
    }

    public function testCharacterGeneratorMarksExpansionChoicesWithSourcebookProvenance(): void
    {
        $view = $this->source('app/Modules/Characters/Views/create.php');
        $controller = $this->source(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );

        self::assertStringContainsString('expansionPresentation', $controller);
        self::assertStringContainsString('presentationMap()', $controller);
        self::assertStringContainsString('data-expansion-source="gmrexp"', $view);
        self::assertStringContainsString('data-expansion=', $view);
        self::assertStringContainsString('gmrc-expansion-source-badge', $view);
        self::assertStringContainsString('gmrc-choice-card--expansion', $view);
        self::assertStringContainsString('gmrc-background-option--expansion', $view);
        self::assertStringContainsString('gmrc-subclass-preview__card--expansion', $view);
    }

    public function testMidnightMenuGetsNeonFirstPassWithoutUsingColourAsTheOnlyCue(): void
    {
        $choiceCss = $this->source(
            'assets/css/modules/characters/choice-selector.css'
        );
        $backgroundCss = $this->source(
            'assets/css/modules/characters/background-selector.css'
        );
        $subclassCss = $this->source(
            'assets/css/modules/characters/grand-catalogue.css'
        );
        $view = $this->source('app/Modules/Characters/Views/create.php');

        self::assertStringContainsString('gmrc-choice-card--expansion-midnight-menu', $choiceCss);
        self::assertStringContainsString('gmrc-background-option--expansion-midnight-menu', $backgroundCss);
        self::assertStringContainsString('gmrc-subclass-preview__card--expansion-midnight-menu', $subclassCss);
        self::assertStringContainsString('gmrc-expansion-source-badge', $view);
        self::assertStringContainsString('forced-colors: active', $choiceCss);
        self::assertStringContainsString('prefers-reduced-motion: reduce', $choiceCss);
    }

    public function testEntitlementRemainsAReplaceablePolicyRatherThanCampaignContentStorage(): void
    {
        $docs = $this->source(
            'docs/PHASE-V.11A.2-THE-DM-SHARES-THE-BOOK.md'
        );

        self::assertStringContainsString('Available → Entitled → Campaign Active → Consumable', $docs);
        self::assertStringContainsString('free entitlement', strtolower($docs));
        self::assertStringContainsString('payment', strtolower($docs));
        self::assertStringContainsString('GMREXP remains canonical', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
