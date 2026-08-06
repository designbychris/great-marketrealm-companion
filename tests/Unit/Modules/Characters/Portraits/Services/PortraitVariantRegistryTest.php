<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitLayerRegistry;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitVariantRegistry;
use PHPUnit\Framework\TestCase;

final class PortraitVariantRegistryTest extends TestCase
{
    private PortraitVariantRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new PortraitVariantRegistry(
            new PortraitLayerRegistry(),
            new PortraitSvgAssetLibrary(
                dirname(__DIR__, 6)
                . '/app/Modules/Characters/Portraits/Library'
            )
        );
    }

    public function testItOnlyReturnsExistingBackgroundAssets(): void
    {
        self::assertSame(
            [
                'background-parchment-01',
                'background-market-arch-01',
                'background-guild-hall-01',
            ],
            $this->registry->variants('background')
        );
    }

    public function testItFiltersMissingClassVariants(): void
    {
        self::assertSame(
            ['grocer-outfit-01'],
            $this->registry->variants(
                'outfit',
                'fructan',
                'grocer'
            )
        );
    }

    public function testItKeepsNoneAndExistingAccessoryVariants(): void
    {
        self::assertSame(
            [
                'grocer-accessory-none',
                'grocer-accessory-01',
            ],
            $this->registry->variants(
                'class_accessory',
                'fructan',
                'grocer'
            )
        );
    }

    public function testNextWrapsToTheFirstVariant(): void
    {
        self::assertSame(
            'eyes-round-01',
            $this->registry->next(
                'eyes',
                'eyes-determined-01'
            )
        );
    }

    public function testPreviousWrapsToTheLastVariant(): void
    {
        self::assertSame(
            'mouth-grin-01',
            $this->registry->previous(
                'mouth',
                'mouth-neutral-01'
            )
        );
    }

    public function testUnknownCurrentVariantReturnsTheDefault(): void
    {
        self::assertSame(
            'frame-guild-gold-01',
            $this->registry->next(
                'frame',
                'unknown-frame'
            )
        );
    }

    public function testSupportsRejectsUnavailableVariant(): void
    {
        self::assertFalse(
            $this->registry->supports(
                'outfit',
                'grocer-outfit-03',
                'fructan',
                'grocer'
            )
        );
    }
}
