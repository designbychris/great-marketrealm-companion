<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Hall;

use PHPUnit\Framework\TestCase;

final class FellowshipHallSealRegressionTest extends TestCase
{
    public function testHallProvidesFourAccessiblePrimaryTabs(): void
    {
        $show = $this->showView();

        self::assertStringContainsString(
            'role="tablist"',
            $show
        );
        self::assertStringContainsString(
            'aria-label="Fellowship Hall"',
            $show
        );

        foreach ([
            'overview',
            'chronicle',
            'treasury',
            'company',
        ] as $tab) {
            self::assertStringContainsString(
                'data-fellowship-tab="<?php echo esc_attr($hallTab); ?>"',
                $show
            );
            self::assertStringContainsString(
                "'{$tab}' =>",
                $show
            );
        }
    }

    public function testHallPanelsHaveTabPanelRelationships(): void
    {
        $show = $this->showView();

        foreach ([
            'overview',
            'chronicle',
            'treasury',
            'company',
        ] as $panel) {
            self::assertStringContainsString(
                'id="gmrc-fellowship-panel-' . $panel . '"',
                $show
            );
            self::assertStringContainsString(
                'aria-labelledby="gmrc-fellowship-tab-' . $panel . '"',
                $show
            );
            self::assertStringContainsString(
                'data-fellowship-panel="' . $panel . '"',
                $show
            );
        }
    }

    public function testOverviewKeepsIdentityAndCharterTogether(): void
    {
        $show = $this->showView();

        $overview = strpos(
            $show,
            'gmrc-fellowship-panel-overview'
        );
        $hero = strpos(
            $show,
            'gmrc-fellowship-hero'
        );
        $charter = strpos(
            $show,
            'gmrc-fellowship-charter'
        );
        $chroniclePanel = strpos(
            $show,
            'gmrc-fellowship-panel-chronicle'
        );

        self::assertIsInt($overview);
        self::assertIsInt($hero);
        self::assertIsInt($charter);
        self::assertIsInt($chroniclePanel);
        self::assertLessThan($hero, $overview);
        self::assertLessThan($charter, $hero);
        self::assertLessThan($chroniclePanel, $charter);
    }

    public function testChronicleAndTreasuryHaveDedicatedPanels(): void
    {
        $show = $this->showView();

        self::assertStringContainsString(
            'gmrc-fellowship-panel-chronicle',
            $show
        );
        self::assertStringContainsString(
            'class="gmrc-fellowship-chronicle"',
            $show
        );
        self::assertStringContainsString(
            'gmrc-fellowship-panel-treasury',
            $show
        );
        self::assertStringContainsString(
            'class="gmrc-fellowship-treasury"',
            $show
        );
    }

    public function testCompanyPanelContainsOfficesRosterAndRecruitment(): void
    {
        $show = $this->showView();

        $company = strpos(
            $show,
            'gmrc-fellowship-panel-company'
        );
        $offices = strpos(
            $show,
            'class="gmrc-fellowship-offices"'
        );
        $roster = strpos(
            $show,
            'class="gmrc-fellowship-roster"'
        );
        $recruit = strpos(
            $show,
            'class="gmrc-fellowship-recruit"'
        );

        self::assertIsInt($company);
        self::assertIsInt($offices);
        self::assertIsInt($roster);
        self::assertIsInt($recruit);
        self::assertLessThan($offices, $company);
        self::assertLessThan($roster, $offices);
        self::assertLessThan($recruit, $roster);
    }

    public function testTabsUseProgressiveEnhancementInsteadOfHidingHallWithoutJavascript(): void
    {
        $css = $this->css();

        self::assertStringContainsString(
            '.gmrc-fellowship-tabs:not(.is-ready)',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-tablist',
            $css
        );
        self::assertStringContainsString(
            'display: none;',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-tabs.is-ready',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-tabpanel[hidden]',
            $css
        );
    }

    public function testTabControllerSupportsKeyboardNavigation(): void
    {
        $js = $this->javascript();

        self::assertStringContainsString(
            "event.key === 'ArrowRight'",
            $js
        );
        self::assertStringContainsString(
            "event.key === 'ArrowLeft'",
            $js
        );
        self::assertStringContainsString(
            "event.key === 'Home'",
            $js
        );
        self::assertStringContainsString(
            "event.key === 'End'",
            $js
        );
        self::assertStringContainsString(
            'event.preventDefault();',
            $js
        );
        self::assertStringContainsString(
            'tab.focus();',
            $js
        );
    }

    public function testTabControllerMaintainsSelectedAndFocusableStates(): void
    {
        $js = $this->javascript();

        self::assertStringContainsString(
            "candidate.setAttribute(",
            $js
        );
        self::assertStringContainsString(
            "'aria-selected'",
            $js
        );
        self::assertStringContainsString(
            'candidate.tabIndex = active ? 0 : -1;',
            $js
        );
        self::assertStringContainsString(
            'panel.hidden = !active;',
            $js
        );
    }

    public function testHallRemembersSelectedTabAcrossFormRedirects(): void
    {
        $js = $this->javascript();

        self::assertStringContainsString(
            "'gmrc:fellowship-hall:' + fellowshipId + ':tab'",
            $js
        );
        self::assertStringContainsString(
            'window.localStorage.setItem(',
            $js
        );
        self::assertStringContainsString(
            'window.localStorage.getItem(storageKey)',
            $js
        );
        self::assertStringContainsString(
            "'gmrc_fellowship_tab'",
            $js
        );
    }

    public function testHallTabsAreResponsiveReducedMotionAndHighContrastAware(): void
    {
        $css = $this->css();

        self::assertStringContainsString(
            '@media (max-width: 640px)',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 420px)',
            $css
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
        self::assertStringContainsString(
            ':focus-visible',
            $css
        );
    }

    public function testFellowshipHallJavascriptIsEnqueuedWithFileVersioning(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            "'gmrc-fellowship-hall'",
            $provider
        );
        self::assertStringContainsString(
            "'assets/js/modules/parties/'",
            $provider
        );
        self::assertStringContainsString(
            "'fellowship-hall.js'",
            $provider
        );
        self::assertStringContainsString(
            'filemtime(',
            $provider
        );
    }

    public function testHallSealRetainsAllFellowshipSystems(): void
    {
        $show = $this->showView();

        foreach ([
            'Company Charter',
            'Company Chronicle',
            'Fellowship Treasury',
            'Company Offices',
            'Fellowship Roster',
            'Add an Adventurer',
            'components.media.fellowship-portrait',
        ] as $contract) {
            self::assertStringContainsString(
                $contract,
                $show
            );
        }
    }

    public function testHallSealRetainsSecureWriteContracts(): void
    {
        $show = $this->showView();

        self::assertStringContainsString(
            "'gmrc_party_chronicle_' . \$id",
            $show
        );
        self::assertStringContainsString(
            "'gmrc_party_treasury_' . \$id",
            $show
        );
        self::assertStringContainsString(
            "'gmrc_party_members_' . \$id",
            $show
        );
        self::assertStringContainsString(
            'name="action"',
            $show
        );
        self::assertStringContainsString(
            'value="gmrc_app_request"',
            $show
        );
    }

    private function showView(): string
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($source);

        return $source;
    }

    private function javascript(): string
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root
            . '/assets/js/modules/parties/'
            . 'fellowship-hall.js'
        );

        self::assertIsString($source);

        return $source;
    }

    private function css(): string
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($source);

        return $source;
    }
}
