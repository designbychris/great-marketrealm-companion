<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Data-backed Path Gift definition for the six registered Fighter Paths.
 *
 * Each instance represents one certified Martial Path and exposes the
 * standard Fighter path-feature cadence at levels 3, 7, 10, 15 and 18.
 */
final class FighterMartialPathGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /**
     * @var array<string,array{
     *     label:string,
     *     gifts:array<int,array<string,mixed>>
     * }>
     */
    private const PATHS = [
        'discontinued-lineage' => [
            'label' => 'Discontinued Lineage',
            'gifts' => [
                [
                    'key' => 'legacy-stock',
                    'label' => 'Legacy Stock',
                    'level' => 3,
                    'summary' =>
                        'Fight with the stubborn resilience of a product line that refuses to disappear.',
                    'detail' =>
                        'Your training is built around obsolete techniques that nobody expects to see anymore. You gain the Path’s first defensive and adaptability tricks.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'out-of-circulation',
                    'label' => 'Out of Circulation',
                    'level' => 7,
                    'summary' =>
                        'Slip out of the enemy’s expected rhythm and punish predictable tactics.',
                    'detail' =>
                        'You learn to break conventional battle patterns, making your movement and reactions unusually difficult to anticipate.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'collector-grade',
                    'label' => 'Collector Grade',
                    'level' => 10,
                    'summary' =>
                        'Old techniques become prized weapons in your hands.',
                    'detail' =>
                        'Your discontinued methods are refined into rare battlefield advantages that reward patience, positioning and careful timing.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'recall-resistant',
                    'label' => 'Recall Resistant',
                    'level' => 15,
                    'summary' =>
                        'Refuse effects that would remove you from the fight.',
                    'detail' =>
                        'The Path hardens you against forced displacement, restraint and attempts to shut down your place on the battlefield.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'never-truly-gone',
                    'label' => 'Never Truly Gone',
                    'level' => 18,
                    'summary' =>
                        'A discontinued legend always finds a way back onto the shelf.',
                    'detail' =>
                        'At the height of this Path, your battlefield presence becomes extraordinarily difficult to erase, allowing dramatic recoveries when allies think you are finished.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'butcher' => [
            'label' => 'Butcher',
            'gifts' => [
                [
                    'key' => 'cleavers-eye',
                    'label' => 'Cleaver’s Eye',
                    'level' => 3,
                    'summary' =>
                        'Read an opponent’s guard and find the cleanest line through it.',
                    'detail' =>
                        'Butcher training teaches precise cuts, disciplined footwork and an instinct for where a defended target is weakest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'joint-separator',
                    'label' => 'Joint Separator',
                    'level' => 7,
                    'summary' =>
                        'Turn well-placed strikes into control of the enemy’s movement.',
                    'detail' =>
                        'Your blows become increasingly capable of disrupting balance, movement and weapon control without sacrificing martial precision.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'prime-cut',
                    'label' => 'Prime Cut',
                    'level' => 10,
                    'summary' =>
                        'Exploit a perfect opening for an especially decisive strike.',
                    'detail' =>
                        'Your study of anatomy and armour lines lets you convert the right opening into a devastatingly efficient attack.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'cold-room-discipline',
                    'label' => 'Cold-Room Discipline',
                    'level' => 15,
                    'summary' =>
                        'Remain controlled when pain, fear and chaos overwhelm others.',
                    'detail' =>
                        'Years of exacting work under harsh conditions grant exceptional composure when the battlefield becomes gruesome or desperate.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'master-butcher',
                    'label' => 'Master Butcher',
                    'level' => 18,
                    'summary' =>
                        'Every movement becomes deliberate, economical and dangerous.',
                    'detail' =>
                        'You reach the Path’s highest discipline, wasting almost no motion and turning sustained pressure into relentless martial dominance.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'the-carver' => [
            'label' => 'The Carver',
            'gifts' => [
                [
                    'key' => 'carvers-flourish',
                    'label' => 'Carver’s Flourish',
                    'level' => 3,
                    'summary' =>
                        'Shape elegant weapon flourishes into practical battlefield openings.',
                    'detail' =>
                        'A Carver treats combat as deliberate craft, creating opportunities through precision, presentation and control.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'engraved-guard',
                    'label' => 'Engraved Guard',
                    'level' => 7,
                    'summary' =>
                        'Turn defensive technique into a practiced work of martial art.',
                    'detail' =>
                        'Your guard becomes as carefully constructed as your attacks, helping you protect yourself or an adjacent companion.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'signature-cut',
                    'label' => 'Signature Cut',
                    'level' => 10,
                    'summary' =>
                        'Develop a recognisable finishing technique unique to your style.',
                    'detail' =>
                        'Repeated practice produces a signature manoeuvre whose timing and precision make it difficult for familiar foes to answer.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'living-masterpiece',
                    'label' => 'Living Masterpiece',
                    'level' => 15,
                    'summary' =>
                        'Move through battle with extraordinary control and poise.',
                    'detail' =>
                        'Your martial craft becomes fluid enough to combine movement, defence and pressure without losing the elegance of the Path.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'gallery-of-blades',
                    'label' => 'Gallery of Blades',
                    'level' => 18,
                    'summary' =>
                        'A complete sequence of attacks becomes a masterwork in motion.',
                    'detail' =>
                        'At the summit of the Path, you can turn a full offensive sequence into a seamless display of controlled martial excellence.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'cutlery-knight' => [
            'label' => 'Cutlery Knight',
            'gifts' => [
                [
                    'key' => 'table-ready-stance',
                    'label' => 'Table-Ready Stance',
                    'level' => 3,
                    'summary' =>
                        'Adopt a disciplined stance built around balanced cutlery weapons.',
                    'detail' =>
                        'The Cutlery Knight trains with blades, forks, serving implements and improvised tableware as though each belonged in a formal armoury.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'silver-service',
                    'label' => 'Silver Service',
                    'level' => 7,
                    'summary' =>
                        'Protect nearby allies with impeccable battlefield etiquette.',
                    'detail' =>
                        'Your disciplined positioning lets you interpose weapon and shield-like implements when companions are threatened.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'full-place-setting',
                    'label' => 'Full Place Setting',
                    'level' => 10,
                    'summary' =>
                        'Switch smoothly between complementary martial implements.',
                    'detail' =>
                        'You learn to treat an assortment of weapons as one complete fighting set, adapting rapidly to range and circumstance.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'banquet-guard',
                    'label' => 'Banquet Guard',
                    'level' => 15,
                    'summary' =>
                        'Hold the line like the last defender of a royal feast.',
                    'detail' =>
                        'Your presence anchors nearby allies, making the space around you difficult for enemies to cross or dominate.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'grand-service',
                    'label' => 'Grand Service',
                    'level' => 18,
                    'summary' =>
                        'Command the battlefield with ceremonial precision.',
                    'detail' =>
                        'The complete Cutlery Knight discipline allows defence, repositioning and offence to flow together like flawless formal service.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'the-vineblade' => [
            'label' => 'The Vineblade',
            'gifts' => [
                [
                    'key' => 'tendril-footwork',
                    'label' => 'Tendril Footwork',
                    'level' => 3,
                    'summary' =>
                        'Wind through occupied ground with flexible, plant-like footwork.',
                    'detail' =>
                        'Vineblade training favours flowing movement, sudden changes of direction and attacks delivered from unexpected angles.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'grasping-cut',
                    'label' => 'Grasping Cut',
                    'level' => 7,
                    'summary' =>
                        'Use sweeping weapon work to hinder escape and reposition foes.',
                    'detail' =>
                        'Your strikes mimic curling vines, controlling lanes and punishing creatures that try to move carelessly around you.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'thorned-riposte',
                    'label' => 'Thorned Riposte',
                    'level' => 10,
                    'summary' =>
                        'Answer an enemy’s aggression with a fast, punishing counter.',
                    'detail' =>
                        'The Path teaches you to turn defensive openings into sharp retaliation, like thorns catching an unwary hand.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'overgrown-battlefield',
                    'label' => 'Overgrown Battlefield',
                    'level' => 15,
                    'summary' =>
                        'Control the space around you through constant reach and motion.',
                    'detail' =>
                        'Your movement and threat pattern spread across the battlefield, making nearby ground feel increasingly dangerous to enemies.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'ancient-vine',
                    'label' => 'Ancient Vine',
                    'level' => 18,
                    'summary' =>
                        'Become almost impossible to uproot once you establish your position.',
                    'detail' =>
                        'The perfected Vineblade style combines deep resilience with flexible movement, allowing you to bend without yielding.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'shelf-sentinel' => [
            'label' => 'Shelf Sentinel',
            'gifts' => [
                [
                    'key' => 'aisle-watch',
                    'label' => 'Aisle Watch',
                    'level' => 3,
                    'summary' =>
                        'Mark a nearby space as your responsibility and punish intrusion.',
                    'detail' =>
                        'Shelf Sentinels train to defend narrow lanes, companions and valuable stock from anything that crosses their watch.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'stockroom-intercept',
                    'label' => 'Stockroom Intercept',
                    'level' => 7,
                    'summary' =>
                        'Move quickly to intercept threats aimed at protected allies.',
                    'detail' =>
                        'Your defensive instincts let you shift position when an enemy attempts to bypass you for a more vulnerable target.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'hold-the-aisle',
                    'label' => 'Hold the Aisle',
                    'level' => 10,
                    'summary' =>
                        'Become exceptionally difficult to push away from a defended position.',
                    'detail' =>
                        'Once you establish a defensive line, forced movement and enemy pressure have a much harder time removing you from it.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'sentinels-warning',
                    'label' => 'Sentinel’s Warning',
                    'level' => 15,
                    'summary' =>
                        'Keep allies alert to attacks approaching through your guarded space.',
                    'detail' =>
                        'Your battlefield awareness becomes a shared defence, helping companions react before an ambush or sudden rush reaches them.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'unbroken-shelf',
                    'label' => 'Unbroken Shelf',
                    'level' => 18,
                    'summary' =>
                        'Stand as the immovable centre of the Fellowship’s defensive line.',
                    'detail' =>
                        'At the height of Sentinel training, your guarded area becomes extraordinarily difficult for enemies to breach while you remain standing.',
                    'mode' => 'automatic',
                ],
            ],
        ],
    ];

    private function __construct(
        private string $path
    ) {
        if (! isset(self::PATHS[$path])) {
            throw new InvalidArgumentException(
                'Unknown Fighter Martial Path gift progression.'
            );
        }
    }

    public static function for(
        string $path
    ): self {
        return new self(
            sanitize_key($path)
        );
    }

    /**
     * @return array<int,self>
     */
    public static function allDefinitions(): array
    {
        return array_map(
            static fn (
                string $path
            ): self =>
                self::for($path),
            array_keys(self::PATHS)
        );
    }

    public function supports(
        string $pathKey
    ): bool {
        return sanitize_key($pathKey)
            === $this->path;
    }

    public function pathKey(): string
    {
        return $this->path;
    }

    public function pathLabel(): string
    {
        return self::PATHS[
            $this->path
        ]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::PATHS[
            $this->path
        ]['gifts'];
    }
}
