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
}
