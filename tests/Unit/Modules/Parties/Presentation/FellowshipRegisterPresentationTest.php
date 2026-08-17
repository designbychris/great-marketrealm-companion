<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Presentation;

use PHPUnit\Framework\TestCase;

final class FellowshipRegisterPresentationTest extends TestCase
{
    public function testFellowshipPresenterReusesEstablishedPortraitPipeline(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root
            . '/app/Modules/Parties/Presenters/'
            . 'FellowshipPresenter.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'PortraitRenderer $portraits',
            $source
        );
        self::assertStringContainsString(
            '->forCharacters($resolvedCharacters)',
            $source
        );
        self::assertStringNotContainsString(
            'PortraitRecipeGenerator',
            $source
        );
        self::assertStringNotContainsString(
            'PortraitSvgRenderer',
            $source
        );
    }

    public function testFellowshipPresenterResolvesMembershipsWithoutCopyingCharacters(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root
            . '/app/Modules/Parties/Presenters/'
            . 'FellowshipPresenter.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            '$party->memberships()',
            $source
        );
        self::assertStringContainsString(
            "'missing' =>",
            $source
        );
        self::assertStringContainsString(
            "'available' => \$available",
            $source
        );
        self::assertStringNotContainsString(
            '->save(',
            $source
        );
    }

    public function testPartyControllerSuppliesPresentationReadyFellowships(): void
    {
        $root = dirname(__DIR__, 5);
        $controller = file_get_contents(
            $root
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );

        self::assertIsString($controller);
        self::assertStringContainsString(
            'FellowshipPresenter $fellowships',
            $controller
        );
        self::assertStringContainsString(
            '$this->fellowships->presentMany(',
            $controller
        );
        self::assertStringContainsString(
            '$this->fellowships->present(',
            $controller
        );
    }

    public function testFellowshipPortraitCompositionSupportsGeneratedAndCustomPortraits(): void
    {
        $root = dirname(__DIR__, 5);
        $component = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'fellowship-portrait.php'
        );

        self::assertIsString($component);
        self::assertStringContainsString(
            '$portrait->isCustom()',
            $component
        );
        self::assertStringContainsString(
            '$portrait->attachmentUrl()',
            $component
        );
        self::assertStringContainsString(
            '$portrait->svg()',
            $component
        );
        self::assertStringContainsString(
            'gmrc-fellowship-portrait__member--leader',
            $component
        );
        self::assertStringContainsString(
            'Awaiting adventurers',
            $component
        );
    }

    public function testFellowshipRegisterUsesGuildLedgerAndCompanyPortraits(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Parties/Views/index.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'The Fellowship Register',
            $view
        );
        self::assertStringContainsString(
            'components.media.fellowship-portrait',
            $view
        );
        self::assertStringContainsString(
            'Form a Fellowship',
            $view
        );
        self::assertStringContainsString(
            'Several adventurers with snacks',
            $view
        );
        self::assertStringContainsString(
            'components.furniture.guild-page',
            $view
        );
        self::assertStringContainsString(
            'components.furniture.guild-ledger',
            $view
        );
    }

    public function testOpenFellowshipShowsPortraitRosterAndMemberActions(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );
        $member = file_get_contents(
            $root
            . '/app/Views/components/entries/'
            . 'fellowship-member.php'
        );

        self::assertIsString($view);
        self::assertIsString($member);
        self::assertStringContainsString(
            'components.media.fellowship-portrait',
            $view
        );
        self::assertStringContainsString(
            'Fellowship Roster',
            $view
        );
        self::assertStringContainsString(
            'Add an Adventurer',
            $view
        );
        self::assertStringContainsString(
            'components.entries.fellowship-member',
            $view
        );
        self::assertStringContainsString(
            'Open Ledger',
            $member
        );
        self::assertStringContainsString(
            'Save role',
            $member
        );
        self::assertStringContainsString(
            'Remove',
            $member
        );
    }

    public function testFellowshipsAreVisibleInMainNavigation(): void
    {
        $root = dirname(__DIR__, 5);
        $kingdom = file_get_contents(
            $root . '/app/Kingdoms/PartiesKingdom.php'
        );
        $icons = file_get_contents(
            $root . '/app/Navigation/Icons.php'
        );

        self::assertIsString($kingdom);
        self::assertIsString($icons);
        self::assertStringContainsString(
            "'Fellowships'",
            $kingdom
        );
        self::assertStringContainsString(
            'Icons::PARTY',
            $kingdom
        );
        self::assertStringContainsString(
            'public const PARTY',
            $icons
        );
        self::assertStringContainsString(
            "'parties'",
            $kingdom
        );
    }

    public function testFellowshipStylesAreEnqueuedAndResponsive(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($provider);
        self::assertIsString($css);
        self::assertStringContainsString(
            'gmrc-fellowship-register',
            $provider
        );
        self::assertStringContainsString(
            'modules/parties/fellowship-register.css',
            $provider
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-portrait__company',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 780px)',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-motion: no-preference)',
            $css
        );
    }
}
