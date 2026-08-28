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
}
