<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Integration;

use DateTimeImmutable;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Session;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignFellowshipRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignTabletopLinkRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\SessionRepository;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyChronicleEntryType;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use Throwable;

final class TabletopSessionBridge
{
    public function __construct(
        private CampaignRepository $campaigns,
        private CampaignTabletopLinkRepository $links,
        private CampaignFellowshipRepository $fellowships,
        private SessionRepository $sessions,
        private PartyRepository $parties
    ) {}

    /** @param array<int,array<string,mixed>> $records @return array<int,array<string,mixed>> */
    public function campaignChoices(array $records, int $ownerId): array
    {
        if ($ownerId < 1) {
            return $records;
        }
        foreach ($this->campaigns->allForOwner($ownerId) as $campaign) {
            if ($campaign->isArchived()) {
                continue;
            }
            $records[] = $this->projectCampaign($campaign);
        }
        return $records;
    }

    /** @param mixed $record @return array<string,mixed>|null */
    public function campaignForTable(mixed $record, string $tableId, int $ownerId): ?array
    {
        unset($record);
        $campaign = $this->links->campaignForTable($tableId, $ownerId);
        return $campaign instanceof Campaign ? $this->projectCampaign($campaign) : null;
    }

    /** @param array<string,mixed> $result @return array<string,mixed> */
    public function linkCampaign(array $result, string $tableId, string $campaignId, int $ownerId): array
    {
        $campaign = $this->campaigns->findForOwner($campaignId, $ownerId);
        if (! $campaign instanceof Campaign || $campaign->isArchived()) {
            return ['available' => true, 'linked' => false, 'message' => 'That Companion Campaign is not available to this Dungeon Master.'];
        }

        try {
            $this->links->link($campaign, $tableId);
            return [
                'available' => true,
                'linked' => true,
                'message' => 'Companion Campaign linked. Pippin has joined the two ledgers.',
                'campaign' => $this->projectCampaign($campaign),
            ];
        } catch (Throwable $exception) {
            return ['available' => true, 'linked' => false, 'message' => $exception->getMessage()];
        }
    }

    /** @param array<string,mixed> $result @param array<string,mixed> $record @return array<string,mixed> */
    public function synchroniseSession(array $result, array $record, int $ownerId): array
    {
        $tableId = trim((string) ($record['table_id'] ?? ''));
        $tableSessionId = trim((string) ($record['id'] ?? ''));
        $campaign = $this->links->campaignForTable($tableId, $ownerId);
        if (! $campaign instanceof Campaign || $campaign->isArchived()) {
            return ['available' => true, 'synchronised' => false, 'reason' => 'unlinked'];
        }

        try {
            $number = max(1, (int) ($record['number'] ?? 1));
            $title = trim((string) ($record['title'] ?? '')) ?: 'Session ' . $number;
            $startedAt = new DateTimeImmutable((string) ($record['started_at'] ?? 'now'));
            $endedAt = ! empty($record['ended_at']) ? new DateTimeImmutable((string) $record['ended_at']) : null;
            $status = $endedAt instanceof DateTimeImmutable ? Session::STATUS_PLAYED : Session::STATUS_IN_PROGRESS;

            $session = $this->sessions->findByTabletopSessionId($tableSessionId, $campaign)
                ?? $this->sessions->findUnlinkedByNumber($number, $campaign);

            if (! $session instanceof Session) {
                $session = Session::create(
                    $campaign->id(),
                    $campaign->ownerId(),
                    $number,
                    $title,
                    $startedAt->format('Y-m-d'),
                    $status,
                    '',
                    '',
                    []
                );
            }

            $session->synchroniseTabletop(
                $number,
                $title,
                $startedAt->format('Y-m-d'),
                $status,
                $tableId,
                $tableSessionId,
                $startedAt->format(DATE_ATOM),
                $endedAt?->format(DATE_ATOM),
                $endedAt instanceof DateTimeImmutable ? max(0, $endedAt->getTimestamp() - $startedAt->getTimestamp()) : 0
            );
            $this->sessions->save($session, $campaign);

            if ($endedAt instanceof DateTimeImmutable) {
                $this->writeFellowshipChronicle($campaign, $session, $endedAt);
            }

            return [
                'available' => true,
                'synchronised' => true,
                'campaign_id' => $campaign->id(),
                'session_id' => $session->id(),
                'status' => $session->status(),
            ];
        } catch (Throwable $exception) {
            return ['available' => true, 'synchronised' => false, 'message' => $exception->getMessage()];
        }
    }

    private function writeFellowshipChronicle(
        Campaign $campaign,
        Session $session,
        DateTimeImmutable $endedAt
    ): void {
        $fellowship = $this->fellowships->linked($campaign);
        if (! $fellowship instanceof Party) {
            return;
        }

        $duration = $this->formatDuration($session->durationSeconds());
        $playedDate = $session->scheduledDate() !== ''
            ? $session->scheduledDate()
            : substr($session->startedAt(), 0, 10);

        $defaultTitle = 'Session ' . $session->number();
        $title = trim($session->title()) === $defaultTitle
            ? $defaultTitle
            : sprintf('%s — %s', $defaultTitle, $session->title());
        try {
            $playedLabel = (new DateTimeImmutable($playedDate))->format('j F Y');
        } catch (Throwable) {
            $playedLabel = $playedDate;
        }
        $content = sprintf(
            'The Fellowship gathered for Session %d of %s. Played %s. The Session concluded after %s.',
            $session->number(),
            $campaign->name(),
            $playedLabel,
            $duration
        );

        $fellowship->chronicle()->upsertCertifiedRecord(
            PartyChronicleEntryType::deed(),
            $title,
            $content,
            $campaign->ownerId(),
            $endedAt,
            [
                'kind' => 'tabletop-session',
                'campaign_id' => $campaign->id(),
                'companion_session_id' => $session->id(),
                'tabletop_table_id' => $session->tabletopTableId(),
                'tabletop_session_id' => $session->tabletopSessionId(),
            ]
        );

        $this->parties->save($fellowship);
    }

    private function formatDuration(int $seconds): string
    {
        $minutes = intdiv(max(0, $seconds), 60);
        $hours = intdiv($minutes, 60);
        $minutes %= 60;

        if ($hours < 1) {
            return sprintf('%dm', $minutes);
        }
        if ($minutes < 1) {
            return sprintf('%dh', $hours);
        }
        return sprintf('%dh %dm', $hours, $minutes);
    }

    /** @return array<string,mixed> */
    private function projectCampaign(Campaign $campaign): array
    {
        $fellowship = $this->fellowships->linked($campaign);
        return [
            'id' => $campaign->id(),
            'name' => $campaign->name(),
            'table_id' => $this->links->tableId($campaign),
            'fellowship_id' => $fellowship?->id()->value() ?? '',
            'fellowship_name' => $fellowship?->name()->value() ?? '',
        ];
    }
}
