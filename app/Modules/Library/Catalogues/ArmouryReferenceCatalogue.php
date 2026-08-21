<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

use GreatMarketrealmCompanion\Modules\Library\Armoury\Repositories\MarketrealmArmouryRegister;

defined('ABSPATH') || exit;

/**
 * Canonical/library seam for mundane equipment.
 *
 * Each entry declares whether it is directly handbook-mentioned or a
 * standard-compatible expansion so the Library never conflates provenance.
 */
final class ArmouryReferenceCatalogue extends AbstractFoundationCatalogue
{
    public function __construct(
        private ?MarketrealmArmouryRegister $register = null
    ) {
        $this->register ??= new MarketrealmArmouryRegister();
    }

    public function key(): string
    {
        return 'armoury';
    }

    public function label(): string
    {
        return 'The Marketrealm Armoury';
    }

    public function description(): string
    {
        return 'Mundane weapons, armour, shields and adventuring gear for the Guild Quartermaster. Relics remain sealed for III.13.5.';
    }

    public function phase(): string
    {
        return 'III.13.4';
    }

    public function status(): string
    {
        return 'registered';
    }

    public function entries(): array
    {
        return array_map(
            static fn ($record): array =>
                $record->toArray(),
            $this->register->all()
        );
    }
}
