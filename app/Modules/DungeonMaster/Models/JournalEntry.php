<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class JournalEntry
{
    public const CATEGORIES = ['general','npc','location','plot-thread','secret','lore','treasure','faction'];
    public const STATUSES = ['active','resolved','archived'];

    private function __construct(
        private string $id,
        private string $campaignId,
        private int $ownerId,
        private string $title,
        private string $category,
        private string $content,
        private string $status,
        private string $sessionId,
        private bool $pinned
    ) {
        if (! Ulid::isValid($id) || ! Ulid::isValid($campaignId) || $ownerId < 1) {
            throw new InvalidArgumentException('Invalid Campaign Journal identity.');
        }
        if (! in_array($category, self::CATEGORIES, true) || ! in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Invalid Campaign Journal classification.');
        }
    }

    public static function create(string $campaignId, int $ownerId, string $title, string $category, string $content, string $status, string $sessionId, bool $pinned): self
    { return new self(Ulid::generate(), $campaignId, $ownerId, $title, $category, $content, $status, $sessionId, $pinned); }

    public static function restore(string $id, string $campaignId, int $ownerId, string $title, string $category, string $content, string $status, string $sessionId, bool $pinned): self
    { return new self($id, $campaignId, $ownerId, $title, $category, $content, $status, $sessionId, $pinned); }

    public function update(string $title, string $category, string $content, string $status, string $sessionId, bool $pinned): void
    {
        if (! in_array($category, self::CATEGORIES, true) || ! in_array($status, self::STATUSES, true)) { throw new InvalidArgumentException('Invalid Campaign Journal classification.'); }
        $this->title=$title;$this->category=$category;$this->content=$content;$this->status=$status;$this->sessionId=$sessionId;$this->pinned=$pinned;
    }
    public function archive(): void { $this->status='archived'; }
    public function id(): string { return $this->id; }
    public function campaignId(): string { return $this->campaignId; }
    public function ownerId(): int { return $this->ownerId; }
    public function title(): string { return $this->title; }
    public function category(): string { return $this->category; }
    public function content(): string { return $this->content; }
    public function status(): string { return $this->status; }
    public function sessionId(): string { return $this->sessionId; }
    public function pinned(): bool { return $this->pinned; }
    public function categoryLabel(): string { return match($this->category){'npc'=>'NPC','plot-thread'=>'Plot Thread',default=>ucwords(str_replace('-',' ',$this->category))}; }
}