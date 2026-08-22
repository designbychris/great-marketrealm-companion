<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use PHPUnit\Framework\TestCase;

final class AccountTypeTest extends TestCase
{
    public function testGuildAccountTypesArePlayerAndDm(): void
    {
        self::assertSame(
            ['player', 'dm'],
            AccountType::values()
        );
    }

    public function testPlayerMapsToDedicatedWordPressRole(): void
    {
        self::assertSame(
            'gmrc_player',
            AccountType::role(AccountType::PLAYER)
        );
    }

    public function testDmMapsToDedicatedWordPressRole(): void
    {
        self::assertSame(
            'gmrc_dm',
            AccountType::role(AccountType::DM)
        );
    }
}
