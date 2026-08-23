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
    public function alignment(): string { return (string) ($this->data['alignment'] ?? ''); }
    public function armorClass(): ?int { return isset($this->data['ac']) ? (int) $this->data['ac'] : null; }
    public function armorDescription(): string { return (string) ($this->data['armor_description'] ?? ''); }
    public function maxHp(): ?int { return isset($this->data['hp']) ? (int) $this->data['hp'] : null; }
    public function hpFormula(): string { return (string) ($this->data['hp_formula'] ?? ''); }
    public function speed(): string { return (string) ($this->data['speed'] ?? ''); }
    public function challenge(): string { return (string) ($this->data['cr'] ?? ''); }
    public function description(): string { return $this->text('description'); }
    public function savingThrows(): string { return $this->text('saving_throws'); }
    public function skills(): string { return $this->text('skills'); }
    public function damageResistances(): string { return $this->text('damage_resistances'); }
    public function damageImmunities(): string { return $this->text('damage_immunities'); }
    public function damageVulnerabilities(): string { return $this->text('damage_vulnerabilities'); }
    public function conditionImmunities(): string { return $this->text('condition_immunities'); }
    public function senses(): string { return $this->text('senses'); }
    public function languages(): string { return $this->text('languages'); }
    public function spellcasting(): string { return $this->text('spellcasting'); }
    public function reactions(): string { return $this->text('reactions'); }
    public function legendaryActions(): string { return $this->text('legendary_actions'); }
    public function mythicActions(): string { return $this->text('mythic_actions'); }
    public function lairActions(): string { return $this->text('lair_actions'); }
    public function traits(): string { return (string) ($this->data['traits'] ?? ''); }
    public function actions(): string { return (string) ($this->data['actions'] ?? ''); }
    public function notes(): string { return (string) ($this->data['notes'] ?? ''); }
    public function sourceIssue(): string { return (string) ($this->data['source_issue'] ?? ''); }
    public function imageAttachmentId(): int { return absint($this->data['image_attachment_id'] ?? 0); }
    public function fieldGuideVisible(): bool { return ! empty($this->data['field_guide_visible']); }
    public function playerDescription(): string { return $this->text('player_description'); }
    public function isArchived(): bool { return false; }
    public function isCanonical(): bool { return true; }

    public function strength(): ?int { return $this->ability('str'); }
    public function dexterity(): ?int { return $this->ability('dex'); }
    public function constitution(): ?int { return $this->ability('con'); }
    public function intelligence(): ?int { return $this->ability('int'); }
    public function wisdom(): ?int { return $this->ability('wis'); }
    public function charisma(): ?int { return $this->ability('cha'); }

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

    private function text(string $key): string
    {
        return (string) ($this->data[$key] ?? '');
    }

    private function ability(string $key): ?int
    {
        return isset($this->data[$key]) ? (int) $this->data[$key] : null;
    }
}
