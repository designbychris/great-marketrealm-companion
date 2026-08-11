<?php
declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Scripts;
use PHPUnit\Framework\TestCase;

final class PrivateStudioControlsMountTest extends TestCase
{
    public function testReadOnlyPortraitSkipsControlsAndPrivatePaneReceivesThem(): void
    {
        $root = dirname(__DIR__, 5);
        $app = file_get_contents($root . '/assets/js/components/media/portrait-studio/app.js');
        $controls = file_get_contents($root . '/assets/js/components/media/portrait-studio/controls.js');
        self::assertIsString($app);
        self::assertIsString($controls);
        self::assertStringContainsString("portraitControls === 'false'", $app);
        self::assertStringContainsString('[data-private-studio-controls]', $controls);
        self::assertStringContainsString('privateControls.replaceChildren(', $controls);
    }
}
