<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class GermanLanguagePackRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_german_po_and_mo_catalogues_are_shipped(): void
    {
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-de_DE.po');
        self::assertFileExists($this->root . '/languages/great-marketrealm-companion-de_DE.mo');
        self::assertGreaterThan(100, filesize($this->root . '/languages/great-marketrealm-companion-de_DE.mo'));
    }

    public function test_german_catalogue_translates_representative_live_play_interface_strings(): void
    {
        $po = file_get_contents($this->root . '/languages/great-marketrealm-companion-de_DE.po');
        self::assertIsString($po);
        self::assertStringContainsString("msgid \"Current HP\"\nmsgstr \"Aktuelle TP\"", $po);
        self::assertStringContainsString("msgid \"Saving Throws\"\nmsgstr \"Rettungswürfe\"", $po);
        self::assertStringContainsString("msgid \"Print / Save PDF\"\nmsgstr \"Drucken / PDF speichern\"", $po);
    }
}
