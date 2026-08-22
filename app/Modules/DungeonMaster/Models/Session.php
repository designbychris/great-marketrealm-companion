<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class Session
{
    public const STATUS_PLANNED = 'planned';
    public const STATUS_PLAYED = 'played';
    public const STATUS_CANCELLED = 'cancelled';

    /** @param array<int,array{player_id:int,character_ids:array<int,string>}> $attendance */
    private function __construct(
        private string $id,
        private string $campaignId,
        private int $ownerId,
        private int $number,
        private string $title,
        private string $scheduledDate,
        private string $status,
        private string $prepNotes,
        private string $recap,
        private array $attendance
    ) {
        if (! Ulid::isValid($id) || ! Ulid::isValid($campaignId)) {
            throw new InvalidArgumentException('Invalid Session or Campaign identifier.');
        }
        if ($ownerId < 1 || $number < 1) {
            throw new InvalidArgumentException('A Session requires a Dungeon Master and session number.');
        }
    }

    /** @param array<int,array{player_id:int,character_ids:array<int,string>}> $attendance */
    public static function create(string $campaignId, int $ownerId, int $number, string $title, string $scheduledDate, string $status, string $prepNotes, string $recap, array $attendance): self
    {
        return new self(Ulid::generate(), $campaignId, $ownerId, $number, $title, $scheduledDate, self::normaliseStatus($status), $prepNotes, $recap, $attendance);
    }

    /** @param array<int,array{player_id:int,character_ids:array<int,string>}> $attendance */
    public static function restore(string $id, string $campaignId, int $ownerId, int $number, string $title, string $scheduledDate, string $status, string $prepNotes, string $recap, array $attendance): self
    {
        return new self($id, $campaignId, $ownerId, $number, $title, $scheduledDate, self::normaliseStatus($status), $prepNotes, $recap, $attendance);
    }

    /** @param array<int,array{player_id:int,character_ids:array<int,string>}> $attendance */
    public function update(int $number, string $title, string $scheduledDate, string $status, string $prepNotes, string $recap, array $attendance): void
    {
        $this->number = max(1, $number);
        $this->title = $title;
        $this->scheduledDate = $scheduledDate;
        $this->status = self::normaliseStatus($status);
        $this->prepNotes = $prepNotes;
        $this->recap = $recap;
        $this->attendance = $attendance;
    }

    private static function normaliseStatus(string $status): string
    {
        return in_array($status, [self::STATUS_PLANNED, self::STATUS_PLAYED, self::STATUS_CANCELLED], true) ? $status : self::STATUS_PLANNED;
    }

    public function id(): string { return $this->id; }
    public function campaignId(): string { return $this->campaignId; }
    public function ownerId(): int { return $this->ownerId; }
    public function number(): int { return $this->number; }
    public function title(): string { return $this->title; }
    public function scheduledDate(): string { return $this->scheduledDate; }
    public function status(): string { return $this->status; }
    public function prepNotes(): string { return $this->prepNotes; }
    public function recap(): string { return $this->recap; }
    /** @return array<int,array{player_id:int,character_ids:array<int,string>}> */
    public function attendance(): array { return $this->attendance; }
}
