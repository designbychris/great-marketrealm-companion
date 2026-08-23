<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class BackgroundMechanicsBridgeRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testStewardCanValidateAndOverrideFutureBackgroundProficiencies(): void
    {
        $source = $this->source('app/Modules/Administration/CanonicalRecords/CanonicalBackgroundRegister.php');
        self::assertStringContainsString("'skills' => \$skills", $source);
        self::assertStringContainsString("'tools' => \$tools", $source);
        self::assertStringContainsString('Choose exactly two certified Background skill proficiencies.', $source);
        self::assertStringContainsString('ToolProficiency::supports', $source);
    }

    public function testCharacterCreationReadsCurrentCanonicalBackgroundMechanics(): void
    {
        $trait = $this->source('app/Modules/Characters/Requests/Concerns/ResolvesRegistrationInput.php');
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');
        self::assertStringContainsString('BackgroundMechanicsRegister', $trait);
        self::assertStringContainsString('Background::fromStringWithMechanics', $controller);
        self::assertStringContainsString("\$registration['background_skills']", $controller);
        self::assertStringContainsString("\$registration['background_tools']", $controller);
    }

    public function testEditingAnExistingCharacterPreservesItsSnapshotUnlessBackgroundChanges(): void
    {
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');
        self::assertStringContainsString("\$currentBackground->value() === \$data['background']", $controller);
        self::assertStringContainsString('? $currentBackground', $controller);
        self::assertStringContainsString("new BackgroundMechanicsRegister())->background(\$data['background'])", $controller);
    }

    public function testCharacterRepositoryPersistsBackgroundMechanicsSnapshot(): void
    {
        $source = $this->source('app/Modules/Characters/Repositories/CharacterRepository.php');
        self::assertStringContainsString('_gmrc_background_skills', $source);
        self::assertStringContainsString('_gmrc_background_tools', $source);
        self::assertStringContainsString('mechanicsSnapshot()', $source);
        self::assertStringContainsString('Background::fromStringWithMechanics', $source);
    }

    public function testLegacyCharactersNeverResolveLaterStewardOverrides(): void
    {
        $source = $this->source('app/Modules/Characters/Repositories/CharacterRepository.php');
        self::assertStringContainsString('Legacy Characters intentionally resolve the immutable bundled', $source);
        self::assertStringContainsString('return Background::fromString($stored);', $source);
        self::assertStringNotContainsString('BackgroundMechanicsRegister', $source);
    }

    public function testGuildLibraryAndCreatorShareResolvedBackgroundRegister(): void
    {
        $catalogue = $this->source('app/Modules/Library/Catalogues/BackgroundReferenceCatalogue.php');
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');
        self::assertStringContainsString('BackgroundMechanicsRegister', $catalogue);
        self::assertStringContainsString('new BackgroundMechanicsRegister()', $controller);
    }

    public function testStewardUiExplainsHistoricalProtectionAndAllowsValidatedMechanics(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-backgrounds.php');
        self::assertStringContainsString('Future-character proficiencies', $view);
        self::assertStringContainsString('name="skills[]"', $view);
        self::assertStringContainsString('name="tools[]"', $view);
        self::assertStringContainsString('Historical protection', $view);
        self::assertStringNotContainsString('Phase III.', $view);
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }
}
