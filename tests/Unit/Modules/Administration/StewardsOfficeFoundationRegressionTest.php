<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardsOfficeFoundationRegressionTest extends TestCase
{
    public function testAdministrationProviderIsRegisteredByKernel(): void
    {
        $kernel = $this->source('app/Core/Kernel.php');
        self::assertStringContainsString('AdministrationServiceProvider::class', $kernel);
    }

    public function testOfficeIsProtectedByAdministratorCapability(): void
    {
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString("CAPABILITY = 'manage_options'", $provider);
        self::assertStringContainsString('current_user_can(self::CAPABILITY)', $provider);
        self::assertStringContainsString("add_action('admin_menu'", $provider);
    }

    public function testOfficeCreatesDedicatedWordPressAdminMenu(): void
    {
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('add_menu_page(', $provider);
        self::assertStringContainsString("MENU_SLUG = 'gmrc-stewards-office'", $provider);
        self::assertStringContainsString("The Steward's Office", $provider);
    }

    public function testFoundationDoesNotYetExposeEditableSecrets(): void
    {
        $view = $this->source('app/Modules/Administration/Views/stewards-office.php');
        self::assertStringContainsString('No secrets or canonical records are editable yet.', $view);
        self::assertStringNotContainsString('<input', $view);
    }

    public function testCampaignCommandCentreDefinesDungeonMasterBackground(): void
    {
        $css = $this->source('assets/css/modules/dungeon-master/command-centre.css');
        self::assertStringContainsString('--gmrc-dm-background:url(', $css);
        self::assertStringContainsString('dungeon-master-desk-background.png', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
