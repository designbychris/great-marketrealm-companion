<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class Campaign
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    private function __construct(
        private string $id,
        private string $name,
        private int $ownerId,
        private string $description,
        private string $status
    ) {
        if (! Ulid::isValid($id)) {
            throw new InvalidArgumentException('Invalid Campaign identifier.');
        }
        if ($ownerId < 1) {
            throw new InvalidArgumentException('A Campaign requires a Dungeon Master owner.');
        }
    }

    public static function create(string $name, int $ownerId, string $description = ''): self
    {
        return new self(Ulid::generate(), $name, $ownerId, $description, self::STATUS_ACTIVE);
    }

    public static function restore(string $id, string $name, int $ownerId, string $description, string $status): self
    {
        return new self($id, $name, $ownerId, $description, $status === self::STATUS_ARCHIVED ? self::STATUS_ARCHIVED : self::STATUS_ACTIVE);
    }

    public function update(string $name, string $description): void { $this->name=$name; $this->description=$description; }
    public function archive(): void { $this->status=self::STATUS_ARCHIVED; }
    public function id(): string { return $this->id; }
    public function name(): string { return $this->name; }
    public function ownerId(): int { return $this->ownerId; }
    public function description(): string { return $this->description; }
    public function status(): string { return $this->status; }
    public function isArchived(): bool { return $this->status === self::STATUS_ARCHIVED; }
}
