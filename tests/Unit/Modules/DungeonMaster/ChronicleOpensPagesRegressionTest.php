<?php
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;
use PHPUnit\Framework\TestCase;
final class ChronicleOpensPagesRegressionTest extends TestCase
{
    private function root(string $path): string { return dirname(__DIR__,4).'/'.$path; }
    public function test_tabletop_recap_is_persisted_in_session_ledger(): void { $s=file_get_contents($this->root('app/Modules/DungeonMaster/Integration/TabletopSessionBridge.php')); self::assertStringContainsString("record['recap']",$s); self::assertStringContainsString('contributions',$s); }
    public function test_fellowship_chronicle_links_to_full_session_page(): void { $s=file_get_contents($this->root('app/Modules/Parties/Views/show.php')); self::assertStringContainsString('Read the full Session', $s); self::assertStringContainsString('target="_blank"', $s); }
    public function test_full_session_page_shows_recap_contributions_and_player_notes(): void { $s=file_get_contents($this->root('app/Modules/Parties/Views/sessions/show.php')); self::assertStringContainsString('Previously, in the MarketRealm', $s); self::assertStringContainsString('Adventurers at the Table', $s); self::assertStringContainsString('Player notes', $s); }
    public function test_session_notes_are_bound_to_immutable_tabletop_session_id(): void { $s=file_get_contents($this->root('app/Modules/Parties/Models/PartyChronicle.php')); self::assertStringContainsString('tabletop-session-note',$s); self::assertStringContainsString('tabletop_session_id',$s); }
    public function test_marketrealm_dates_use_friendly_ordinal_formatting(): void { $s=file_get_contents($this->root('app/Core/Support/MarketRealmDate.php')); self::assertStringContainsString("'st'",$s); self::assertStringContainsString("'nd'",$s); self::assertStringContainsString("'rd'",$s); self::assertStringContainsString("'th'",$s); }

    public function test_session_ledger_cards_use_marketrealm_date_presenter(): void
    {
        $index = file_get_contents($this->root('app/Modules/DungeonMaster/Views/sessions/index.php'));
        $campaign = file_get_contents($this->root('app/Modules/DungeonMaster/Views/campaigns/show.php'));
        self::assertStringContainsString('MarketRealmDate::date($entry->scheduledDate())', $index);
        self::assertStringContainsString('MarketRealmDate::dateTime($entry->startedAt())', $index);
        self::assertStringContainsString('MarketRealmDate::date($nextSession->scheduledDate())', $campaign);
        self::assertStringContainsString('MarketRealmDate::dateTime($currentSession->startedAt())', $campaign);
    }

    public function test_player_note_composer_uses_companion_styled_controls(): void
    {
        $view = file_get_contents($this->root('app/Modules/Parties/Views/sessions/show.php'));
        $css = file_get_contents($this->root('assets/css/modules/dungeon-master/session-ledger.css'));
        self::assertStringContainsString('gmrc-session-note-form', $view);
        self::assertStringContainsString('gmrc-fellowship-field', $view);
        self::assertStringContainsString('gmrc-fellowship-button--primary', $view);
        self::assertStringContainsString('Player-written notes are Fellowship memories', $view);
        self::assertStringContainsString('.gmrc-session-note-form', $css);
    }

    public function testFellowshipSessionNotesUseTheApplicationNonceGateway(): void
    {
        $provider = file_get_contents($this->root('app/Providers/FrontendServiceProvider.php'));

        self::assertStringContainsString(
            "#^parties/([^/]+)/sessions/([^/]+)/notes$#",
            $provider
        );
        self::assertStringContainsString(
            "return 'gmrc_party_session_note_'",
            $provider
        );
        self::assertStringContainsString(
            '$matches[2]',
            $provider
        );
    }


    public function test_session_memories_keep_author_and_character_identity(): void
    {
        $controller = file_get_contents($this->root('app/Modules/Parties/Controllers/PartySessionController.php'));
        $chronicle = file_get_contents($this->root('app/Modules/Parties/Models/PartyChronicle.php'));
        self::assertStringContainsString('author_display_name', $controller);
        self::assertStringContainsString('character_name', $controller);
        self::assertStringContainsString('character_portrait_url', $controller);
        self::assertStringContainsString('array_merge', $chronicle);
    }

    public function test_company_chronicle_nests_session_memories_under_their_session(): void
    {
        $view = file_get_contents($this->root('app/Modules/Parties/Views/show.php'));
        self::assertStringContainsString('topLevelChronicleEntries', $view);
        self::assertStringContainsString('Player memories ·', $view);
        self::assertStringContainsString('gmrc-fellowship-session-memories', $view);
    }

    public function test_dungeon_master_session_ledger_receives_shared_player_memory_projection(): void
    {
        $repository = file_get_contents($this->root('app/Modules/DungeonMaster/Repositories/SessionRepository.php'));
        $view = file_get_contents($this->root('app/Modules/DungeonMaster/Views/sessions/show.php'));
        self::assertStringContainsString('appendPlayerNoteProjection', $repository);
        self::assertStringContainsString('Shared player memories', $view);
        self::assertStringContainsString('Dungeon Master preparation above remains private', $view);
    }

    public function test_sub_minute_duration_is_not_shown_as_zero_minutes(): void
    {
        $view = file_get_contents($this->root('app/Modules/Parties/Views/sessions/show.php'));
        self::assertStringContainsString('$duration >= 60', $view);
        self::assertStringNotContainsString("sprintf('%dm', intdiv(\$duration,60))", $view);
    }

}
