<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class HeritageAvailabilityTest extends TestCase
{
    public function testHeritageControlHasStableAvailabilityWrapper(): void
    {
        $source = $this->source('app/Modules/Characters/Views/create.php');

        self::assertStringContainsString('data-heritage-selector', $source);
    }

    public function testJavascriptHidesAndDisablesSelectorWithoutChoices(): void
    {
        $source = $this->source('assets/js/modules/characters/grand-catalogue.js');

        self::assertStringContainsString('const refreshHeritageAvailability = function ()', $source);
        self::assertStringContainsString("heritage.closest('[data-heritage-selector]')", $source);
        self::assertStringContainsString('wrapper.hidden = !hasHeritages;', $source);
        self::assertStringContainsString('heritage.disabled = !hasHeritages;', $source);
        self::assertStringContainsString('refreshHeritageAvailability();', $source);
    }

    private function source(string $relative): string
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents($root . '/' . $relative);
        self::assertIsString($source);

        return $source;
    }
}
