<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Models;

defined('ABSPATH') || exit;

final class CanonicalMonster
{
    /** @param array<string,mixed> $data */
    public function __construct(private array $data) {}

    public function id(): string { return 'canonical:' . $this->key(); }
    public function key(): string { return (string) ($this->data['key'] ?? ''); }
    public function name(): string { return (string) ($this->data['name'] ?? ''); }
    public function creatureType(): string { return (string) ($this->data['type'] ?? ''); }
    public function size(): string { return (string) ($this->data['size'] ?? ''); }
    public function armorClass(): ?int { return isset($this->data['ac']) ? (int) $this->data['ac'] : null; }
    public function maxHp(): ?int { return isset($this->data['hp']) ? (int) $this->data['hp'] : null; }
    public function speed(): string { return (string) ($this->data['speed'] ?? ''); }
    public function challenge(): string { return (string) ($this->data['cr'] ?? ''); }
    public function traits(): string { return (string) ($this->data['traits'] ?? ''); }
    public function actions(): string { return (string) ($this->data['actions'] ?? ''); }
    public function notes(): string { return (string) ($this->data['notes'] ?? ''); }
    public function sourceIssue(): string { return (string) ($this->data['source_issue'] ?? ''); }
    public function isArchived(): bool { return false; }
    public function isCanonical(): bool { return true; }

    public function dexterity(): ?int
    {
        return isset($this->data['dex']) ? (int) $this->data['dex'] : null;
    }

    public function initiativeModifier(): ?int
    {
        $dexterity = $this->dexterity();
        return $dexterity === null ? null : (int) floor(($dexterity - 10) / 2);
    }

    public function encounterReady(): bool
    {
        return $this->armorClass() !== null
            && $this->maxHp() !== null
            && $this->initiativeModifier() !== null;
    }

    /** @return array<string,mixed> */
    public function encounterSnapshot(int $quantity): array
    {
        if (! $this->encounterReady()) {
            return [];
        }

        return [
            'monster_id' => $this->id(),
            'name' => $this->name(),
            'quantity' => max(1, min(20, $quantity)),
            'armor_class' => $this->armorClass(),
            'max_hp' => $this->maxHp(),
            'initiative_modifier' => $this->initiativeModifier(),
            'challenge' => $this->challenge(),
            'canonical' => true,
        ];
    }
}
