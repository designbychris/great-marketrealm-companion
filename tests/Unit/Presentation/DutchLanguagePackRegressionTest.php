<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class DutchLanguagePackRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_dutch_po_and_mo_catalogues_are_shipped(): void
    {
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-nl_NL.po');
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-nl_NL.mo');
        self::assertGreaterThan(100, filesize($this->root . '/languages/great-marketrealm-companion-nl_NL.mo'));
    }

    public function test_dutch_catalogue_translates_representative_live_play_interface_strings(): void
    {
        $po = file_get_contents($this->root . '/languages/great-marketrealm-companion-nl_NL.po');
        self::assertIsString($po);
        self::assertStringContainsString("msgid \"Current HP\"\nmsgstr \"Huidige HP\"", $po);
        self::assertStringContainsString("msgid \"Saving Throws\"\nmsgstr \"Reddingsworpen\"", $po);
        self::assertStringContainsString("msgid \"Print / Save PDF\"\nmsgstr \"Afdrukken / PDF opslaan\"", $po);
        self::assertStringContainsString("msgid \"The Dice Ledger has been cleared.\"\nmsgstr \"Het dobbelgrootboek is gewist.\"", $po);
    }

    public function test_dutch_pack_keeps_canonical_content_translation_separate(): void
    {
        $guide = file_get_contents($this->root . '/docs/Dutch-Language-Pack.md');
        self::assertIsString($guide);
        self::assertStringContainsString('Canonical authored MarketRealm content remains separate.', $guide);
        self::assertStringContainsString('Do not add locale branches', $guide);
    }
}
