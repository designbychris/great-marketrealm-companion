<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class Monster
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    private function __construct(
        private string $id,
        private int $ownerId,
        private string $name,
        private string $creatureType,
        private string $size,
        private int $armorClass,
        private int $maxHp,
        private string $speed,
        private int $strength,
        private int $dexterity,
        private int $constitution,
        private int $intelligence,
        private int $wisdom,
        private int $charisma,
        private string $challenge,
        private string $traits,
        private string $actions,
        private string $notes,
        private string $status
    ) {
        if (! Ulid::isValid($id)) {
            throw new InvalidArgumentException('Invalid Monster Ledger identifier.');
        }
        if ($ownerId < 1 || trim($name) === '') {
            throw new InvalidArgumentException('A Monster Ledger entry requires a Dungeon Master and name.');
        }
    }

    public static function create(
        int $ownerId,
        string $name,
        string $creatureType,
        string $size,
        int $armorClass,
        int $maxHp,
        string $speed,
        int $strength,
        int $dexterity,
        int $constitution,
        int $intelligence,
        int $wisdom,
        int $charisma,
        string $challenge,
        string $traits,
        string $actions,
        string $notes
    ): self {
        return new self(
            Ulid::generate(),
            $ownerId,
            $name,
            $creatureType,
            $size,
            $armorClass,
            $maxHp,
            $speed,
            $strength,
            $dexterity,
            $constitution,
            $intelligence,
            $wisdom,
            $charisma,
            $challenge,
            $traits,
            $actions,
            $notes,
            self::STATUS_ACTIVE
        );
    }

    public static function restore(
        string $id,
        int $ownerId,
        string $name,
        string $creatureType,
        string $size,
        int $armorClass,
        int $maxHp,
        string $speed,
        int $strength,
        int $dexterity,
        int $constitution,
        int $intelligence,
        int $wisdom,
        int $charisma,
        string $challenge,
        string $traits,
        string $actions,
        string $notes,
        string $status
    ): self {
        return new self(
            $id,
            $ownerId,
            $name,
            $creatureType,
            $size,
            $armorClass,
            $maxHp,
            $speed,
            $strength,
            $dexterity,
            $constitution,
            $intelligence,
            $wisdom,
            $charisma,
            $challenge,
            $traits,
            $actions,
            $notes,
            self::normaliseStatus($status)
        );
    }

    public function update(
        string $name,
        string $creatureType,
        string $size,
        int $armorClass,
        int $maxHp,
        string $speed,
        int $strength,
        int $dexterity,
        int $constitution,
        int $intelligence,
        int $wisdom,
        int $charisma,
        string $challenge,
        string $traits,
        string $actions,
        string $notes
    ): void {
        $this->name = $name;
        $this->creatureType = $creatureType;
        $this->size = $size;
        $this->armorClass = $armorClass;
        $this->maxHp = $maxHp;
        $this->speed = $speed;
        $this->strength = $strength;
        $this->dexterity = $dexterity;
        $this->constitution = $constitution;
        $this->intelligence = $intelligence;
        $this->wisdom = $wisdom;
        $this->charisma = $charisma;
        $this->challenge = $challenge;
        $this->traits = $traits;
        $this->actions = $actions;
        $this->notes = $notes;
    }

    public function archive(): void { $this->status = self::STATUS_ARCHIVED; }
    public function isArchived(): bool { return $this->status === self::STATUS_ARCHIVED; }

    private static function normaliseStatus(string $status): string
    {
        return $status === self::STATUS_ARCHIVED ? self::STATUS_ARCHIVED : self::STATUS_ACTIVE;
    }

    public function initiativeModifier(): int
    {
        return (int) floor(($this->dexterity - 10) / 2);
    }

    /** @return array<string,mixed> */
    public function encounterSnapshot(int $quantity): array
    {
        return [
            'monster_id' => $this->id,
            'name' => $this->name,
            'quantity' => max(1, min(20, $quantity)),
            'armor_class' => $this->armorClass,
            'max_hp' => $this->maxHp,
            'initiative_modifier' => $this->initiativeModifier(),
            'challenge' => $this->challenge,
        ];
    }

    public function id(): string { return $this->id; }
    public function ownerId(): int { return $this->ownerId; }
    public function name(): string { return $this->name; }
    public function creatureType(): string { return $this->creatureType; }
    public function size(): string { return $this->size; }
    public function armorClass(): int { return $this->armorClass; }
    public function maxHp(): int { return $this->maxHp; }
    public function speed(): string { return $this->speed; }
    public function strength(): int { return $this->strength; }
    public function dexterity(): int { return $this->dexterity; }
    public function constitution(): int { return $this->constitution; }
    public function intelligence(): int { return $this->intelligence; }
    public function wisdom(): int { return $this->wisdom; }
    public function charisma(): int { return $this->charisma; }
    public function challenge(): string { return $this->challenge; }
    public function traits(): string { return $this->traits; }
    public function actions(): string { return $this->actions; }
    public function notes(): string { return $this->notes; }
    public function status(): string { return $this->status; }
}
