<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Integration;

use PHPUnit\Framework\TestCase;

final class TabletopCharacterBridgeRegressionTest extends TestCase
{
    private function root(string $path): string
    {
        return dirname(__DIR__, 5) . '/' . $path;
    }

    public function testCharactersProviderRegistersOwnerScopedTabletopBridge(): void
    {
        $source = file_get_contents($this->root('app/Modules/Characters/CharactersServiceProvider.php'));
        self::assertStringContainsString('gmrc_tabletop_owned_characters', $source);
        self::assertStringContainsString('gmrc_tabletop_owned_character', $source);
    }

    public function testBridgeProjectsForgedTokenWithoutChangingCharacterOwnership(): void
    {
        $source = file_get_contents($this->root('app/Modules/Characters/Services/TabletopCharacterBridge.php'));
        self::assertStringContainsString('allForOwner($userId)', $source);
        self::assertStringContainsString('findForOwner(', $source);
        self::assertStringContainsString("'token' => [", $source);
    }

    public function testBridgePreservesCharacterOwnerWhenResolvingPortraitArtwork(): void
    {
        $bridge = file_get_contents($this->root('app/Modules/Characters/Services/TabletopCharacterBridge.php'));
        $renderer = file_get_contents($this->root('app/Modules/Characters/Portraits/Services/PortraitRenderer.php'));
        $repository = file_get_contents($this->root('app/Modules/Characters/Portraits/Repositories/CharacterPortraitRepository.php'));

        self::assertStringContainsString('forCharacterForOwner($character, $ownerId)', $bridge);
        self::assertStringContainsString('findForOwner($character->id(), $ownerId)', $renderer);
        self::assertStringContainsString("'author' => \$ownerId", $repository);
    }

    public function testBridgeProjectsTheAdventurersTabletopPlaySnapshot(): void
    {
        $bridge = file_get_contents($this->root('app/Modules/Characters/Services/TabletopCharacterBridge.php'));

        self::assertStringContainsString("'play' => [", $bridge);
        self::assertStringContainsString("'armour_class' =>", $bridge);
        self::assertStringContainsString("'hit_points' => [", $bridge);
        self::assertStringContainsString("'passive_perception' =>", $bridge);
        self::assertStringContainsString("'abilities' => \$abilityProjection", $bridge);
        self::assertStringContainsString("'saving_throws' => \$savingThrowProjection", $bridge);
        self::assertStringContainsString("'skills' => \$skillProjection", $bridge);
    }
}
