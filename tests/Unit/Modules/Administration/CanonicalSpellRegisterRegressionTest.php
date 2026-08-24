<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class CanonicalSpellRegisterRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testStewardProviderRegistersSpellActionsAndSection(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('CanonicalSpellRegister::class', $source);
        self::assertStringContainsString('admin_post_gmrc_save_canonical_spell', $source);
        self::assertStringContainsString('admin_post_gmrc_reset_canonical_spell', $source);
        self::assertStringContainsString("\$section === 'canonical-spells'", $source);
    }

    public function testCanonicalRegisterWrapsImmutableHandbookSpellSource(): void
    {
        $source = $this->source('app/Modules/Library/Spells/Repositories/CanonicalSpellRegister.php');
        self::assertStringContainsString('HandbookSpellRegister', $source);
        self::assertStringContainsString('gmrc_canonical_spell_overrides', $source);
        self::assertStringContainsString("'name' => sanitize_text_field", $source);
        self::assertStringContainsString("'variant_texts' => \$cleanTexts", $source);
        self::assertStringContainsString("'steward_notes' => sanitize_textarea_field", $source);
    }

    public function testMechanicalSpellIdentityRemainsProtected(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-spells.php');
        self::assertStringContainsString('Protected spell identity', $view);
        self::assertStringContainsString('Stable identity protected', $view);
        foreach (['kind', 'original_spell', 'level', 'school', 'access_labels'] as $field) {
            self::assertStringNotContainsString('name="' . $field . '"', $view);
        }
    }

    public function testHandbookSourceVariantsRemainIndependentlyEditable(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-spells.php');
        $register = $this->source('app/Modules/Library/Spells/Repositories/CanonicalSpellRegister.php');
        self::assertStringContainsString('variant_texts[', $view);
        self::assertStringContainsString('source_variant', $view);
        self::assertStringContainsString("\$variant['source_text']", $view);
        self::assertStringContainsString("\$variant['source_variant']", $register);
    }

    public function testSpellWritesUseRecordSpecificNoncesAndRestoreBaseline(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-spells.php');
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        $register = $this->source('app/Modules/Library/Spells/Repositories/CanonicalSpellRegister.php');
        self::assertStringContainsString('gmrc_save_canonical_spell_', $view);
        self::assertStringContainsString('gmrc_reset_canonical_spell_', $view);
        self::assertStringContainsString("check_admin_referer('gmrc_save_canonical_spell_'", $provider);
        self::assertStringContainsString("check_admin_referer('gmrc_reset_canonical_spell_'", $provider);
        self::assertStringContainsString('unset($overrides[$spell->key()])', $register);
    }

    public function testGuildSpellbookAndCharacterReferenceResolverUseCanonicalOverlay(): void
    {
        foreach ([
            'app/Modules/Library/Spells/Services/SpellbookPresenter.php',
            'app/Modules/Library/Catalogues/SpellReferenceCatalogue.php',
            'app/Modules/Characters/Arcana/Services/CanonicalSpellReferenceResolver.php',
        ] as $path) {
            $source = $this->source($path);
            self::assertStringContainsString('CanonicalSpellRegister', $source);
            self::assertStringNotContainsString('new HandbookSpellRegister()', $source);
        }
    }

    public function testStewardsOfficeLinksToSearchableSpellRegister(): void
    {
        $office = $this->source('app/Modules/Administration/Views/stewards-office.php');
        $view = $this->source('app/Modules/Administration/Views/canonical-spells.php');
        $script = $this->source('assets/js/admin/canonical-spells.js');
        self::assertStringContainsString('Canonical Spells', $office);
        self::assertStringContainsString("'section' => 'canonical-spells'", $office);
        self::assertStringContainsString('Open Spell Register', $office);
        self::assertStringContainsString('data-gmrc-spell-filter', $view);
        self::assertStringContainsString('aria-current="page"', $view);
        self::assertStringContainsString("addEventListener('input'", $script);
    }

    public function testSourceGapsRemainVisibleAndOptionReadsAreUnitTestSafe(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-spells.php');
        $register = $this->source('app/Modules/Library/Spells/Repositories/CanonicalSpellRegister.php');
        self::assertStringContainsString('Handbook source gaps', $view);
        self::assertStringContainsString('sourceIssues()', $view);
        self::assertStringContainsString("\\function_exists('get_option')", $register);
        self::assertStringContainsString('\\get_option(self::OPTION, [])', $register);
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }
}
