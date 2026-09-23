<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketSpellbookRegressionTest extends TestCase
{
    public function testSpellbookUsesCharacterOwnedSpellIdentitiesAndSharedRegister(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketApi.php');
        self::assertIsString($source);
        self::assertStringContainsString('$character->spellbook()', $source);
        self::assertStringContainsString('$spellRegister->find($identifier)', $source);
        self::assertStringContainsString("'spellbook' => \$spellRows", $source);
        self::assertStringContainsString("'resolved' => \$record !== null", $source);
    }

    public function testPocketSpellbookSharesDiceworksAndDoesNotSpendSlots(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        self::assertStringContainsString('pocketSpellbook(character,diceTray)', $source);
        self::assertStringContainsString('diceTray.rollDamage(', $source);
        self::assertStringContainsString('!spell.add_casting_modifier', $source);
        self::assertStringNotContainsString('spendSpellSlot(', $source);
    }
}
