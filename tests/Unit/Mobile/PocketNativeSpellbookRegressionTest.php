<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeSpellbookRegressionTest extends TestCase
{
    public function testNativeSpellbookConsumesTheAuthoritativePocketProjection(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($api);
        self::assertIsString($client);
        self::assertStringContainsString("'spellbook' => \$spellRows", $api);
        self::assertStringContainsString("'spellcasting' => \$castingMeasures", $api);
        self::assertStringContainsString('function spellbookPanel(character)', $client);
        self::assertStringContainsString('Array.isArray(character.spellbook)', $client);
        self::assertStringContainsString('character.spellcasting || {}', $client);
        self::assertStringNotContainsString("placeholderPanel('Spellbook'", $client);
    }

    public function testNativeSpellbookPresentsCanonicalSpellDetailsWithoutInventingMechanics(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString('spell.casting_time', $client);
        self::assertStringContainsString('spell.range', $client);
        self::assertStringContainsString('spell.components', $client);
        self::assertStringContainsString('spell.duration', $client);
        self::assertStringContainsString('spell.rules_text', $client);
        self::assertStringContainsString('spell.higher_levels', $client);
        self::assertStringContainsString("spell.resolved === false", $client);
    }

    public function testSpellSlotReservesUseTheExistingOwnerScopedLiveEndpoint(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString("/spell-slots'", $api);
        self::assertStringContainsString('function persistSpellSlot(character, slot, action)', $client);
        self::assertStringContainsString('expected_remaining: slot.remaining', $client);
        self::assertStringContainsString("button.dataset.slotAction", $client);
        self::assertStringContainsString("data-slot-action=\"spend\"", $client);
        self::assertStringContainsString("data-slot-action=\"recover\"", $client);
        self::assertStringNotContainsString('localStorage', $client);
    }

    public function testSpellAttackUsesTheSharedDiceworksAndAuthoritativeBonus(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString('casting.attack_bonus', $client);
        self::assertStringContainsString("performTrainingRoll('Spell Attack', Number(casting.attack_bonus), 'spell-attack')", $client);
        self::assertStringContainsString('rollD20Mode(diceMode)', $client);
        self::assertStringContainsString('diceHistory.unshift', $client);
    }
}
