<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

defined('ABSPATH') || exit;

/**
 * Project Golden Apple — Guild Wardrobe asset map.
 *
 * Class wardrobe remains independent of race anatomy so any supported
 * race can wear any supported class treatment.
 */
final class PortraitClassAssetMap
{
    /**
     * @return array<string,string>
     */
    public static function assets(): array
    {
        return [
            'artificer-accessory-01' => 'Expanded/Classes/Artificer/Accessories/accessory-01.svg',
        'artificer-effects-01' => 'Expanded/Classes/Artificer/Effects/effects-01.svg',
        'artificer-equipment-01' => 'Expanded/Classes/Artificer/Equipment/equipment-01.svg',
        'artificer-equipment-02' => 'Expanded/Classes/Artificer/Equipment/equipment-02.svg',
        'artificer-ornament-01' => 'Expanded/Classes/Artificer/Ornaments/ornament-01.svg',
        'artificer-outfit-01' => 'Expanded/Classes/Artificer/Outfits/outfit-01.svg',
        'artificer-outfit-02' => 'Expanded/Classes/Artificer/Outfits/outfit-02.svg',
        'barbarian-accessory-01' => 'Expanded/Classes/Barbarian/Accessories/accessory-01.svg',
        'barbarian-effects-01' => 'Expanded/Classes/Barbarian/Effects/effects-01.svg',
        'barbarian-equipment-01' => 'Expanded/Classes/Barbarian/Equipment/equipment-01.svg',
        'barbarian-equipment-02' => 'Expanded/Classes/Barbarian/Equipment/equipment-02.svg',
        'barbarian-ornament-01' => 'Expanded/Classes/Barbarian/Ornaments/ornament-01.svg',
        'barbarian-outfit-01' => 'Expanded/Classes/Barbarian/Outfits/outfit-01.svg',
        'barbarian-outfit-02' => 'Expanded/Classes/Barbarian/Outfits/outfit-02.svg',
        'bard-accessory-01' => 'Expanded/Classes/Bard/Accessories/accessory-01.svg',
        'bard-effects-01' => 'Expanded/Classes/Bard/Effects/effects-01.svg',
        'bard-equipment-01' => 'Expanded/Classes/Bard/Equipment/equipment-01.svg',
        'bard-equipment-02' => 'Expanded/Classes/Bard/Equipment/equipment-02.svg',
        'bard-ornament-01' => 'Expanded/Classes/Bard/Ornaments/ornament-01.svg',
        'bard-outfit-01' => 'Expanded/Classes/Bard/Outfits/outfit-01.svg',
        'bard-outfit-02' => 'Expanded/Classes/Bard/Outfits/outfit-02.svg',
        'cleric-accessory-01' => 'Expanded/Classes/Cleric/Accessories/accessory-01.svg',
        'cleric-effects-01' => 'Expanded/Classes/Cleric/Effects/effects-01.svg',
        'cleric-equipment-01' => 'Expanded/Classes/Cleric/Equipment/equipment-01.svg',
        'cleric-equipment-02' => 'Expanded/Classes/Cleric/Equipment/equipment-02.svg',
        'cleric-ornament-01' => 'Expanded/Classes/Cleric/Ornaments/ornament-01.svg',
        'cleric-outfit-01' => 'Expanded/Classes/Cleric/Outfits/outfit-01.svg',
        'cleric-outfit-02' => 'Expanded/Classes/Cleric/Outfits/outfit-02.svg',
        'druid-accessory-01' => 'Expanded/Classes/Druid/Accessories/accessory-01.svg',
        'druid-effects-01' => 'Expanded/Classes/Druid/Effects/effects-01.svg',
        'druid-equipment-01' => 'Expanded/Classes/Druid/Equipment/equipment-01.svg',
        'druid-equipment-02' => 'Expanded/Classes/Druid/Equipment/equipment-02.svg',
        'druid-ornament-01' => 'Expanded/Classes/Druid/Ornaments/ornament-01.svg',
        'druid-outfit-01' => 'Expanded/Classes/Druid/Outfits/outfit-01.svg',
        'druid-outfit-02' => 'Expanded/Classes/Druid/Outfits/outfit-02.svg',
        'fighter-accessory-01' => 'Expanded/Classes/Fighter/Accessories/accessory-01.svg',
        'fighter-effects-01' => 'Expanded/Classes/Fighter/Effects/effects-01.svg',
        'fighter-equipment-01' => 'Expanded/Classes/Fighter/Equipment/equipment-01.svg',
        'fighter-equipment-02' => 'Expanded/Classes/Fighter/Equipment/equipment-02.svg',
        'fighter-ornament-01' => 'Expanded/Classes/Fighter/Ornaments/ornament-01.svg',
        'fighter-outfit-01' => 'Expanded/Classes/Fighter/Outfits/outfit-01.svg',
        'fighter-outfit-02' => 'Expanded/Classes/Fighter/Outfits/outfit-02.svg',
        'monk-accessory-01' => 'Expanded/Classes/Monk/Accessories/accessory-01.svg',
        'monk-effects-01' => 'Expanded/Classes/Monk/Effects/effects-01.svg',
        'monk-equipment-01' => 'Expanded/Classes/Monk/Equipment/equipment-01.svg',
        'monk-equipment-02' => 'Expanded/Classes/Monk/Equipment/equipment-02.svg',
        'monk-ornament-01' => 'Expanded/Classes/Monk/Ornaments/ornament-01.svg',
        'monk-outfit-01' => 'Expanded/Classes/Monk/Outfits/outfit-01.svg',
        'monk-outfit-02' => 'Expanded/Classes/Monk/Outfits/outfit-02.svg',
        'paladin-accessory-01' => 'Expanded/Classes/Paladin/Accessories/accessory-01.svg',
        'paladin-effects-01' => 'Expanded/Classes/Paladin/Effects/effects-01.svg',
        'paladin-equipment-01' => 'Expanded/Classes/Paladin/Equipment/equipment-01.svg',
        'paladin-equipment-02' => 'Expanded/Classes/Paladin/Equipment/equipment-02.svg',
        'paladin-ornament-01' => 'Expanded/Classes/Paladin/Ornaments/ornament-01.svg',
        'paladin-outfit-01' => 'Expanded/Classes/Paladin/Outfits/outfit-01.svg',
        'paladin-outfit-02' => 'Expanded/Classes/Paladin/Outfits/outfit-02.svg',
        'ranger-accessory-01' => 'Expanded/Classes/Ranger/Accessories/accessory-01.svg',
        'ranger-effects-01' => 'Expanded/Classes/Ranger/Effects/effects-01.svg',
        'ranger-equipment-01' => 'Expanded/Classes/Ranger/Equipment/equipment-01.svg',
        'ranger-equipment-02' => 'Expanded/Classes/Ranger/Equipment/equipment-02.svg',
        'ranger-ornament-01' => 'Expanded/Classes/Ranger/Ornaments/ornament-01.svg',
        'ranger-outfit-01' => 'Expanded/Classes/Ranger/Outfits/outfit-01.svg',
        'ranger-outfit-02' => 'Expanded/Classes/Ranger/Outfits/outfit-02.svg',
        'rogue-accessory-01' => 'Expanded/Classes/Rogue/Accessories/accessory-01.svg',
        'rogue-effects-01' => 'Expanded/Classes/Rogue/Effects/effects-01.svg',
        'rogue-equipment-01' => 'Expanded/Classes/Rogue/Equipment/equipment-01.svg',
        'rogue-equipment-02' => 'Expanded/Classes/Rogue/Equipment/equipment-02.svg',
        'rogue-ornament-01' => 'Expanded/Classes/Rogue/Ornaments/ornament-01.svg',
        'rogue-outfit-01' => 'Expanded/Classes/Rogue/Outfits/outfit-01.svg',
        'rogue-outfit-02' => 'Expanded/Classes/Rogue/Outfits/outfit-02.svg',
        'sorcerer-accessory-01' => 'Expanded/Classes/Sorcerer/Accessories/accessory-01.svg',
        'sorcerer-effects-01' => 'Expanded/Classes/Sorcerer/Effects/effects-01.svg',
        'sorcerer-equipment-01' => 'Expanded/Classes/Sorcerer/Equipment/equipment-01.svg',
        'sorcerer-equipment-02' => 'Expanded/Classes/Sorcerer/Equipment/equipment-02.svg',
        'sorcerer-ornament-01' => 'Expanded/Classes/Sorcerer/Ornaments/ornament-01.svg',
        'sorcerer-outfit-01' => 'Expanded/Classes/Sorcerer/Outfits/outfit-01.svg',
        'sorcerer-outfit-02' => 'Expanded/Classes/Sorcerer/Outfits/outfit-02.svg',
        'warlock-accessory-01' => 'Expanded/Classes/Warlock/Accessories/accessory-01.svg',
        'warlock-effects-01' => 'Expanded/Classes/Warlock/Effects/effects-01.svg',
        'warlock-equipment-01' => 'Expanded/Classes/Warlock/Equipment/equipment-01.svg',
        'warlock-equipment-02' => 'Expanded/Classes/Warlock/Equipment/equipment-02.svg',
        'warlock-ornament-01' => 'Expanded/Classes/Warlock/Ornaments/ornament-01.svg',
        'warlock-outfit-01' => 'Expanded/Classes/Warlock/Outfits/outfit-01.svg',
        'warlock-outfit-02' => 'Expanded/Classes/Warlock/Outfits/outfit-02.svg',
        'wizard-accessory-01' => 'Expanded/Classes/Wizard/Accessories/accessory-01.svg',
        'wizard-effects-01' => 'Expanded/Classes/Wizard/Effects/effects-01.svg',
        'wizard-equipment-01' => 'Expanded/Classes/Wizard/Equipment/equipment-01.svg',
        'wizard-equipment-02' => 'Expanded/Classes/Wizard/Equipment/equipment-02.svg',
        'wizard-ornament-01' => 'Expanded/Classes/Wizard/Ornaments/ornament-01.svg',
        'wizard-outfit-01' => 'Expanded/Classes/Wizard/Outfits/outfit-01.svg',
        'wizard-outfit-02' => 'Expanded/Classes/Wizard/Outfits/outfit-02.svg',
        ];
    }

