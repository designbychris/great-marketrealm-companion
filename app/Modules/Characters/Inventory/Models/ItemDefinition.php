<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Models;

defined('ABSPATH') || exit;

/**
 * Immutable catalogue definition for an item carried by an adventurer.
 */
final class ItemDefinition
{
    public function __construct(
        private string $id,
        private string $label,
        private string $category,
        private string $description,
        private float $weight,
        private ?string $equipSlot = null,
        private ?string $damageDie = null,
        private ?string $damageType = null,
        private ?int $armourBase = null,
        private ?int $dexterityCap = null,
        private int $armourBonus = 0,
        private array $properties = []
    ) {
    }

    public function id(): string { return $this->id; }
    public function label(): string { return $this->label; }
    public function category(): string { return $this->category; }
    public function description(): string { return $this->description; }
    public function weight(): float { return $this->weight; }
    public function equipSlot(): ?string { return $this->equipSlot; }
    public function damageDie(): ?string { return $this->damageDie; }
    public function damageType(): ?string { return $this->damageType; }
    public function armourBase(): ?int { return $this->armourBase; }
    public function dexterityCap(): ?int { return $this->dexterityCap; }
    public function armourBonus(): int { return $this->armourBonus; }
    public function properties(): array { return $this->properties; }
    public function isEquippable(): bool { return $this->equipSlot !== null; }
    public function isWeapon(): bool { return $this->category === 'weapon'; }
    public function isArmour(): bool { return $this->category === 'armour'; }
}
