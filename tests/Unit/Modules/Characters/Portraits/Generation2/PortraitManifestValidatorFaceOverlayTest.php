<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\PortraitManifestValidator;
use PHPUnit\Framework\TestCase;

final class PortraitManifestValidatorFaceOverlayTest extends TestCase
{
    public function testFaceOverlayIsAValidGenerationTwoSlot(): void
    {
        $directory = sys_get_temp_dir()
            . '/gmrc-face-overlay-'
            . bin2hex(random_bytes(5));

        mkdir($directory, 0777, true);

        file_put_contents(
            $directory . '/eyelids.svg',
            '<svg viewBox="0 0 480 600"></svg>'
        );

        $errors = (
            new PortraitManifestValidator()
        )->validate(
            [
                'schema_version' => '2.0',
                'id' => 'test-face-overlay',
                'type' => 'shared',
                'label' => 'Face Overlay',
                'assets' => [
                    [
                        'id' => 'g2-test-eyelids',
                        'slot' => 'face_overlay',
                        'file' => 'eyelids.svg',
                        'label' => 'Eyelids',
                    ],
                ],
            ],
            $directory
        );

        @unlink($directory . '/eyelids.svg');
        @rmdir($directory);

        self::assertSame([], $errors);
    }
}
