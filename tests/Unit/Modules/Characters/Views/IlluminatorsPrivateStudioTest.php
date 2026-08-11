<?php
declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;
use PHPUnit\Framework\TestCase;

final class IlluminatorsPrivateStudioTest extends TestCase
{
    public function testLedgerIsReadOnlyButKeepsUploadWorkbench(): void
    {
        $root = dirname(__DIR__, 5);
        $show = file_get_contents($root . '/app/Modules/Characters/Views/show.php');
        self::assertIsString($show);
        self::assertStringContainsString("'controlsEnabled' => false", $show);
        self::assertStringContainsString("'components.media.illuminator-workbench'", $show);
    }

    public function testEditHostsPrivateStudioAndPortraitFields(): void
    {
        $root = dirname(__DIR__, 5);
        $edit = file_get_contents($root . '/app/Modules/Characters/Views/edit.php');
        self::assertIsString($edit);
        self::assertStringContainsString('gmrc-private-studio', $edit);
        self::assertStringContainsString("'controlsEnabled' => true", $edit);
        self::assertStringContainsString('data-private-studio-controls', $edit);
        self::assertStringContainsString('name="portrait_seed"', $edit);
    }
}
