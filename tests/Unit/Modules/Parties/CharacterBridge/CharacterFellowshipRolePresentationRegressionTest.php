<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\CharacterBridge;

use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use PHPUnit\Framework\TestCase;

final class CharacterFellowshipRolePresentationRegressionTest extends TestCase
{
    public function testMembershipRoleProvidesPresentationLabel(): void
    {
        self::assertSame(
            'Leader',
            PartyMembershipRole::leader()->label()
        );

        self::assertSame(
            'Member',
            PartyMembershipRole::member()->label()
        );
    }

    public function testCharacterFellowshipCardUsesSupportedRoleContract(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            "->role()\n"
            . "                                                    ->label()",
            $view
        );

        $role = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Models/ValueObjects/'
            . 'PartyMembershipRole.php'
        );

        self::assertIsString($role);
        self::assertStringContainsString(
            'public function label(): string',
            $role
        );
    }

    public function testTemporaryLedgerLayoutWorkaroundHasBeenRemoved(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        $css = file_get_contents(
            $this->root()
            . '/assets/css/modules/characters/open-ledger.css'
        );

        self::assertIsString($view);
        self::assertIsString($css);

        self::assertStringNotContainsString(
            'data-character-ledger-boundary',
            $view
        );

        self::assertStringNotContainsString(
            'Phase III.11.3C.1 — Ledger Layout Repair',
            $css
        );

        self::assertStringNotContainsString(
            '.gmrc-open-ledger[data-character-ledger-boundary]',
            $css
        );
    }

    public function testCharacterLedgerSourceRemainsStructurallyBalanced(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        foreach ([
            'section',
            'div',
            'article',
            'aside',
            'form',
            'nav',
            'header',
        ] as $tag) {
            preg_match_all(
                '/<' . $tag . '\b/i',
                $view,
                $opening
            );

            preg_match_all(
                '/<\/' . $tag . '>/i',
                $view,
                $closing
            );

            self::assertSame(
                count($opening[0]),
                count($closing[0]),
                sprintf(
                    'Expected <%s> elements to remain balanced.',
                    $tag
                )
            );
        }
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}
