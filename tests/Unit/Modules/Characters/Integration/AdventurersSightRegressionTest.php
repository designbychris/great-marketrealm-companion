<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Integration;

use PHPUnit\Framework\TestCase;

final class AdventurersSightRegressionTest extends TestCase
{
    public function testTabletopProjectionCarriesCompanionCertifiedDarkvision(): void
    {
        $bridge = file_get_contents(dirname(__DIR__, 5) . '/app/Modules/Characters/Services/TabletopCharacterBridge.php');
        self::assertIsString($bridge);
        self::assertStringContainsString("'senses' => [", $bridge);
        self::assertStringContainsString("'darkvision' => \$darkvision", $bridge);
        self::assertStringContainsString('\$this->races->get(\$character->race()->value())', $bridge);
        self::assertStringContainsString("hardened-rind-and-cave-hunter", $bridge);
        self::assertStringContainsString('\$racialDarkvision + 30', $bridge);
    }
}
