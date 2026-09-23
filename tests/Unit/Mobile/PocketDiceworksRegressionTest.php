<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketDiceworksRegressionTest extends TestCase
{
    public function testPocketDiceworksKeepsSupportedDiceAndSecureRejectionSampling(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        self::assertStringContainsString('[4,6,8,10,12,20,100]', $source);
        self::assertStringContainsString('cryptoSource.getRandomValues(values)', $source);
        self::assertStringContainsString('while(value>=limit)', $source);
        self::assertStringNotContainsString('Math.random()', $source);
    }

    public function testDiceTrayHasBoundedInputsAndSixRollHistory(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertStringContainsString("field('Number of dice',1,1,20)", $source);
        self::assertStringContainsString("field('Modifier',0,-999,999)", $source);
        self::assertStringContainsString('while(history.children.length>6)', $source);
        self::assertStringContainsString('detail.append(abilities,pocketDiceworks())', $source);
    }
}
