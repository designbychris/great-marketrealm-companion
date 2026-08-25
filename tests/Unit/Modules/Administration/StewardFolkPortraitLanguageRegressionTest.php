<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardFolkPortraitLanguageRegressionTest extends TestCase
{
    public function testPieSpeakIsCanonicalCharacterLanguage(): void
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/app/Modules/Characters/Models/ValueObjects/Language.php');

        self::assertIsString($source);
        self::assertStringContainsString("'piespeak' => 'PieSpeak'", $source);
    }

    public function testFolkWorkshopPersistsOptionalPortraitIdentity(): void
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/app/Modules/Administration/Workshop/FolkWorkshop.php');
        $view = file_get_contents($root . '/app/Modules/Administration/Views/folk-workshop.php');

        self::assertIsString($source);
        self::assertIsString($view);
        self::assertStringContainsString("'portrait_url' => \$portraitUrl", $source);
        self::assertStringContainsString('name="portrait_url"', $view);
        self::assertStringContainsString('Default Folk portrait image URL', $view);
    }

    public function testPortraitRendererUsesPublishedStewardDefaultWithoutOverridingCharacterUpload(): void
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/app/Modules/Characters/Portraits/Services/PortraitRenderer.php');

        self::assertIsString($source);
        self::assertStringContainsString('stewardDefaultPortraitUrl', $source);
        self::assertStringContainsString("if (\$mode === 'generated')", $source);
        self::assertStringContainsString("(\$record['status'] ?? '') !== 'published'", $source);
    }
}
