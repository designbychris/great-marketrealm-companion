<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class InternationalisationFoundationRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function test_plugin_declares_and_loads_its_language_pack_directory(): void
    {
        $bootstrap = file_get_contents($this->root . '/great-marketrealm-companion.php');
        self::assertIsString($bootstrap);
        self::assertStringContainsString('Text Domain: great-marketrealm-companion', $bootstrap);
        self::assertStringContainsString('Domain Path: /languages', $bootstrap);
        self::assertStringContainsString("load_plugin_textdomain(\n            'great-marketrealm-companion'", $bootstrap);
        self::assertStringContainsString("dirname(plugin_basename(GMRC_PLUGIN_FILE)) . '/languages'", $bootstrap);
    }

    public function test_language_pack_contract_keeps_interface_and_authored_content_separate(): void
    {
        self::assertFileExists($this->root . '/languages/README.md');
        self::assertFileExists($this->root . '/docs/Internationalisation.md');

        $guide = file_get_contents($this->root . '/docs/Internationalisation.md');
        self::assertIsString($guide);
        self::assertStringContainsString('Interface translation', $guide);
        self::assertStringContainsString('Canonical content translation', $guide);
        self::assertStringContainsString('No code should test specifically for `nl_NL`', $guide);
    }
}
