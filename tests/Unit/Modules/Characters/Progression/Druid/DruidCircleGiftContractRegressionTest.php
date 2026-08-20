<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Druid;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\DruidCircleGiftProgression;
use PHPUnit\Framework\TestCase;

final class DruidCircleGiftContractRegressionTest extends TestCase
{
    public function testEveryDruidCircleDefinitionSatisfiesSharedGiftContract(): void
    {
        $definitions =
            DruidCircleGiftProgression::allDefinitions();

        self::assertCount(
            6,
            $definitions
        );

        foreach ($definitions as $definition) {
            self::assertInstanceOf(
                PathGiftProgressionDefinitionInterface::class,
                $definition
            );

            self::assertNotSame(
                '',
                $definition->pathKey()
            );

            self::assertTrue(
                $definition->supports(
                    $definition->pathKey()
                )
            );

            self::assertNotSame(
                '',
                $definition->pathLabel()
            );

            self::assertCount(
                4,
                $definition->gifts()
            );
        }
    }

    public function testCirclePathKeyReturnsCertifiedCatalogueKey(): void
    {
        self::assertSame(
            'circle-of-the-compost',
            DruidCircleGiftProgression::forCircle(
                'circle-of-the-compost'
            )->pathKey()
        );
    }
}
