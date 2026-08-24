<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Models;

defined('ABSPATH') || exit;

final class StartingEquipmentPackage
{
    /** @param array<string,int> $items */
    public function __construct(
        private readonly string $id,
        private readonly string $classKey,
        private readonly string $label,
        private readonly array $items,
        private readonly string $source = 'Companion certified starting kit'
    ) {}

    public function id(): string { return $this->id; }
    public function classKey(): string { return $this->classKey; }
    public function label(): string { return $this->label; }
    /** @return array<string,int> */
    public function items(): array { return $this->items; }
    public function source(): string { return $this->source; }
}
