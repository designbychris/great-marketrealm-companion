<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties;

use PHPUnit\Framework\TestCase;

final class PartyPersistenceRegistrationTest extends TestCase
{
    public function testPartyKingdomRegistersPersistenceProviderWithoutNavigationYet(): void
    {
        $root = dirname(__DIR__, 4);

        $kingdom = file_get_contents(
            $root . '/app/Kingdoms/PartiesKingdom.php'
        );
        $registry = file_get_contents(
            $root . '/app/Providers/KingdomServiceProvider.php'
        );
        $provider = file_get_contents(
            $root . '/app/Modules/Parties/PartiesServiceProvider.php'
        );

        self::assertIsString($kingdom);
        self::assertIsString($registry);
        self::assertIsString($provider);

        self::assertStringContainsString(
            "return 'parties';",
            $kingdom
        );
        self::assertStringContainsString(
            'PartiesServiceProvider::class',
            $kingdom
        );
        self::assertStringContainsString(
            'new PartiesKingdom($this->app)',
            $registry
        );
        self::assertStringContainsString(
            'PartyRepositoryInterface::class',
            $provider
        );
        self::assertStringContainsString(
            "register_post_type(\n            'gmrc_party'",
            $provider
        );
        self::assertStringNotContainsString(
            'registerNavigation',
            $kingdom
        );
    }
}
