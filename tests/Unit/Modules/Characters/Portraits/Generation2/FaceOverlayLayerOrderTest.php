<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitAssetDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitManifest;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\Generation2CollectionResolver;
use PHPUnit\Framework\TestCase;

final class FaceOverlayLayerOrderTest extends TestCase
{
    public function testFaceOverlayResolvesAfterEyesAndMouth(): void
    {
        $repository = new class implements
            PortraitManifestRepositoryInterface {
            private array $items = [];

            public function __construct()
            {
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

                    if ($id === 'shared-faces') {
                        $assets = [
                            new PortraitAssetDefinition(
                                'g2-eyes',
                                'eyes',
                                '/tmp/eyes.svg',
                                'Eyes'
                            ),
                            new PortraitAssetDefinition(
                                'g2-mouth',
                                'mouth',
                                '/tmp/mouth.svg',
                                'Mouth'
                            ),
                            new PortraitAssetDefinition(
                                'g2-eyelids',
                                'face_overlay',
                                '/tmp/eyelids.svg',
                                'Eyelids'
                            ),
                        ];
                    }

                    $this->items[$id] =
                        new PortraitManifest(
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
                return $this->items[$manifestId]
                    ?? null;
            }
        };

        $resolver = new Generation2CollectionResolver(
            $repository
        );

        $ids = $resolver->assetIds(
            'fructan',
            'grocer'
        );

        $eyes = array_search(
            'g2-eyes',
            $ids,
            true
        );

        $mouth = array_search(
            'g2-mouth',
            $ids,
            true
        );

        $overlay = array_search(
            'g2-eyelids',
            $ids,
            true
        );

        self::assertIsInt($eyes);
        self::assertIsInt($mouth);
        self::assertIsInt($overlay);

        self::assertLessThan(
            $overlay,
            $eyes
        );

        self::assertLessThan(
            $overlay,
            $mouth
        );
    }

    public function testManifestUsesFaceOverlayForEyelids(): void
    {
        $root = dirname(__DIR__, 6);

        $manifest = json_decode(
            (string) file_get_contents(
                $root
                . '/app/Modules/Characters/Portraits/'
                . 'Library/Generation2/Shared/Faces/'
                . 'manifest.json'
            ),
            true
        );

        self::assertIsArray($manifest);

        $eyelids = array_values(
            array_filter(
                $manifest['assets'] ?? [],
                static fn (array $asset): bool =>
                    ($asset['id'] ?? '')
                    === 'g2-eyelids-apple-closed-01'
            )
        );

        self::assertCount(1, $eyelids);

        self::assertSame(
            'face_overlay',
            $eyelids[0]['slot']
        );
    }
}
