<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\FilesystemPortraitManifestRepository;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\PortraitManifestValidator;
use PHPUnit\Framework\TestCase;

final class FilesystemPortraitManifestRepositoryTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir()
            . '/gmrc-g2-repository-'
            . bin2hex(random_bytes(5));

        mkdir($this->directory . '/Race/Assets', 0777, true);

        file_put_contents(
            $this->directory . '/Race/Assets/body.svg',
            '<svg viewBox="0 0 480 600"></svg>'
        );

        file_put_contents(
            $this->directory . '/Race/manifest.json',
            json_encode(
                [
                    'schema_version' => '2.0',
                    'id' => 'race-test',
                    'type' => 'race',
                    'label' => 'Test Race',
                    'defaults' => [
                        'body_base' => 'g2-test-body-01',
                    ],
                    'assets' => [
                        [
                            'id' => 'g2-test-body-01',
                            'slot' => 'body_base',
                            'file' => 'Assets/body.svg',
                            'label' => 'Test body',
                        ],
                    ],
                ],
                JSON_THROW_ON_ERROR
            )
        );
    }

    protected function tearDown(): void
    {
        @unlink($this->directory . '/Race/manifest.json');
        @unlink($this->directory . '/Race/Assets/body.svg');
        @rmdir($this->directory . '/Race/Assets');
        @rmdir($this->directory . '/Race');
        @rmdir($this->directory);
    }

    public function testItDiscoversAndLoadsValidManifests(): void
    {
        $repository = new FilesystemPortraitManifestRepository(
            $this->directory,
            new PortraitManifestValidator()
        );

        $manifests = $repository->all();

        self::assertCount(1, $manifests);
        self::assertSame('race-test', $manifests[0]->id());
        self::assertSame(
            'g2-test-body-01',
            $manifests[0]->assets()[0]->id()
        );
    }

    public function testItFindsAManifestById(): void
    {
        $repository = new FilesystemPortraitManifestRepository(
            $this->directory,
            new PortraitManifestValidator()
        );

        self::assertNotNull($repository->find('race-test'));
        self::assertNull($repository->find('missing'));
    }
}
