<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Combat\Targets;

use GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Models\RollTarget;
use GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Models\RollTargetKind;
use PHPUnit\Framework\TestCase;

final class RollTargetContractTest extends TestCase
{
    public function testStableTargetKindsExposePlayerFacingLabels(): void
    {
        self::assertSame(
            [
                'self' => 'Self',
                'ally' => 'Ally',
                'player-character' => 'Player Character',
                'npc' => 'NPC',
                'hostile-creature' => 'Hostile Creature',
            ],
            RollTargetKind::labels()
        );

        self::assertTrue(
            RollTargetKind::valid('hostile-creature')
        );
        self::assertFalse(
            RollTargetKind::valid('mystery-target')
        );
    }

    public function testResolvedTargetRequiresConcreteIdentity(): void
    {
        $target = RollTarget::resolved(
            RollTargetKind::SELF,
            '01KZM4W72K1G12FY75R0BTQREW',
            'Magic'
        );

        self::assertSame(
            RollTargetKind::SELF,
            $target->kind()
        );
        self::assertSame(
            '01KZM4W72K1G12FY75R0BTQREW',
            $target->id()
        );
        self::assertSame('Magic', $target->label());
        self::assertTrue($target->isResolved());

        self::assertSame(
            [
                'kind' => 'self',
                'id' => '01KZM4W72K1G12FY75R0BTQREW',
                'label' => 'Magic',
                'resolved' => true,
            ],
            $target->toArray()
        );
    }

    public function testReferenceTargetCanRemainUnresolved(): void
    {
        $target = RollTarget::reference(
            RollTargetKind::HOSTILE_CREATURE,
            'Gravy Golem'
        );

        self::assertSame(
            'hostile-creature',
            $target->kind()
        );
        self::assertNull($target->id());
        self::assertSame(
            'Gravy Golem',
            $target->label()
        );
        self::assertFalse(
            $target->isResolved()
        );
    }
}
