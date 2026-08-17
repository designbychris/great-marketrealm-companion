<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Charter;

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCharter;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CompanyCharterRegressionTest extends TestCase
{
    public function testNewFellowshipStartsWithBlankCharter(): void
    {
        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString('Test Fellowship'),
            PartyOwnerId::fromInt(42)
        );

        self::assertTrue(
            $party->charter()->isBlank()
        );
        self::assertSame(
            '',
            $party->charter()->motto()
        );
        self::assertSame(
            '',
            $party->charter()->description()
        );
        self::assertSame(
            '',
            $party->charter()->statement()
        );
    }

    public function testCharterTrimsAndPreservesCompanyWriting(): void
    {
        $charter = PartyCharter::make(
            '  Leave no pantry unexplored.  ',
            '  A curious company of Guild adventurers.  ',
            "  We travel together.\nWe share the snacks.  "
        );

        self::assertSame(
            'Leave no pantry unexplored.',
            $charter->motto()
        );
        self::assertSame(
            'A curious company of Guild adventurers.',
            $charter->description()
        );
        self::assertSame(
            "We travel together.\nWe share the snacks.",
            $charter->statement()
        );
        self::assertFalse(
            $charter->isBlank()
        );
    }

    public function testCharterRejectsOversizedMotto(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyCharter::make(
            str_repeat('M', 91),
            '',
            ''
        );
    }

    public function testCharterRejectsOversizedDescription(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyCharter::make(
            '',
            str_repeat('D', 241),
            ''
        );
    }

    public function testCharterRejectsOversizedStatement(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyCharter::make(
            '',
            '',
            str_repeat('C', 1201)
        );
    }

    public function testRepositoryPersistsCharterSeparatelyFromPartyTitle(): void
    {
        $root = dirname(__DIR__, 5);
        $repository = file_get_contents(
            $root
            . '/app/Modules/Parties/Repositories/'
            . 'PartyRepository.php'
        );

        self::assertIsString($repository);
        self::assertStringContainsString(
            "'_gmrc_party_charter'",
            $repository
        );
        self::assertStringContainsString(
            '$party->charter()->toArray()',
            $repository
        );
        self::assertStringContainsString(
            '$this->charter($post->ID)',
            $repository
        );
        self::assertStringContainsString(
            'PartyCharter::blank()',
            $repository
        );
    }

    public function testCharterUsesDedicatedOwnerScopedHttpBoundary(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents(
            $root . '/app/Modules/Parties/Routes.php'
        );
        $controller = file_get_contents(
            $root
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($routes);
        self::assertIsString($controller);
        self::assertIsString($provider);
        self::assertStringContainsString(
            "'/parties/{id}/charter'",
            $routes
        );
        self::assertStringContainsString(
            "'updateCharter'",
            $routes
        );
        self::assertStringContainsString(
            '$this->updateCharter->handle(',
            $controller
        );
        self::assertStringContainsString(
            '#^parties/([^/]+)/charter$#',
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_'",
            $provider
        );
    }

    public function testCharterRequestConstrainsAllWrittenFields(): void
    {
        $root = dirname(__DIR__, 5);
        $request = file_get_contents(
            $root
            . '/app/Modules/Parties/Requests/'
            . 'UpdatePartyCharterRequest.php'
        );

        self::assertIsString($request);
        self::assertStringContainsString(
            "'max:90'",
            $request
        );
        self::assertStringContainsString(
            "'max:240'",
            $request
        );
        self::assertStringContainsString(
            "'max:1200'",
            $request
        );
        self::assertStringContainsString(
            'return is_user_logged_in();',
            $request
        );
    }

    public function testEditViewProvidesMottoDescriptionAndCharterStatement(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Parties/Views/edit.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'The Company Charter',
            $view
        );
        self::assertStringContainsString(
            'name="motto"',
            $view
        );
        self::assertStringContainsString(
            'name="description"',
            $view
        );
        self::assertStringContainsString(
            'name="statement"',
            $view
        );
        self::assertStringContainsString(
            'Save Company Charter',
            $view
        );
    }

    public function testFellowshipHallShowsMottoAndWrittenCharterWithoutDuplicatingCharacterData(): void
    {
        $root = dirname(__DIR__, 5);
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($show);
        self::assertStringContainsString(
            'gmrc-fellowship-hero__motto',
            $show
        );
        self::assertStringContainsString(
            'Company Charter',
            $show
        );
        self::assertStringContainsString(
            '$party->charter()->description()',
            $show
        );
        self::assertStringContainsString(
            '$party->charter()->statement()',
            $show
        );
        self::assertStringContainsString(
            '$party->standard()->emblemGlyph()',
            $show
        );
    }

    public function testRegisterUsesMottoFirstThenFallsBackToShortDescription(): void
    {
        $root = dirname(__DIR__, 5);
        $index = file_get_contents(
            $root . '/app/Modules/Parties/Views/index.php'
        );

        self::assertIsString($index);
        self::assertStringContainsString(
            '$party->charter()->motto()',
            $index
        );
        self::assertStringContainsString(
            '$party->charter()->description()',
            $index
        );
        self::assertStringContainsString(
            'gmrc-fellowship-entry__motto',
            $index
        );
        self::assertStringContainsString(
            'gmrc-fellowship-entry__description',
            $index
        );
    }
}
