<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

use GreatMarketrealmCompanion\Modules\Library\Relics\Repositories\HandbookRelicRegister;

defined('ABSPATH') || exit;

final class RelicReferenceCatalogue extends AbstractFoundationCatalogue
{
    public function __construct(
        private ?HandbookRelicRegister $register = null
    ) {
        $this->register ??= new HandbookRelicRegister();
    }

    public function key(): string
    {
        return 'relics';
    }

    public function label(): string
    {
        return 'Relics of the Marketrealm';
    }

    public function description(): string
    {
        return 'Magical gear, strange wonders, enchanted foil, legendary armour and legendary weapons from the canonical Player’s Handbook.';
    }

    public function phase(): string
    {
        return 'III.13.5';
    }

    public function status(): string
    {
        return 'registered';
    }

    public function entries(): array
    {
        return array_map(
            static fn ($record): array => $record->toArray(),
            $this->register->all()
        );
    }
}
