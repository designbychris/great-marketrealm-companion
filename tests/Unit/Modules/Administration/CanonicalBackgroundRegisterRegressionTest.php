<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class CanonicalBackgroundRegisterRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testStewardProviderRegistersBackgroundRegisterActionsAndSection(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('CanonicalBackgroundRegister::class', $source);
        self::assertStringContainsString('admin_post_gmrc_save_canonical_background', $source);
        self::assertStringContainsString('admin_post_gmrc_reset_canonical_background', $source);
        self::assertStringContainsString("\$section === 'canonical-backgrounds'", $source);
    }

    public function testRegisterUsesExistingHandbookBackgroundSource(): void
    {
        $source = $this->source('app/Modules/Administration/CanonicalRecords/CanonicalBackgroundRegister.php');
        self::assertStringContainsString('HandbookBackgroundRegister', $source);
        self::assertStringContainsString('gmrc_canonical_background_overrides', $source);
        self::assertStringContainsString('The Great Marketrealm - Players Handbook', $source);
    }

    public function testStewardCanOverridePresentationAndRestoreBaseline(): void
    {
        $source = $this->source('app/Modules/Administration/CanonicalRecords/CanonicalBackgroundRegister.php');
        self::assertStringContainsString("'name' => sanitize_text_field", $source);
        self::assertStringContainsString("'feature_name' => sanitize_text_field", $source);
        self::assertStringContainsString("'feature_detail' => sanitize_textarea_field", $source);
        self::assertStringContainsString("'steward_notes' => sanitize_textarea_field", $source);
        self::assertStringContainsString('unset($overrides[$record->key()])', $source);
    }

    public function testBackgroundMechanicsBridgeAllowsValidatedFutureCharacterProficiencies(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-backgrounds.php');
        $register = $this->source('app/Modules/Administration/CanonicalRecords/CanonicalBackgroundRegister.php');
        self::assertStringContainsString('Future-character proficiencies', $view);
        self::assertStringContainsString('name="skills[]"', $view);
        self::assertStringContainsString('name="tools[]"', $view);
        self::assertStringContainsString("'skills' => \$skills", $register);
        self::assertStringContainsString("'tools' => \$tools", $register);
    }

    public function testBackgroundWritesUseRecordSpecificNonces(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-backgrounds.php');
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('gmrc_save_canonical_background_', $view);
        self::assertStringContainsString('gmrc_reset_canonical_background_', $view);
        self::assertStringContainsString("check_admin_referer('gmrc_save_canonical_background_'", $provider);
        self::assertStringContainsString("check_admin_referer('gmrc_reset_canonical_background_'", $provider);
    }

    public function testStewardsOfficeLinksToCanonicalBackgroundRegister(): void
    {
        $source = $this->source('app/Modules/Administration/Views/stewards-office.php');
        self::assertStringContainsString('Canonical Backgrounds', $source);
        self::assertStringContainsString("'section' => 'canonical-backgrounds'", $source);
        self::assertStringContainsString('Open Background Register', $source);
    }

    public function testBackgroundRegisterIsSearchableAndAccessible(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-backgrounds.php');
        $script = $this->source('assets/js/admin/canonical-backgrounds.js');
        self::assertStringContainsString('gmrc-background-steward-register-title', $view);
        self::assertStringContainsString('data-gmrc-background-filter', $view);
        self::assertStringContainsString('aria-current="page"', $view);
        self::assertStringContainsString("addEventListener('input'", $script);
    }

    public function testSourceGapsRemainVisibleRatherThanInvented(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-backgrounds.php');
        self::assertStringContainsString('Handbook source gaps', $view);
        self::assertStringContainsString('sourceIssues()', $view);
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }
}
