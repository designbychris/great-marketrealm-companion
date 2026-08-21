<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Relics\Models;

defined('ABSPATH') || exit;

final class RelicRecord
{
    public function __construct(
        private string $key,
        private string $name,
        private string $group,
        private string $itemType,
        private string $rarity,
        private ?string $attunement,
        private array $mechanics,
        private ?string $baseProfile = null,
        private ?string $flavour = null
    ) {
    }

    public function key(): string { return $this->key; }
    public function name(): string { return $this->name; }
    public function group(): string { return $this->group; }
    public function itemType(): string { return $this->itemType; }
    public function rarity(): string { return $this->rarity; }
    public function attunement(): ?string { return $this->attunement; }
    public function mechanics(): array { return $this->mechanics; }
    public function baseProfile(): ?string { return $this->baseProfile; }
    public function flavour(): ?string { return $this->flavour; }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'group' => $this->group,
            'item_type' => $this->itemType,
            'rarity' => $this->rarity,
            'attunement' => $this->attunement,
            'mechanics' => $this->mechanics,
            'base_profile' => $this->baseProfile,
            'flavour' => $this->flavour,
        ];
    }
}
