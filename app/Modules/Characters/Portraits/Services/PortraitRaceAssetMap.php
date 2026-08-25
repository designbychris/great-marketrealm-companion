<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

defined('ABSPATH') || exit;

/**
 * Great Portrait Expansion race-family asset map.
 *
 * Race anatomy and catalogue heritage remain independent from class wardrobe.
 */
final class PortraitRaceAssetMap
{
    /**
     * @return array<string,string>
     */
    public static function assets(): array
    {
        return [
            'boxfolk-body-01' => 'Expanded/Races/Boxfolk/body-01.svg',
            'boxfolk-body-02' => 'Expanded/Races/Boxfolk/body-02.svg',
            'boxfolk-heritage-canned-steel' => 'Expanded/Races/Boxfolk/Heritages/canned-steel.svg',
            'boxfolk-heritage-frozen-core' => 'Expanded/Races/Boxfolk/Heritages/frozen-core.svg',
            'boxfolk-heritage-insulated-box' => 'Expanded/Races/Boxfolk/Heritages/insulated-box.svg',
            'dairyfolk-body-01' => 'Expanded/Races/Dairyfolk/body-01.svg',
            'dairyfolk-body-02' => 'Expanded/Races/Dairyfolk/body-02.svg',
            'dairyfolk-heritage-creamkin' => 'Expanded/Races/Dairyfolk/Heritages/creamkin.svg',
            'dairyfolk-heritage-rindborn' => 'Expanded/Races/Dairyfolk/Heritages/rindborn.svg',
            'dairyfolk-heritage-wheykin' => 'Expanded/Races/Dairyfolk/Heritages/wheykin.svg',
            'drink-folk-body-01' => 'Expanded/Races/Drinkfolk/body-01.svg',
            'drink-folk-body-02' => 'Expanded/Races/Drinkfolk/body-02.svg',
            'drink-folk-heritage-fizzfolk' => 'Expanded/Races/Drinkfolk/Heritages/fizzfolk.svg',
            'drink-folk-heritage-juicekin' => 'Expanded/Races/Drinkfolk/Heritages/juicekin.svg',
            'drink-folk-heritage-steamspout' => 'Expanded/Races/Drinkfolk/Heritages/steamspout.svg',
            'drink-folk-heritage-vintage-brewed' => 'Expanded/Races/Drinkfolk/Heritages/vintage-brewed.svg',
            'fluffling-body-01' => 'Expanded/Races/Fluffling/body-01.svg',
            'fluffling-body-02' => 'Expanded/Races/Fluffling/body-02.svg',
            'frostreem-body-01' => 'Expanded/Races/Frostreem/body-01.svg',
            'frostreem-body-02' => 'Expanded/Races/Frostreem/body-02.svg',
            'frostreem-heritage-gelatari' => 'Expanded/Races/Frostreem/Heritages/gelatari.svg',
            'frostreem-heritage-herbchurn' => 'Expanded/Races/Frostreem/Heritages/herbchurn.svg',
            'frostreem-heritage-nutcrust' => 'Expanded/Races/Frostreem/Heritages/nutcrust.svg',
            'frostreem-heritage-rippling' => 'Expanded/Races/Frostreem/Heritages/rippling.svg',
            'fructan-body-01' => 'Expanded/Races/Fructan/body-01.svg',
            'fructan-body-02' => 'Expanded/Races/Fructan/body-02.svg',
            'fructan-heritage-applekin' => 'Expanded/Races/Fructan/Heritages/applekin.svg',
            'fructan-heritage-bananari' => 'Expanded/Races/Fructan/Heritages/bananari.svg',
            'fructan-heritage-berryling' => 'Expanded/Races/Fructan/Heritages/berryling.svg',
            'fructan-heritage-melonian' => 'Expanded/Races/Fructan/Heritages/melonian.svg',
            'fructan-heritage-pinefolk' => 'Expanded/Races/Fructan/Heritages/pinefolk.svg',
            'fungifolk-body-01' => 'Expanded/Races/Fungifolk/body-01.svg',
            'fungifolk-body-02' => 'Expanded/Races/Fungifolk/body-02.svg',
            'fungifolk-heritage-glowshroom' => 'Expanded/Races/Fungifolk/Heritages/glowshroom.svg',
            'fungifolk-heritage-moldkin' => 'Expanded/Races/Fungifolk/Heritages/moldkin.svg',
            'fungifolk-heritage-stonecap' => 'Expanded/Races/Fungifolk/Heritages/stonecap.svg',
            'herbfolk-body-01' => 'Expanded/Races/Herbfolk/body-01.svg',
            'herbfolk-body-02' => 'Expanded/Races/Herbfolk/body-02.svg',
            'herbfolk-heritage-basilkin' => 'Expanded/Races/Herbfolk/Heritages/basilkin.svg',
            'herbfolk-heritage-chilite' => 'Expanded/Races/Herbfolk/Heritages/chilite.svg',
            'herbfolk-heritage-sageborn' => 'Expanded/Races/Herbfolk/Heritages/sageborn.svg',
            'marshmallow-folk-body-01' => 'Expanded/Races/MarshmallowFolk/body-01.svg',
            'marshmallow-folk-body-02' => 'Expanded/Races/MarshmallowFolk/body-02.svg',
            'meatfolk-body-01' => 'Expanded/Races/Meatfolk/body-01.svg',
            'meatfolk-body-02' => 'Expanded/Races/Meatfolk/body-02.svg',
            'meatfolk-heritage-bloodraw' => 'Expanded/Races/Meatfolk/Heritages/bloodraw.svg',
            'meatfolk-heritage-grillborn' => 'Expanded/Races/Meatfolk/Heritages/grillborn.svg',
            'meatfolk-heritage-jerkanite' => 'Expanded/Races/Meatfolk/Heritages/jerkanite.svg',
            'meatfolk-heritage-sauskling' => 'Expanded/Races/Meatfolk/Heritages/sauskling.svg',
            'meatkin-body-01' => 'Expanded/Races/Legacy/Meatkin/body-01.svg',
            'meatkin-body-02' => 'Expanded/Races/Legacy/Meatkin/body-02.svg',
            'melonian-body-01' => 'Expanded/Races/Legacy/Melonian/body-01.svg',
            'melonian-body-02' => 'Expanded/Races/Legacy/Melonian/body-02.svg',
            'recalled-body-01' => 'Expanded/Races/Recalled/body-01.svg',
            'recalled-body-02' => 'Expanded/Races/Recalled/body-02.svg',
            'recalled-heritage-explosive-batch' => 'Expanded/Races/Recalled/Heritages/explosive-batch.svg',
            'recalled-heritage-forbidden-flavour' => 'Expanded/Races/Recalled/Heritages/forbidden-flavour.svg',
            'recalled-heritage-preserved-horror' => 'Expanded/Races/Recalled/Heritages/preserved-horror.svg',
            'recalled-heritage-processed-slime' => 'Expanded/Races/Recalled/Heritages/processed-slime.svg',
            'rootkin-body-01' => 'Expanded/Races/Rootkin/body-01.svg',
            'rootkin-body-02' => 'Expanded/Races/Rootkin/body-02.svg',
            'rootkin-heritage-carrotfolk' => 'Expanded/Races/Rootkin/Heritages/carrotfolk.svg',
            'rootkin-heritage-potatofolk' => 'Expanded/Races/Rootkin/Heritages/potatofolk.svg',
            'rootkin-heritage-onionfolk' => 'Expanded/Races/Rootkin/Heritages/onionfolk.svg',
            'rootkin-heritage-garlicfolk' => 'Expanded/Races/Rootkin/Heritages/garlicfolk.svg',
            'rootkin-heritage-parsnipfolk' => 'Expanded/Races/Rootkin/Heritages/parsnipfolk.svg',
            'stalker-body-01' => 'Expanded/Races/Legacy/Stalker/body-01.svg',
            'stalker-body-02' => 'Expanded/Races/Legacy/Stalker/body-02.svg',
            'sweetfolk-body-01' => 'Expanded/Races/Sweetfolk/body-01.svg',
            'sweetfolk-body-02' => 'Expanded/Races/Sweetfolk/body-02.svg',
            'sweetfolk-heritage-biscottian' => 'Expanded/Races/Sweetfolk/Heritages/biscottian.svg',
            'sweetfolk-heritage-chocobite' => 'Expanded/Races/Sweetfolk/Heritages/chocobite.svg',
            'sweetfolk-heritage-gumdrop-gnome' => 'Expanded/Races/Sweetfolk/Heritages/gumdrop-gnome.svg',
            'sweetfolk-heritage-lollifey' => 'Expanded/Races/Sweetfolk/Heritages/lollifey.svg',
            'sweetfolk-heritage-taffling' => 'Expanded/Races/Sweetfolk/Heritages/taffling.svg',
            'vegfolk-body-01' => 'Expanded/Races/Vegfolk/body-01.svg',
            'vegfolk-body-02' => 'Expanded/Races/Vegfolk/body-02.svg',
            'vegfolk-heritage-capsicumite' => 'Expanded/Races/Vegfolk/Heritages/capsicumite.svg',
            'vegfolk-heritage-carrotian' => 'Expanded/Races/Vegfolk/Heritages/carrotian.svg',
            'vegfolk-heritage-garliad' => 'Expanded/Races/Vegfolk/Heritages/garliad.svg',
            'vegfolk-heritage-lettuceling' => 'Expanded/Races/Vegfolk/Heritages/lettuceling.svg',
            'vegfolk-heritage-spudling' => 'Expanded/Races/Vegfolk/Heritages/spudling.svg',
            'vegfolk-heritage-stalker' => 'Expanded/Races/Vegfolk/Heritages/stalker.svg',
        ];
    }