    /**
     * @return array<string,array<string,array<int,string>>>
     */
    public static function layers(): array
    {
        return [
            'artificer' => [
            'outfit' => [
                'artificer-outfit-01',
                'artificer-outfit-02',
            ],
            'equipment' => [
                'artificer-equipment-01',
                'artificer-equipment-02',
            ],
            'class_accessory' => [
                'artificer-accessory-none',
                'artificer-accessory-01',
            ],
            'class_effects' => [
                'artificer-effects-none',
                'artificer-effects-01',
            ],
            'guild_ornament' => [
                'artificer-ornament-none',
                'artificer-ornament-01',
            ],
        ],
        'barbarian' => [
            'outfit' => [
                'barbarian-outfit-01',
                'barbarian-outfit-02',
            ],
            'equipment' => [
                'barbarian-equipment-01',
                'barbarian-equipment-02',
            ],
            'class_accessory' => [
                'barbarian-accessory-none',
                'barbarian-accessory-01',
            ],
            'class_effects' => [
                'barbarian-effects-none',
                'barbarian-effects-01',
            ],
            'guild_ornament' => [
                'barbarian-ornament-none',
                'barbarian-ornament-01',
            ],
        ],
        'bard' => [
            'outfit' => [
                'bard-outfit-01',
                'bard-outfit-02',
            ],
            'equipment' => [
                'bard-equipment-01',
                'bard-equipment-02',
            ],
            'class_accessory' => [
                'bard-accessory-none',
                'bard-accessory-01',
            ],
            'class_effects' => [
                'bard-effects-none',
                'bard-effects-01',
            ],
            'guild_ornament' => [
                'bard-ornament-none',
                'bard-ornament-01',
            ],
        ],
        'cleric' => [
            'outfit' => [
                'cleric-outfit-01',
                'cleric-outfit-02',
            ],
            'equipment' => [
                'cleric-equipment-01',
                'cleric-equipment-02',
            ],
            'class_accessory' => [
                'cleric-accessory-none',
                'cleric-accessory-01',
            ],
            'class_effects' => [
                'cleric-effects-none',
                'cleric-effects-01',
            ],
            'guild_ornament' => [
                'cleric-ornament-none',
                'cleric-ornament-01',
            ],
        ],
        'druid' => [
            'outfit' => [
                'druid-outfit-01',
                'druid-outfit-02',
            ],
            'equipment' => [
                'druid-equipment-01',
                'druid-equipment-02',
            ],
            'class_accessory' => [
                'druid-accessory-none',
                'druid-accessory-01',
            ],
            'class_effects' => [
                'druid-effects-none',
                'druid-effects-01',
            ],
            'guild_ornament' => [
                'druid-ornament-none',
                'druid-ornament-01',
            ],
        ],
        'fighter' => [
            'outfit' => [
                'fighter-outfit-01',
                'fighter-outfit-02',
            ],
            'equipment' => [
                'fighter-equipment-01',
                'fighter-equipment-02',
            ],
            'class_accessory' => [
                'fighter-accessory-none',
                'fighter-accessory-01',
            ],
            'class_effects' => [
                'fighter-effects-none',
                'fighter-effects-01',
            ],
            'guild_ornament' => [
                'fighter-ornament-none',
                'fighter-ornament-01',
            ],
        ],
        'monk' => [
            'outfit' => [
                'monk-outfit-01',
                'monk-outfit-02',
            ],
            'equipment' => [
                'monk-equipment-01',
                'monk-equipment-02',
            ],
            'class_accessory' => [
                'monk-accessory-none',
                'monk-accessory-01',
            ],
            'class_effects' => [
                'monk-effects-none',
                'monk-effects-01',
            ],
            'guild_ornament' => [
                'monk-ornament-none',
                'monk-ornament-01',
            ],
        ],
        'paladin' => [
            'outfit' => [
                'paladin-outfit-01',
                'paladin-outfit-02',
            ],
            'equipment' => [
                'paladin-equipment-01',
                'paladin-equipment-02',
            ],
            'class_accessory' => [
                'paladin-accessory-none',
                'paladin-accessory-01',
            ],
            'class_effects' => [
                'paladin-effects-none',
                'paladin-effects-01',
            ],
            'guild_ornament' => [
                'paladin-ornament-none',
                'paladin-ornament-01',
            ],
        ],
        'ranger' => [
            'outfit' => [
                'ranger-outfit-01',
                'ranger-outfit-02',
            ],
            'equipment' => [
                'ranger-equipment-01',
                'ranger-equipment-02',
            ],
            'class_accessory' => [
                'ranger-accessory-none',
                'ranger-accessory-01',
            ],
            'class_effects' => [
                'ranger-effects-none',
                'ranger-effects-01',
            ],
            'guild_ornament' => [
                'ranger-ornament-none',
                'ranger-ornament-01',
            ],
        ],
        'rogue' => [
            'outfit' => [
                'rogue-outfit-01',
                'rogue-outfit-02',
            ],
            'equipment' => [
                'rogue-equipment-01',
                'rogue-equipment-02',
            ],
            'class_accessory' => [
                'rogue-accessory-none',
                'rogue-accessory-01',
            ],
            'class_effects' => [
                'rogue-effects-none',
                'rogue-effects-01',
            ],
            'guild_ornament' => [
                'rogue-ornament-none',
                'rogue-ornament-01',
            ],
        ],
        'sorcerer' => [
            'outfit' => [
                'sorcerer-outfit-01',
                'sorcerer-outfit-02',
            ],
            'equipment' => [
                'sorcerer-equipment-01',
                'sorcerer-equipment-02',
            ],
            'class_accessory' => [
                'sorcerer-accessory-none',
                'sorcerer-accessory-01',
            ],
            'class_effects' => [
                'sorcerer-effects-none',
                'sorcerer-effects-01',
            ],
            'guild_ornament' => [
                'sorcerer-ornament-none',
                'sorcerer-ornament-01',
            ],
        ],
        'warlock' => [
            'outfit' => [
                'warlock-outfit-01',
                'warlock-outfit-02',
            ],
            'equipment' => [
                'warlock-equipment-01',
                'warlock-equipment-02',
            ],
            'class_accessory' => [
                'warlock-accessory-none',
                'warlock-accessory-01',
            ],
            'class_effects' => [
                'warlock-effects-none',
                'warlock-effects-01',
            ],
            'guild_ornament' => [
                'warlock-ornament-none',
                'warlock-ornament-01',
            ],
        ],
        'wizard' => [
            'outfit' => [
                'wizard-outfit-01',
                'wizard-outfit-02',
            ],
            'equipment' => [
                'wizard-equipment-01',
                'wizard-equipment-02',
            ],
            'class_accessory' => [
                'wizard-accessory-none',
                'wizard-accessory-01',
            ],
            'class_effects' => [
                'wizard-effects-none',
                'wizard-effects-01',
            ],
            'guild_ornament' => [
                'wizard-ornament-none',
                'wizard-ornament-01',
            ],
        ],
        ];
    }

    /**
     * @return array<string,array<int,string>>
     */
    public static function forClass(
        string $characterClass
    ): array {
        $characterClass =
            sanitize_key($characterClass);

        return self::layers()[$characterClass]
            ?? [];
    }
}
