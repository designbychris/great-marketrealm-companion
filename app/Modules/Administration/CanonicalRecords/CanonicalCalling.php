<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords;

defined('ABSPATH') || exit;

final class CanonicalCalling
{
    /** @param array<int,string> $traits */
    public function __construct(
        private string $key,
        private string $kind,
        private string $name,
        private string $description,
        private ?int $hitDie,
        private string $parent,
        private array $traits,
        private string $source,
        private string $stewardNotes = ''
    ) {}

    public function key(): string { return $this->key; }
    public function kind(): string { return $this->kind; }
    public function name(): string { return $this->name; }
    public function description(): string { return $this->description; }
    public function hitDie(): ?int { return $this->hitDie; }
    public function parent(): string { return $this->parent; }
    /** @return array<int,string> */
    public function traits(): array { return $this->traits; }
    public function source(): string { return $this->source; }
    public function stewardNotes(): string { return $this->stewardNotes; }
}
