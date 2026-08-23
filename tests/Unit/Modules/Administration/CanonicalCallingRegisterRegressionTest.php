<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class CanonicalCallingRegisterRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testStewardProviderRegistersCanonicalCallingActionsAndSection(): void
    {
        $source = file_get_contents($this->root . '/app/Providers/AdministrationServiceProvider.php');
        self::assertIsString($source);
        self::assertStringContainsString('CanonicalCallingRegister::class', $source);
        self::assertStringContainsString('admin_post_gmrc_save_canonical_calling', $source);
        self::assertStringContainsString('admin_post_gmrc_reset_canonical_calling', $source);
        self::assertStringContainsString("\$section === 'canonical-callings'", $source);
    }

    public function testRegisterUsesPlayersHandbookCatalogueAsBaseline(): void
    {
        $source = file_get_contents($this->root . '/app/Modules/Administration/CanonicalRecords/CanonicalCallingRegister.php');
        self::assertIsString($source);
        self::assertStringContainsString('CharacterCatalogueRepository', $source);
        self::assertStringContainsString("'classes'", $source);
        self::assertStringContainsString("'subclasses'", $source);
        self::assertStringContainsString('gmrc_canonical_calling_overrides', $source);
    }

    public function testCallingOverridesAreSeparateAndRestorable(): void
    {
        $source = file_get_contents($this->root . '/app/Modules/Administration/CanonicalRecords/CanonicalCallingRegister.php');
        self::assertIsString($source);
        self::assertStringContainsString("'name' => sanitize_text_field", $source);
        self::assertStringContainsString("'description' => sanitize_textarea_field", $source);
        self::assertStringContainsString("'steward_notes' => sanitize_textarea_field", $source);
        self::assertStringContainsString('unset($overrides[$this->identity($record)])', $source);
    }

    public function testCallingEditorProtectsCertifiedMechanicalIdentity(): void
    {
        $source = file_get_contents($this->root . '/app/Modules/Administration/Views/canonical-callings.php');
        self::assertIsString($source);
        self::assertStringContainsString('Mechanical identity remains read-only to protect existing characters.', $source);
        self::assertStringContainsString('Parentage remains read-only to protect existing characters.', $source);
        self::assertStringNotContainsString('name="hit_die"', $source);
        self::assertStringNotContainsString('name="parent"', $source);
    }

    public function testCallingWritesUseRecordSpecificNonces(): void
    {
        $view = file_get_contents($this->root . '/app/Modules/Administration/Views/canonical-callings.php');
        $provider = file_get_contents($this->root . '/app/Providers/AdministrationServiceProvider.php');
        self::assertIsString($view);
        self::assertIsString($provider);
        self::assertStringContainsString('gmrc_save_canonical_calling_', $view);
        self::assertStringContainsString('gmrc_reset_canonical_calling_', $view);
        self::assertStringContainsString("check_admin_referer('gmrc_save_canonical_calling_'", $provider);
        self::assertStringContainsString("check_admin_referer('gmrc_reset_canonical_calling_'", $provider);
    }

    public function testStewardsOfficeLinksToCallingRegister(): void
    {
        $source = file_get_contents($this->root . '/app/Modules/Administration/Views/stewards-office.php');
        self::assertIsString($source);
        self::assertStringContainsString('Canonical Callings', $source);
        self::assertStringContainsString("'section' => 'canonical-callings'", $source);
        self::assertStringContainsString('Open Calling Register', $source);
    }

    public function testCallingRegisterHasSearchableAccessibleRegister(): void
    {
        $view = file_get_contents($this->root . '/app/Modules/Administration/Views/canonical-callings.php');
        $script = file_get_contents($this->root . '/assets/js/admin/canonical-callings.js');
        self::assertIsString($view);
        self::assertIsString($script);
        self::assertStringContainsString('gmrc-calling-register-title', $view);
        self::assertStringContainsString('data-gmrc-calling-filter', $view);
        self::assertStringContainsString('aria-current="page"', $view);
        self::assertStringContainsString("addEventListener('input'", $script);
    }
}
