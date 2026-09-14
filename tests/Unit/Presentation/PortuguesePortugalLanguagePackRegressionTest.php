<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class PortuguesePortugalLanguagePackRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_portuguese_portugal_catalogue_is_bundled_and_compiled(): void
    {
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-pt_PT.po');
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-pt_PT.mo');
        self::assertGreaterThan(100, filesize($this->root . '/languages/great-marketrealm-companion-pt_PT.mo'));
    }

    public function test_portuguese_portugal_catalogue_translates_representative_companion_interface_strings(): void
    {
        $po = (string) file_get_contents($this->root . '/languages/great-marketrealm-companion-pt_PT.po');

        self::assertStringContainsString("msgid \"Adventuring Sheet\"\nmsgstr \"Ficha de Aventura\"", $po);
        self::assertStringContainsString("msgid \"Interface language\"\nmsgstr \"Idioma da interface\"", $po);
        self::assertStringContainsString("msgid \"Return to Register\"\nmsgstr \"Voltar ao Registo\"", $po);
    }
}
