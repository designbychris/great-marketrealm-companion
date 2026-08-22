<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class Encounter
{
    public const STATUS_PREPARED = 'prepared';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const THREAT_LOW = 'low';
    public const THREAT_MODERATE = 'moderate';
    public const THREAT_HIGH = 'high';
    public const THREAT_DEADLY = 'deadly';

    /** @param string[] $characterIds */
    private function __construct(
        private string $id,
        private string $campaignId,
        private int $ownerId,
        private string $title,
        private string $sessionId,
        private string $status,
        private string $threat,
        private string $location,
        private string $adversaries,
        private string $notes,
        private array $characterIds,
        private array $monsterGroups
    ) {
        if (! Ulid::isValid($id) || ! Ulid::isValid($campaignId)) {
            throw new InvalidArgumentException('Invalid Encounter or Campaign identifier.');
        }
        if ($ownerId < 1 || trim($title) === '') {
            throw new InvalidArgumentException('An Encounter requires a Dungeon Master and title.');
        }
    }

    /** @param string[] $characterIds @param array<int,array<string,mixed>> $monsterGroups */
    public static function create(string $campaignId, int $ownerId, string $title, string $sessionId, string $status, string $threat, string $location, string $adversaries, string $notes, array $characterIds, array $monsterGroups = []): self
    {
        return new self(Ulid::generate(), $campaignId, $ownerId, $title, $sessionId, self::normaliseStatus($status), self::normaliseThreat($threat), $location, $adversaries, $notes, $characterIds, $monsterGroups);
    }

    /** @param string[] $characterIds @param array<int,array<string,mixed>> $monsterGroups */
    public static function restore(string $id, string $campaignId, int $ownerId, string $title, string $sessionId, string $status, string $threat, string $location, string $adversaries, string $notes, array $characterIds, array $monsterGroups = []): self
    {
        return new self($id, $campaignId, $ownerId, $title, $sessionId, self::normaliseStatus($status), self::normaliseThreat($threat), $location, $adversaries, $notes, $characterIds, $monsterGroups);
    }

    /** @param string[] $characterIds @param array<int,array<string,mixed>> $monsterGroups */
    public function update(string $title, string $sessionId, string $status, string $threat, string $location, string $adversaries, string $notes, array $characterIds, array $monsterGroups = []): void
    {
        $this->title = $title;
        $this->sessionId = $sessionId;
        $this->status = self::normaliseStatus($status);
        $this->threat = self::normaliseThreat($threat);
        $this->location = $location;
        $this->adversaries = $adversaries;
        $this->notes = $notes;
        $this->characterIds = $characterIds;
        $this->monsterGroups = $monsterGroups;
    }

    private static function normaliseStatus(string $status): string
    {
        return in_array($status, [self::STATUS_PREPARED, self::STATUS_RUNNING, self::STATUS_COMPLETED], true) ? $status : self::STATUS_PREPARED;
    }

    private static function normaliseThreat(string $threat): string
    {
        return in_array($threat, [self::THREAT_LOW, self::THREAT_MODERATE, self::THREAT_HIGH, self::THREAT_DEADLY], true) ? $threat : self::THREAT_MODERATE;
    }

    public function id(): string { return $this->id; }
    public function campaignId(): string { return $this->campaignId; }
    public function ownerId(): int { return $this->ownerId; }
    public function title(): string { return $this->title; }
    public function sessionId(): string { return $this->sessionId; }
    public function status(): string { return $this->status; }
    public function threat(): string { return $this->threat; }
    public function location(): string { return $this->location; }
    public function adversaries(): string { return $this->adversaries; }
    public function notes(): string { return $this->notes; }
    /** @return string[] */ public function characterIds(): array { return $this->characterIds; }
    /** @return array<int,array<string,mixed>> */ public function monsterGroups(): array { return $this->monsterGroups; }
}
