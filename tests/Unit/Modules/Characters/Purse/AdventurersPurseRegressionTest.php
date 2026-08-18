<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Purse;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterPurse;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AdventurersPurseRegressionTest extends TestCase
{
    public function testNewCharacterStartsWithEmptyPurse(): void
    {
        $character = $this->character();

        self::assertSame(
            0,
            $character->purse()->copper()
        );
        self::assertTrue(
            $character->purse()->isEmpty()
        );
    }

    public function testPurseCanonicalisesCoinsToCopper(): void
    {
        $purse = CharacterPurse::fromCoins(
            12,
            3,
            4
        );

        self::assertSame(1234, $purse->copper());
        self::assertSame(
            [
                'gold' => 12,
                'silver' => 3,
                'copper' => 4,
            ],
            $purse->coins()
        );
    }

    public function testPurseFormatsPersonalCoinClearly(): void
    {
        self::assertSame(
            '7 gp · 8 sp · 9 cp',
            CharacterPurse::fromCopper(
                789
            )->formatted()
        );
    }

    public function testNegativeCoinIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterPurse::fromCoins(
            1,
            -1,
            0
        );
    }

    public function testDepositIncreasesPersonalPurse(): void
    {
        $character = $this->character();

        $character->depositToPurse(
            CharacterPurse::fromCoins(
                3,
                5,
                0
            )
        );

        self::assertSame(
            350,
            $character->purse()->copper()
        );
    }

    public function testWithdrawalReducesPersonalPurse(): void
    {
        $character = $this->character();

        $character->depositToPurse(
            CharacterPurse::fromCoins(
                5,
                0,
                0
            )
        );

        $character->withdrawFromPurse(
            CharacterPurse::fromCoins(
                1,
                2,
                5
            )
        );

        self::assertSame(
            375,
            $character->purse()->copper()
        );
    }

    public function testPurseCannotBeOverdrawn(): void
    {
        $character = $this->character();

        $character->depositToPurse(
            CharacterPurse::fromCoins(
                1,
                0,
                0
            )
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $character->withdrawFromPurse(
            CharacterPurse::fromCoins(
                1,
                0,
                1
            )
        );
    }

    public function testZeroDepositIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->character()->depositToPurse(
            CharacterPurse::empty()
        );
    }

    public function testZeroWithdrawalIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->character()->withdrawFromPurse(
            CharacterPurse::empty()
        );
    }

    public function testRepositoryPersistsPurseAsCanonicalCopper(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            "'_gmrc_character_purse_copper'",
            $source
        );
        self::assertStringContainsString(
            '$character->purse()->copper()',
            $source
        );
        self::assertStringContainsString(
            'CharacterPurse::fromCopper(',
            $source
        );
        self::assertStringContainsString(
            'self::META_PURSE',
            $source
        );
    }

    public function testLegacyCharacterWithoutPurseHydratesAsZero(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'max(',
            $source
        );
        self::assertStringContainsString(
            'self::META_PURSE',
            $source
        );
    }

    public function testPurseHasDedicatedDepositAndWithdrawalRoutes(): void
    {
        $routes = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Routes.php'
        );

        self::assertIsString($routes);
        self::assertStringContainsString(
            "'/characters/{id}/purse/deposit'",
            $routes
        );
        self::assertStringContainsString(
            "'depositPurse'",
            $routes
        );
        self::assertStringContainsString(
            "'/characters/{id}/purse/withdraw'",
            $routes
        );
        self::assertStringContainsString(
            "'withdrawPurse'",
            $routes
        );
    }

    public function testPurseUsesDedicatedCharacterNonceContract(): void
    {
        $provider = file_get_contents(
            $this->root()
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            '#^characters/([^/]+)/purse/(?:deposit|withdraw)$#',
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_character_purse_'",
            $provider
        );
    }

    public function testControllerRejectsInvalidAndOverdrawnPurseRequests(): void
    {
        $controller = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);
        self::assertStringContainsString(
            'private function purseAmountFromRequest()',
            $controller
        );
        self::assertStringContainsString(
            '$gold > 999999',
            $controller
        );
        self::assertStringContainsString(
            '$silver > 9',
            $controller
        );
        self::assertStringContainsString(
            '$copper > 9',
            $controller
        );
        self::assertStringContainsString(
            '$amount->copper()',
            $controller
        );
        self::assertStringContainsString(
            '> $character->purse()->copper()',
            $controller
        );
    }

    public function testPurseWritesReturnToEquipmentLedger(): void
    {
        $controller = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);
        self::assertGreaterThanOrEqual(
            4,
            substr_count(
                $controller,
                "'equipment'"
            )
        );
    }

    public function testEquipmentLedgerShowsPersonalPurseBalanceAndControls(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'The Adventurer’s Purse',
            $view
        );
        self::assertStringContainsString(
            '$character->purse()->formatted()',
            $view
        );
        self::assertStringContainsString(
            'Add to Purse',
            $view
        );
        self::assertStringContainsString(
            'Spend from Purse',
            $view
        );
        self::assertStringContainsString(
            "'gmrc_character_purse_'",
            $view
        );
    }

    public function testPersonalPurseExplainsFellowshipTreasuryBoundary(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'These coins belong to this adventurer personally.',
            $view
        );
        self::assertStringContainsString(
            'Fellowship funds remain separate',
            $view
        );
    }

    public function testPursePhaseDoesNotMutateFellowshipTreasury(): void
    {
        $controller = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);
        self::assertStringNotContainsString(
            'depositTreasury(',
            $controller
        );
        self::assertStringNotContainsString(
            'withdrawTreasury(',
            $controller
        );
    }

    private function character(): Character
    {
        return Character::create(
            CharacterId::generate(),
            CharacterName::fromString(
                'Penny Parsnip'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::average()
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}
