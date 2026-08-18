<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Chronicle;

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyChronicle;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyChronicleEntry;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyChronicleEntryType;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CompanyChronicleRegressionTest extends TestCase
{
    public function testNewFellowshipStartsWithEmptyChronicle(): void
    {
        $party = $this->party();

        self::assertSame(0, $party->chronicle()->count());
        self::assertSame([], $party->chronicle()->entries());
    }

    public function testAdventureNoteRecordsPlayerAuthorshipWithoutCertification(): void
    {
        $entry = PartyChronicleEntry::adventureNote(
            'The Pantry Door',
            'It was definitely trapped.',
            42
        );

        self::assertSame(
            'adventure-note',
            $entry->type()->value()
        );
        self::assertSame(
            'Adventure Note',
            $entry->type()->label()
        );
        self::assertSame(
            'player',
            $entry->provenance()->value()
        );
        self::assertSame(42, $entry->authorUserId());
        self::assertFalse($entry->isCertified());
    }

    public function testAdventureNoteRequiresTitleAndContent(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyChronicleEntry::adventureNote(
            '',
            'Something happened.',
            42
        );
    }

    public function testAdventureNoteRequiresValidAuthor(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyChronicleEntry::adventureNote(
            'A title',
            'Something happened.',
            0
        );
    }

    public function testChronicleTextLimitsAreEnforcedByDomain(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyChronicleEntry::adventureNote(
            str_repeat('T', 121),
            'Something happened.',
            42
        );
    }

    public function testCompanyDeedsRequireDungeonMasterCertification(): void
    {
        $entry = PartyChronicleEntry::certifiedRecord(
            PartyChronicleEntryType::deed(),
            'The Moulder Defeated',
            'The Fellowship defeated the Moulder.',
            99
        );

        self::assertTrue($entry->isCertified());
        self::assertSame(
            'company-deed',
            $entry->type()->value()
        );
        self::assertSame(
            'Dungeon Master',
            $entry->provenance()->label()
        );
    }

    public function testFellowshipHonoursRequireDungeonMasterCertification(): void
    {
        $entry = PartyChronicleEntry::certifiedRecord(
            PartyChronicleEntryType::honour(),
            'Golden Ladle',
            'Awarded for unreasonable bravery.',
            99
        );

        self::assertTrue($entry->isCertified());
        self::assertSame(
            'fellowship-honour',
            $entry->type()->value()
        );
        self::assertTrue(
            $entry->type()->requiresCertification()
        );
    }

    public function testAdventureNotesCannotUseCertifiedRecordBoundary(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyChronicleEntry::certifiedRecord(
            PartyChronicleEntryType::note(),
            'Player note',
            'This is not a certified record.',
            99
        );
    }

    public function testChronicleAcceptsPlayerNotesAndSortsNewestFirst(): void
    {
        $chronicle = PartyChronicle::empty();

        $chronicle->addAdventureNote(
            'First',
            'First entry.',
            42
        );
        $chronicle->addAdventureNote(
            'Second',
            'Second entry.',
            42
        );

        self::assertSame(2, $chronicle->count());
        self::assertSame(
            'Second',
            $chronicle->newestFirst()[0]->title()
        );
    }

    public function testCertifiedRecordsUseSeparateChronicleBoundary(): void
    {
        $chronicle = PartyChronicle::empty();

        $chronicle->addCertifiedRecord(
            PartyChronicleEntry::certifiedRecord(
                PartyChronicleEntryType::honour(),
                'Market Medal',
                'Certified by the Dungeon Master.',
                99
            )
        );

        self::assertSame(1, $chronicle->count());
        self::assertTrue(
            $chronicle->entries()[0]->isCertified()
        );
    }

    public function testUncertifiedRecordCannotEnterCertifiedBoundary(): void
    {
        $chronicle = PartyChronicle::empty();

        $this->expectException(
            InvalidArgumentException::class
        );

        $chronicle->addCertifiedRecord(
            PartyChronicleEntry::adventureNote(
                'Ordinary note',
                'Not a certified deed.',
                42
            )
        );
    }

    public function testPartyDelegatesPlayerNoteToOwnedChronicle(): void
    {
        $party = $this->party();

        $party->addChronicleNote(
            'Session One',
            'The Fellowship met at the Giggling Gourd.',
            42
        );

        self::assertSame(1, $party->chronicle()->count());
        self::assertSame(
            'Session One',
            $party->chronicle()->entries()[0]->title()
        );
    }

    public function testRepositoryPersistsChronicleSeparatelyAndLegacyPartiesDefaultEmpty(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root . '/app/Modules/Parties/Repositories/PartyRepository.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            "'_gmrc_party_chronicle'",
            $source
        );
        self::assertStringContainsString(
            '$party->chronicle()->entries()',
            $source
        );
        self::assertStringContainsString(
            '$this->chronicle($post->ID)',
            $source
        );
        self::assertStringContainsString(
            'PartyChronicle::empty()',
            $source
        );
    }

    public function testChronicleNoteUsesOwnerScopedApplicationAction(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root
            . '/app/Modules/Parties/Actions/'
            . 'AddPartyChronicleNoteAction.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            '$this->finder->find(',
            $source
        );
        self::assertStringContainsString(
            '$party->addChronicleNote(',
            $source
        );
        self::assertStringContainsString(
            '$this->parties->save($party)',
            $source
        );
    }

    public function testPlayerHttpContractOnlyCreatesAdventureNotes(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents(
            $root . '/app/Modules/Parties/Routes.php'
        );

        self::assertIsString($routes);
        self::assertStringContainsString(
            "'/parties/{id}/chronicle/notes'",
            $routes
        );
        self::assertStringContainsString(
            "'addChronicleNote'",
            $routes
        );
        self::assertStringNotContainsString(
            '/chronicle/deeds',
            $routes
        );
        self::assertStringNotContainsString(
            '/chronicle/honours',
            $routes
        );
        self::assertStringNotContainsString(
            '/chronicle/certify',
            $routes
        );
    }

    public function testChronicleNoteRequestConstrainsTitleAndContent(): void
    {
        $root = dirname(__DIR__, 5);
        $request = file_get_contents(
            $root
            . '/app/Modules/Parties/Requests/'
            . 'AddPartyChronicleNoteRequest.php'
        );

        self::assertIsString($request);
        self::assertStringContainsString(
            "'max:120'",
            $request
        );
        self::assertStringContainsString(
            "'max:3000'",
            $request
        );
        self::assertStringContainsString(
            'return is_user_logged_in();',
            $request
        );
    }

    public function testChronicleUsesDedicatedNonceContract(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            '#^parties/([^/]+)/chronicle/notes$#',
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_chronicle_'",
            $provider
        );
    }

    public function testFellowshipHallProvidesAdventureNotesAndTimeline(): void
    {
        $root = dirname(__DIR__, 5);
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($show);
        self::assertStringContainsString(
            'Company Chronicle',
            $show
        );
        self::assertStringContainsString(
            'Adventure Notes',
            $show
        );
        self::assertStringContainsString(
            'Add to Chronicle',
            $show
        );
        self::assertStringContainsString(
            '$party->chronicle()->newestFirst()',
            $show
        );
        self::assertStringContainsString(
            'DM Certified',
            $show
        );
        self::assertStringContainsString(
            'Auby, Acting Guild Historian',
            $show
        );
    }

    public function testChroniclePresentationSupportsCertifiedFutureRecordsWithoutExposingCreation(): void
    {
        $root = dirname(__DIR__, 5);
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($show);
        self::assertIsString($css);
        self::assertStringContainsString(
            '$entry->isCertified()',
            $show
        );
        self::assertStringContainsString(
            'gmrc-fellowship-chronicle-entry--certified',
            $show
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-chronicle-entry--certified',
            $css
        );
    }

    private function party(): Party
    {
        return Party::create(
            PartyId::generate(),
            PartyName::fromString(
                'The Pantry Fellowship'
            ),
            PartyOwnerId::fromInt(42)
        );
    }
}
