<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitAssetDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitManifest;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\Generation2CollectionResolver;
use PHPUnit\Framework\TestCase;

final class Generation2CollectionResolverBenchmarkTest extends TestCase
{
    public function testItIncludesEveryAssetWithinTheSameSlot(): void
    {
        $repository = new class implements
            PortraitManifestRepositoryInterface {
            /**
             * @var array<string,PortraitManifest>
             */
            private array $items;

            public function __construct()
            {
                $this->items = [];

                foreach (
                    [
                        'shared-backgrounds',
                        'shared-faces',
                        'shared-effects',
                        'shared-frames',
                        'race-fructan',
                        'class-grocer',
                        'collection-fructan-grocer',
                    ] as $id
                ) {
                    $assets = [];

                    if ($id === 'race-fructan') {
                        $assets = [
                            new PortraitAssetDefinition(
                                'g2-body-base',
                                'body_base',
                                '/tmp/base.svg',
                                'Base'
                            ),
                            new PortraitAssetDefinition(
                                'g2-highlight-one',
                                'body_highlight',
                                '/tmp/one.svg',
                                'One'
                            ),
                            new PortraitAssetDefinition(
                                'g2-highlight-two',
                                'body_highlight',
                                '/tmp/two.svg',
                                'Two'
                            ),
                        ];
                    }

                    $this->items[$id] = new PortraitManifest(
                        $id,
                        'shared',
                        $id,
                        '/tmp',
                        [],
                        [],
                        $assets
                    );
                }
            }

            public function all(): array
            {
                return array_values($this->items);
            }

            public function find(
                string $manifestId
            ): ?PortraitManifest {
                return $this->items[$manifestId] ?? null;
            }
        };

        $resolver = new Generation2CollectionResolver(
            $repository
        );

        self::assertSame(
            [
                'g2-body-base',
                'g2-highlight-one',
                'g2-highlight-two',
            ],
            $resolver->assetIds(
                'fructan',
                'grocer'
            )
        );
    }
}
