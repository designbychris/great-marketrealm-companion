<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties;

use PHPUnit\Framework\TestCase;

final class PartyApplicationRegistrationTest extends TestCase
{
    public function testPartyApplicationServicesAreRegistered(): void
    {
        $root = dirname(__DIR__, 4);
        $provider = file_get_contents(
            $root . '/app/Modules/Parties/PartiesServiceProvider.php'
        );

        self::assertIsString($provider);

        foreach ([
            'PartyFinder::class',
            'CreatePartyAction::class',
            'AddPartyMemberAction::class',
            'RemovePartyMemberAction::class',
            'ChangePartyMemberRoleAction::class',
            'RenamePartyAction::class',
            'DeletePartyAction::class',
        ] as $service) {
            self::assertStringContainsString(
                $service,
                $provider
            );
        }

        self::assertStringContainsString(
            'CharacterRepositoryInterface::class',
            $provider
        );
    }
}
