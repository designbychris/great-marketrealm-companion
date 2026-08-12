<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Core\Session\SessionStore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories\AdvancementChoiceStore;
use PHPUnit\Framework\TestCase;

final class AdvancementChoiceStoreTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testChoicesAreScopedByCharacterAndTargetLevel(): void
    {
        $store = new AdvancementChoiceStore(
            new SessionStore()
        );

        $characterId = CharacterId::fromString(
            '01KZM4W72K1G12FY75R0BTQREW'
        );

        $store->put(
            $characterId,
            2,
            'vitality-hit-points',
            ['average']
        );

        self::assertSame(
            [
                'vitality-hit-points' => [
                    'average',
                ],
            ],
            $store->all(
                $characterId,
                2
            )
        );

        self::assertSame(
            [],
            $store->all(
                $characterId,
                3
            )
        );
    }
}
