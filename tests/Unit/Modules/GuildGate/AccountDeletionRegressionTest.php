<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class AccountDeletionRegressionTest extends TestCase
{
    public function testDeletionCharterHasPublicPrettyRouteAndAuthenticatedCommand(): void
    {
        $routes = $this->source('app/Modules/GuildGate/Routes.php');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString("'/delete-account'", $routes);
        self::assertStringContainsString("'^companion/delete-account/?$'", $frontend);
        self::assertStringContainsString("'gmrc_account_deletion_request'", $frontend);
        self::assertStringContainsString('return $gate->deleteAccount();', $frontend);
    }

    public function testDeletionRequestRequiresPasswordAndExplicitPhrase(): void
    {
        $service = $this->source('app/Modules/GuildGate/Services/RequestAccountDeletion.php');

        self::assertStringContainsString('wp_check_password(', $service);
        self::assertStringContainsString("CONFIRMATION_PHRASE = 'DELETE MY ACCOUNT'", $service);
        self::assertStringContainsString('gmrc_account_deletion_requested_at', $service);
        self::assertStringContainsString('wp_mail(', $service);
        self::assertStringNotContainsString('wp_delete_user(', $service);
    }

    public function testPublicCharterExplainsDeletedAndSharedData(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/delete-account.php');

        self::assertStringContainsString('Delete your Great MarketRealm account and data', $view);
        self::assertStringContainsString('Guild profile and owned Character records', $view);
        self::assertStringContainsString('detached or anonymised', $view);
        self::assertStringContainsString('legal, security or fraud-prevention obligations', $view);
        self::assertStringContainsString("wp_nonce_field('gmrc_account_deletion_request'", $view);
    }

    public function testProfileAndPocketBothExposeTheWebAuthoritativeDeletionRoute(): void
    {
        $profile = $this->source('app/Modules/GuildGate/Views/profile.php');
        $native = $this->source('native/pocket-companion/src/main.js');

        self::assertStringContainsString("home_url('/companion/delete-account/')", $profile);
        self::assertStringContainsString('Delete Account and Data', $profile);
        self::assertStringContainsString("const DELETE_ACCOUNT_URL = 'https://greatmarketrealm.co.uk/companion/delete-account/'", $native);
        self::assertStringContainsString('Delete Account &amp; Data', $native);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
