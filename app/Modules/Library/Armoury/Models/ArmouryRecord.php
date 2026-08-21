<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Armoury\Models;

defined('ABSPATH') || exit;

/**
 * Read-only mundane equipment record held by the Guild Library.
 */
final class ArmouryRecord
{
    public function __construct(
        private string $id,
        private string $label,
        private string $category,
        private string $description,
        private float $weight,
        private string $provenance,
        private ?string $equipSlot = null,
        private ?string $damageDie = null,
        private ?string $damageType = null,
        private ?int $armourBase = null,
        private ?int $dexterityCap = null,
        private int $armourBonus = 0,
        private array $properties = [],
        private ?string $range = null
    ) {
    }

    public function id(): string { return $this->id; }
    public function label(): string { return $this->label; }
    public function category(): string { return $this->category; }
    public function description(): string { return $this->description; }
    public function weight(): float { return $this->weight; }
    public function provenance(): string { return $this->provenance; }
    public function equipSlot(): ?string { return $this->equipSlot; }
    public function damageDie(): ?string { return $this->damageDie; }
    public function damageType(): ?string { return $this->damageType; }
    public function armourBase(): ?int { return $this->armourBase; }
    public function dexterityCap(): ?int { return $this->dexterityCap; }
    public function armourBonus(): int { return $this->armourBonus; }
    public function properties(): array { return $this->properties; }
    public function range(): ?string { return $this->range; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'category' => $this->category,
            'description' => $this->description,
            'weight' => $this->weight,
            'provenance' => $this->provenance,
            'equip_slot' => $this->equipSlot,
            'damage_die' => $this->damageDie,
            'damage_type' => $this->damageType,
            'armour_base' => $this->armourBase,
            'dexterity_cap' => $this->dexterityCap,
            'armour_bonus' => $this->armourBonus,
            'properties' => $this->properties,
            'range' => $this->range,
        ];
    }
}
