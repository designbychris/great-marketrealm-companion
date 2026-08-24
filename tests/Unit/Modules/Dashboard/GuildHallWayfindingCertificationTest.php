<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Dashboard;

use GreatMarketrealmCompanion\Modules\Dashboard\Services\GuildHallDirectory;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use PHPUnit\Framework\TestCase;

final class GuildHallWayfindingCertificationTest extends TestCase
{
    public function testPlayerDirectoryExposesCertifiedPlayerDestinations(): void
    {
        $rooms = (new GuildHallDirectory())->forAccount(AccountType::PLAYER, false);

        self::assertSame(
            ['characters', 'active-campaigns', 'market-pass', 'parties', 'library', 'profile', 'guild-honours'],
            array_column($rooms, 'key')
        );
    }

    public function testPlayerDirectoryOffersFellowshipSealRedemption(): void
    {
        $room = $this->room('parties', (new GuildHallDirectory())->forAccount(AccountType::PLAYER, false));

        self::assertContains('fellowship-seal', array_column($room['actions'], 'route'));
    }

    public function testPlayerDirectoryDoesNotExposeDungeonMasterDesk(): void
    {
        $rooms = (new GuildHallDirectory())->forAccount(AccountType::PLAYER, false);

        self::assertNotContains('dungeon-master', array_column($rooms, 'key'));
    }

    public function testDungeonMasterDirectoryExposesDeskWithoutPlayerInvitationRooms(): void
    {
        $rooms = (new GuildHallDirectory())->forAccount(AccountType::DM, true);
        $keys = array_column($rooms, 'key');

        self::assertContains('dungeon-master', $keys);
        self::assertNotContains('active-campaigns', $keys);
        self::assertNotContains('market-pass', $keys);
    }

    public function testDungeonMasterFellowshipRoomDoesNotOfferPlayerSealRedemption(): void
    {
        $room = $this->room('parties', (new GuildHallDirectory())->forAccount(AccountType::DM, true));

        self::assertNotContains('fellowship-seal', array_column($room['actions'], 'route'));
    }

    public function testCommonDirectoryRoutesReachCurrentRegisters(): void
    {
        $rooms = (new GuildHallDirectory())->forAccount(AccountType::PLAYER, false);

        self::assertSame('characters', $this->room('characters', $rooms)['actions'][0]['route']);
        self::assertSame('parties', $this->room('parties', $rooms)['actions'][0]['route']);
        self::assertSame('library', $this->room('library', $rooms)['actions'][0]['route']);
        self::assertSame('guild-profile', $this->room('profile', $rooms)['actions'][0]['route']);
    }

    public function testEveryGuildHallRoomNowPointsAtARealDestination(): void
    {
        $rooms = (new GuildHallDirectory())->forAccount(AccountType::PLAYER, false);
        $planned = array_values(array_filter($rooms, static fn (array $room): bool => ! empty($room['planned'])));
        $honours = $this->room('guild-honours', $rooms);

        self::assertSame([], $planned);
        self::assertSame('guild-honours', $honours['actions'][0]['route']);
        self::assertSame('Open the Book of Deeds', $honours['actions'][0]['label']);
    }

    public function testDashboardRendersDirectoryDataInsteadOfPerformingRoleChecks(): void
    {
        $view = $this->source('app/Modules/Dashboard/Views/index.php');

        self::assertStringContainsString('foreach ($rooms as $room)', $view);
        self::assertStringContainsString('data-room-key=', $view);
        self::assertStringNotContainsString('GuildProfile::accountType', $view);
        self::assertStringNotContainsString('get_current_user_id()', $view);
    }

    public function testLegacyJournalAndSatchelPlaceholdersAreRetired(): void
    {
        $view = $this->source('app/Modules/Dashboard/Views/index.php');
        $directory = $this->source('app/Modules/Dashboard/Services/GuildHallDirectory.php');

        self::assertStringNotContainsString('Guild Journal Initiative', $view);
        self::assertStringNotContainsString('Project Leather Satchel', $view);
        self::assertStringContainsString('Guild Journal, Leather Satchel', $directory);
    }

    public function testDirectoryPresentationCertifiesFocusAndAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/dashboard/guild-hall-dashboard.css');

        self::assertStringContainsString('.gmrc-guild-hall-room__link:focus-visible', $css);
        self::assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
        self::assertStringContainsString('.gmrc-guild-hall-room--planned', $css);
    }

    /** @param array<int, array<string, mixed>> $rooms
     *  @return array<string, mixed>
     */
    private function room(string $key, array $rooms): array
    {
        foreach ($rooms as $room) {
            if (($room['key'] ?? '') === $key) {
                return $room;
            }
        }

        self::fail('Missing Guild Hall room: ' . $key);
    }

    private function source(string $relativePath): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $relativePath);
        self::assertIsString($source);

        return $source;
    }
}