    /**
     * @return array<string,array<string,array<int,string>>>
     */
    public static function layers(): array
    {
        return [
            'boxfolk' => [
                'body' => [
                    'boxfolk-body-01',
                    'boxfolk-body-02',
                ],
                'heritage' => [
                    'boxfolk-heritage-canned-steel',
                    'boxfolk-heritage-frozen-core',
                    'boxfolk-heritage-insulated-box',
                ],
            ],
            'dairyfolk' => [
                'body' => [
                    'dairyfolk-body-01',
                    'dairyfolk-body-02',
                ],
                'heritage' => [
                    'dairyfolk-heritage-creamkin',
                    'dairyfolk-heritage-rindborn',
                    'dairyfolk-heritage-wheykin',
                ],
            ],
            'drink-folk' => [
                'body' => [
                    'drink-folk-body-01',
                    'drink-folk-body-02',
                ],
                'heritage' => [
                    'drink-folk-heritage-fizzfolk',
                    'drink-folk-heritage-vintage-brewed',
                    'drink-folk-heritage-juicekin',
                    'drink-folk-heritage-steamspout',
                ],
            ],
            'fluffling' => [
                'body' => [
                    'fluffling-body-01',
                    'fluffling-body-02',
                ],
                'heritage' => [
                    'fluffling-heritage-none',
                ],
            ],
            'frostreem' => [
                'body' => [
                    'frostreem-body-01',
                    'frostreem-body-02',
                ],
                'heritage' => [
                    'frostreem-heritage-gelatari',
                    'frostreem-heritage-rippling',
                    'frostreem-heritage-nutcrust',
                    'frostreem-heritage-herbchurn',
                ],
            ],
            'fructan' => [
                'body' => [
                    'fructan-body-01',
                    'fructan-body-02',
                ],
                'heritage' => [
                    'fructan-heritage-applekin',
                    'fructan-heritage-bananari',
                    'fructan-heritage-berryling',
                    'fructan-heritage-pinefolk',
                    'fructan-heritage-melonian',
                ],
            ],
            'fungifolk' => [
                'body' => [
                    'fungifolk-body-01',
                    'fungifolk-body-02',
                ],
                'heritage' => [
                    'fungifolk-heritage-stonecap',
                    'fungifolk-heritage-moldkin',
                    'fungifolk-heritage-glowshroom',
                ],
            ],
            'herbfolk' => [
                'body' => [
                    'herbfolk-body-01',
                    'herbfolk-body-02',
                ],
                'heritage' => [
                    'herbfolk-heritage-basilkin',
                    'herbfolk-heritage-sageborn',
                    'herbfolk-heritage-chilite',
                ],
            ],
            'marshmallow-folk' => [
                'body' => [
                    'marshmallow-folk-body-01',
                    'marshmallow-folk-body-02',
                ],
                'heritage' => [
                    'marshmallow-folk-heritage-none',
                ],
            ],
            'meatfolk' => [
                'body' => [
                    'meatfolk-body-01',
                    'meatfolk-body-02',
                ],
                'heritage' => [
                    'meatfolk-heritage-grillborn',
                    'meatfolk-heritage-bloodraw',
                    'meatfolk-heritage-sauskling',
                    'meatfolk-heritage-jerkanite',
                ],
            ],
            'meatkin' => [
                'body' => [
                    'meatkin-body-01',
                    'meatkin-body-02',
                ],
                'heritage' => [
                    'meatkin-heritage-none',
                ],
            ],
            'melonian' => [
                'body' => [
                    'melonian-body-01',
                    'melonian-body-02',
                ],
                'heritage' => [
                    'melonian-heritage-none',
                ],
            ],
            'recalled' => [
                'body' => [
                    'recalled-body-01',
                    'recalled-body-02',
                ],
                'heritage' => [
                    'recalled-heritage-processed-slime',
                    'recalled-heritage-preserved-horror',
                    'recalled-heritage-forbidden-flavour',
                    'recalled-heritage-explosive-batch',
                ],
            ],
            'rootkin' => [
                'body' => [
                    'rootkin-body-01',
                    'rootkin-body-02',
                ],
                'heritage' => [
                    'rootkin-heritage-carrotfolk',
                    'rootkin-heritage-potatofolk',
                    'rootkin-heritage-onionfolk',
                    'rootkin-heritage-garlicfolk',
                    'rootkin-heritage-parsnipfolk',
                ],
            ],
            'stalker' => [
                'body' => [
                    'stalker-body-01',
                    'stalker-body-02',
                ],
                'heritage' => [
                    'stalker-heritage-none',
                ],
            ],
            'sweetfolk' => [
                'body' => [
                    'sweetfolk-body-01',
                    'sweetfolk-body-02',
                ],
                'heritage' => [
                    'sweetfolk-heritage-gumdrop-gnome',
                    'sweetfolk-heritage-chocobite',
                    'sweetfolk-heritage-lollifey',
                    'sweetfolk-heritage-taffling',
                    'sweetfolk-heritage-biscottian',
                ],
            ],
            'vegfolk' => [
                'body' => [
                    'vegfolk-body-01',
                    'vegfolk-body-02',
                ],
                'heritage' => [
                    'vegfolk-heritage-carrotian',
                    'vegfolk-heritage-garliad',
                    'vegfolk-heritage-lettuceling',
                    'vegfolk-heritage-spudling',
                    'vegfolk-heritage-capsicumite',
                    'vegfolk-heritage-stalker',
                ],
            ],
        ];
    }

    /**
     * @return array<string,array<int,string>>
     */
    public static function forRace(string $race): array
    {
        $race = sanitize_key($race);

        return self::layers()[$race] ?? [];
    }

    public static function heritageAssetId(
        string $race,
        string $heritage
    ): string {
        $race = sanitize_key($race);
        $heritage = sanitize_key($heritage);

        if ($race === '') {
            return '';
        }

        if ($heritage === '') {
            return $race . '-heritage-none';
        }

        $candidate =
            $race . '-heritage-' . $heritage;

        return in_array(
            $candidate,
            self::forRace($race)['heritage'] ?? [],
            true
        )
            ? $candidate
            : $race . '-heritage-none';
    }
}
