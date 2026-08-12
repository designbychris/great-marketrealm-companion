<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

defined('ABSPATH') || exit;

final class FolioCollection
{
    /** @var array<int,AdvancementFolio> */
    private array $folios = [];

    public function add(
        AdvancementFolio $folio
    ): void {
        $this->folios[] = $folio;
    }

    public function total(): int
    {
        return count($this->folios);
    }

    public function readyCount(): int
    {
        return count(
            array_filter(
                $this->folios,
                static fn (
                    AdvancementFolio $folio
                ): bool => $folio->isReady()
            )
        );
    }

    public function attentionCount(): int
    {
        return $this->total()
            - $this->readyCount();
    }

    public function allReady(): bool
    {
        return $this->total() > 0
            && $this->attentionCount() === 0;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            static fn (
                AdvancementFolio $folio
            ): array => $folio->toArray(),
            $this->folios
        );
    }
}
