<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\PortraitManifestValidator;
use PHPUnit\Framework\TestCase;

final class PortraitManifestValidatorTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir()
            . '/gmrc-g2-manifest-'
            . bin2hex(random_bytes(5));

        mkdir($this->directory, 0777, true);
        file_put_contents(
            $this->directory . '/asset.svg',
            '<svg viewBox="0 0 480 600"></svg>'
        );
    }

    protected function tearDown(): void
    {
        @unlink($this->directory . '/asset.svg');
        @rmdir($this->directory);
    }

    public function testItAcceptsAValidManifest(): void
    {
        $errors = (new PortraitManifestValidator())->validate(
            $this->manifest(),
            $this->directory
        );

        self::assertSame([], $errors);
    }

    public function testItRejectsMissingFiles(): void
    {
        $manifest = $this->manifest();
        $manifest['assets'][0]['file'] = 'missing.svg';

        $errors = (new PortraitManifestValidator())->validate(
            $manifest,
            $this->directory
        );

        self::assertContains(
            'Asset "g2-test-body-01" references a missing SVG file.',
            $errors
        );
    }

    public function testItRejectsUnsafePaths(): void
    {
        $manifest = $this->manifest();
        $manifest['assets'][0]['file'] = '../asset.svg';

        $errors = (new PortraitManifestValidator())->validate(
            $manifest,
            $this->directory
        );

        self::assertContains(
            'Asset "g2-test-body-01" has an unsafe file path.',
            $errors
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function manifest(): array
    {
        return [
            'schema_version' => '2.0',
            'id' => 'test-manifest',
            'type' => 'race',
            'label' => 'Test',
            'assets' => [
                [
                    'id' => 'g2-test-body-01',
                    'slot' => 'body_base',
                    'file' => 'asset.svg',
                    'label' => 'Test body',
                ],
            ],
        ];
    }
}
