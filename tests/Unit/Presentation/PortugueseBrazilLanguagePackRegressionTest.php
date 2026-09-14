<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class PortugueseBrazilLanguagePackRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_portuguese_brazil_catalogue_is_bundled_and_compiled(): void
    {
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-pt_BR.po');
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-pt_BR.mo');
        self::assertGreaterThan(100, filesize($this->root . '/languages/great-marketrealm-companion-pt_BR.mo'));
    }

    public function test_portuguese_brazil_catalogue_uses_brazilian_interface_vocabulary(): void
    {
        $po = (string) file_get_contents($this->root . '/languages/great-marketrealm-companion-pt_BR.po');

        self::assertStringContainsString("msgid \"Adventuring Sheet\"\nmsgstr \"Ficha de Aventura\"", $po);
        self::assertStringContainsString("msgid \"Return to Register\"\nmsgstr \"Voltar ao Registro\"", $po);
        self::assertStringContainsString("msgid \"Current adventuring record\"\nmsgstr \"Registro de aventura atual\"", $po);
        self::assertStringContainsString("msgid \"NPC\"\nmsgstr \"NPC\"", $po);
        self::assertStringContainsString("msgid \"Dice of Destiny selected. Roll 3d6 for each ability.\"\nmsgstr \"Dados do Destino selecionados. Role 3d6 para cada atributo.\"", $po);
    }
}
