<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\AdvancementFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\FolioCollection;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\FolioStatus;
use PHPUnit\Framework\TestCase;

final class RisingFoliosTest extends TestCase
{
    public function testCollectionTracksReadyAndAttentionFolios(): void
    {
        $folios = new FolioCollection();

        $folios->add(
            new AdvancementFolio(
                'ready',
                'Ready Folio',
                'No decision required.',
                FolioStatus::READY,
                false
            )
        );

        $folios->add(
            new AdvancementFolio(
                'attention',
                'Attention Folio',
                'A decision is required.',
                FolioStatus::ATTENTION,
                true
            )
        );

        self::assertSame(2, $folios->total());
        self::assertSame(1, $folios->readyCount());
        self::assertSame(1, $folios->attentionCount());
        self::assertFalse($folios->allReady());
    }

    public function testFolioSerialisesDecisionState(): void
    {
        $folio = new AdvancementFolio(
            'vitality',
            'Vitality Folio',
            'Choose HP.',
            FolioStatus::ATTENTION,
            true,
            ['hit_die' => 'd8'],
            [['key' => 'average']]
        );

        $state = $folio->toArray();

        self::assertSame('vitality', $state['key']);
        self::assertTrue($state['requires_choice']);
        self::assertFalse($state['ready']);
        self::assertSame('d8', $state['facts']['hit_die']);
    }
}
