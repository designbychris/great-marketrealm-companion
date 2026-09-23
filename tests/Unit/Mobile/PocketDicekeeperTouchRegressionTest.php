<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketDicekeeperTouchRegressionTest extends TestCase
{
    private function source(): string
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        return $source;
    }

    public function testAbilityChecksUseSharedSecureRollerAndHistory(): void
    {
        $source = $this->source();
        self::assertStringContainsString("tray.rollAbility=(name,mod)=>{tray.open=true;performRoll(20,1,mod,name+' check · 1d20')", $source);
        self::assertStringContainsString("quick.addEventListener('click',()=>diceTray.rollAbility(label,mod))", $source);
        self::assertStringContainsString('values.push(pocketSecureDie(die))', $source);
        self::assertStringContainsString('while(history.children.length>6)', $source);
    }

    public function testAbilityModifierAndTouchControlsAreBounded(): void
    {
        $source = $this->source();
        self::assertStringContainsString('Math.floor((n-10)/2)', $source);
        self::assertStringContainsString('addStepper(count,1,20)', $source);
        self::assertStringContainsString('addStepper(modifier,-999,999)', $source);
        self::assertStringContainsString('Number.isSafeInteger(mod)', $source);
    }

    public function testNaturalRollFeedbackRespectsReducedMotion(): void
    {
        $source = $this->source();
        self::assertStringContainsString("values[0]===20||values[0]===1", $source);
        self::assertStringContainsString('prefers-reduced-motion: reduce', $source);
        self::assertStringContainsString('gmrc-pocket-dice__celebration--animated', $source);
    }
}
