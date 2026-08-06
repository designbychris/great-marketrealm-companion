<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitAssetDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitManifest;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\Generation2CollectionResolver;
use PHPUnit\Framework\TestCase;

final class Generation2CollectionResolverTest extends TestCase
{
    public function testItSupportsTheFructanGrocerCollection(): void
    {
        $resolver = new Generation2CollectionResolver(
            $this->repository()
        );

        self::assertTrue(
            $resolver->supports('fructan', 'grocer')
        );

        self::assertFalse(
            $resolver->supports('rootkin', 'grocer')
        );
    }

    public function testItReturnsAssetsInLayerOrder(): void
    {
        $resolver = new Generation2CollectionResolver(
            $this->repository()
        );

        self::assertSame(
            [
                'g2-background',
                'g2-shadow',
                'g2-body',
                'g2-eyes',
                'g2-outfit',
                'g2-frame',
            ],
            $resolver->assetIds(
                'fructan',
                'grocer'
            )
        );
    }

    private function repository(): PortraitManifestRepositoryInterface
    {
        $manifests = [
            'shared-backgrounds' => $this->manifest(
                'shared-backgrounds',
                ['background' => 'g2-background']
            ),
            'shared-faces' => $this->manifest(
                'shared-faces',
                ['eyes' => 'g2-eyes']
            ),
            'shared-effects' => $this->manifest(
                'shared-effects'
            ),
            'shared-frames' => $this->manifest(
                'shared-frames',
                ['frame' => 'g2-frame']
            ),
            'race-fructan' => $this->manifest(
                'race-fructan',
                ['body_base' => 'g2-body']
            ),
            'class-grocer' => $this->manifest(
                'class-grocer',
                ['outfit_base' => 'g2-outfit']
            ),
            'collection-fructan-grocer' =>
                $this->manifest(
                    'collection-fructan-grocer',
                    [],
                    [
                        new PortraitAssetDefinition(
                            'g2-shadow',
                            'ground_shadow',
                            '/tmp/shadow.svg',
                            'Shadow'
                        ),
                    ]
                ),
        ];

        return new class($manifests)
            implements PortraitManifestRepositoryInterface {
                /**
                 * @param array<string,PortraitManifest> $manifests
                 */
                public function __construct(
                    private array $manifests
                ) {
                }

                public function all(): array
                {
                    return array_values(
                        $this->manifests
                    );
                }

                public function find(
                    string $manifestId
                ): ?PortraitManifest {
                    return $this->manifests[
                        $manifestId
                    ] ?? null;
                }
            };
    }

    /**
     * @param array<string,string> $defaults
     * @param array<int,PortraitAssetDefinition> $assets
     */
    private function manifest(
        string $id,
        array $defaults = [],
        array $assets = []
    ): PortraitManifest {
        return new PortraitManifest(
            $id,
            'shared',
            $id,
            '/tmp',
            $defaults,
            [],
            $assets
        );
    }
}
