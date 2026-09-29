<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeFieldVitalityRegressionTest extends TestCase
{
    public function testNativeDashboardEstablishesFiveDestinationDockAndLiveOverview(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $css = file_get_contents($root . '/native/pocket-companion/src/native.css');

        foreach (['Overview', 'Character', 'Combat', 'Spellbook', 'More'] as $label) {
            self::assertStringContainsString("'{$label}'", $client);
        }
        self::assertStringContainsString("className = 'native-dashboard-dock'", $client);
        self::assertStringContainsString('.native-dashboard-dock', $css);
        self::assertStringContainsString("dataset.nativePanel = 'overview'", $client);
    }

    public function testNativeVitalityWritesUseBearerContractAndOptimisticConcurrency(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString('/vitality`', $client);
        self::assertStringContainsString('Authorization: `Bearer ${accessToken}`', $client);
        self::assertStringContainsString('expected_current: hp.current', $client);
        self::assertStringContainsString('expected_temporary: hp.temporary', $client);
        self::assertStringContainsString("cache: 'no-store'", $client);
        self::assertStringNotContainsString('localStorage', $client);
    }

    public function testDamageAndHealingRulesRemainAuthoritativeOnServer(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString("in_array(\$input['action'], ['damage', 'heal'], true)", $api);
        self::assertStringContainsString('$absorbed = min($temporary, $input[\'amount\']);', $api);
        self::assertStringContainsString('$current = min($hp->maximum(), $current + $input[\'amount\']);', $api);
        self::assertStringContainsString("perform({ action: button.dataset.vitalityAction, amount: n });", $client);
        self::assertStringNotContainsString('Math.min(character.hp', $client);
    }

    public function testMaximumHpIsPresentedAsCertifiedReadOnlyMeasure(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertStringContainsString('<span>Maximum HP</span>', $client);
        self::assertStringContainsString('<small>Read-only</small>', $client);
        self::assertStringNotContainsString('id="native-maximum-hp"', $client);
    }

    public function testFellowshipAubyNotesUseCanonicalFaceInsteadOfEmojiShorthand(): void
    {
        $root = dirname(__DIR__, 3);
        foreach (['index.php', 'create.php', 'show.php'] as $view) {
            $source = file_get_contents($root . '/app/Modules/Parties/Views/' . $view);
            self::assertStringNotContainsString('🍆', $source);
            self::assertStringContainsString('assets/images/auby/auby-note-face.svg', $source);
        }
        $css = file_get_contents($root . '/assets/css/modules/parties/fellowship-register.css');
        self::assertStringContainsString('.gmrc-fellowship-auby-note__seal img', $css);
    }
}
