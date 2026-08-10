<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Models;

defined('ABSPATH') || exit;

/**
 * A character-owned catalogue item and its mutable carrying state.
 */
final class InventoryEntry
{
    public function __construct(
        private string $itemId,
        private int $quantity = 1,
        private bool $equipped = false,
        private string $notes = ''
    ) {
        $this->quantity = max(1, $quantity);
    }

    public function itemId(): string { return $this->itemId; }
    public function quantity(): int { return $this->quantity; }
    public function equipped(): bool { return $this->equipped; }
    public function notes(): string { return $this->notes; }

    public function withQuantity(int $quantity): self
    {
        return new self($this->itemId, max(1, $quantity), $this->equipped, $this->notes);
    }

    public function withEquipped(bool $equipped): self
    {
        return new self($this->itemId, $this->quantity, $equipped, $this->notes);
    }

    public function toArray(): array
    {
        return [
            'item_id' => $this->itemId,
            'quantity' => $this->quantity,
            'equipped' => $this->equipped,
            'notes' => $this->notes,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            sanitize_key((string) ($data['item_id'] ?? '')),
            max(1, (int) ($data['quantity'] ?? 1)),
            (bool) ($data['equipped'] ?? false),
            sanitize_text_field((string) ($data['notes'] ?? ''))
        );
    }
}
