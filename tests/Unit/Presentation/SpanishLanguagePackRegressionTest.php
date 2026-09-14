<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class SpanishLanguagePackRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_spanish_catalogue_is_bundled_and_compiled(): void
    {
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-es_ES.po');
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-es_ES.mo');
        self::assertGreaterThan(100, filesize($this->root . '/languages/great-marketrealm-companion-es_ES.mo'));
    }

    public function test_spanish_catalogue_translates_representative_companion_interface_strings(): void
    {
        $po = (string) file_get_contents($this->root . '/languages/great-marketrealm-companion-es_ES.po');

        self::assertStringContainsString("msgid \"Adventuring Sheet\"\nmsgstr \"Hoja de aventura\"", $po);
        self::assertStringContainsString("msgid \"Interface language\"\nmsgstr \"Idioma de la interfaz\"", $po);
        self::assertStringContainsString("msgid \"Return to Register\"\nmsgstr \"Volver al Registro\"", $po);
    }
}
