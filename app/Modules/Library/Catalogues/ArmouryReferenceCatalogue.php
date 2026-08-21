<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

defined('ABSPATH') || exit;

/**
 * Reserved canonical seam for mundane and magical equipment.
 */
final class ArmouryReferenceCatalogue extends AbstractFoundationCatalogue
{
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
        return 'Weapons, armour, shields, adventuring gear and later the relics and artefacts of the Marketrealm.';
    }

    public function phase(): string
    {
        return 'III.13.4';
    }
}
