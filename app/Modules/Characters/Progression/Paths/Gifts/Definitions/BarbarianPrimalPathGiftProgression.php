<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Gift progressions for the eight registered Barbarian Primal Paths.
 */
final class BarbarianPrimalPathGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /**
     * @var array<string,array{
     *     label:string,
     *     gifts:array<int,array<string,mixed>>
     * }>
     */
    private const PATHS = [
        'path-of-the-great-tony' => [
            'label' => 'Path of the Great Tony',
            'gifts' => [
                [
                    'key' => 'tonys-opening-roar',
                    'label' => 'Tony’s Opening Roar',
                    'level' => 3,
                    'summary' =>
                        'Turn the first heartbeat of Rage into a larger-than-life challenge.',
                    'detail' =>
                        'Your Rage announces itself with impossible confidence, helping you seize attention and establish the tempo of a fight.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'larger-than-the-label',
                    'label' => 'Larger Than the Label',
                    'level' => 6,
                    'summary' =>
                        'Your reputation and physical presence become difficult to ignore.',
                    'detail' =>
                        'You carry the swagger of a legendary aisle champion, using sheer presence to keep pressure on nearby foes.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'tonys-comeback',
                    'label' => 'Tony’s Comeback',
                    'level' => 10,
                    'summary' =>
                        'Turn a rough exchange into an explosive return to form.',
                    'detail' =>
                        'The Path teaches you to answer setbacks with theatrical momentum, becoming more dangerous when a battle seems to turn against you.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'great-tony-unstoppable',
                    'label' => 'The Great Tony',
                    'level' => 14,
                    'summary' =>
                        'Become the impossible-to-ignore centre of the battlefield.',
                    'detail' =>
                        'At the height of the Path, your Rage carries the presence of a market legend: loud, resilient and extraordinarily hard to contain.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'path-of-the-expired' => [
            'label' => 'Path of the Expired',
            'gifts' => [
                [
                    'key' => 'past-best-before',
                    'label' => 'Past Best Before',
                    'level' => 3,
                    'summary' =>
                        'Keep fighting long after sensible adventurers would have stopped.',
                    'detail' =>
                        'Your Rage draws strength from stubborn survival, making the idea of being “past your best” almost insulting.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'shelf-life',
                    'label' => 'Shelf Life',
                    'level' => 6,
                    'summary' =>
                        'Endure punishing conditions with unsettling persistence.',
                    'detail' =>
                        'Your body adapts to hardship with the durability of something that should have been discarded but simply refuses to spoil.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'use-by-defiance',
                    'label' => 'Use-By Defiance',
                    'level' => 10,
                    'summary' =>
                        'Refuse to let a bad turn decide the fight.',
                    'detail' =>
                        'When pressure mounts, the Path turns desperation into defiance and gives you another reason to remain standing.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'never-off-the-shelf',
                    'label' => 'Never Off the Shelf',
                    'level' => 14,
                    'summary' =>
                        'Your Rage becomes a monument to stubborn continued existence.',
                    'detail' =>
                        'You become extraordinarily difficult to write off, carrying on with the furious durability of stock nobody can quite get rid of.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'path-of-the-marbled-rage' => [
            'label' => 'Path of the Marbled Rage',
            'gifts' => [
                [
                    'key' => 'marbled-muscle',
                    'label' => 'Marbled Muscle',
                    'level' => 3,
                    'summary' =>
                        'Layer brute force with dense physical resilience.',
                    'detail' =>
                        'Your Rage settles through the body in powerful layers, making every movement feel heavier, tougher and more deliberate.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'prime-grain',
                    'label' => 'Prime Grain',
                    'level' => 6,
                    'summary' =>
                        'Absorb punishment without losing your forward drive.',
                    'detail' =>
                        'The Path teaches you to distribute impact through hardened muscle and keep advancing through blows that would stop others.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'rich-cut',
                    'label' => 'Rich Cut',
                    'level' => 10,
                    'summary' =>
                        'Put tremendous weight behind a decisive strike.',
                    'detail' =>
                        'Your attacks carry the full density of your Rage, favouring powerful committed hits over delicate technique.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'perfect-marbled-fury',
                    'label' => 'Perfect Marbled Fury',
                    'level' => 14,
                    'summary' =>
                        'Become a dense wall of momentum and physical power.',
                    'detail' =>
                        'At full mastery, the Path makes strength and toughness feel inseparable, allowing you to dominate the space immediately around you.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'path-of-the-rind' => [
            'label' => 'Path of the Rind',
            'gifts' => [
                [
                    'key' => 'hard-rind',
                    'label' => 'Hard Rind',
                    'level' => 3,
                    'summary' =>
                        'Rage toughens your outer defences like a protective peel.',
                    'detail' =>
                        'The Path teaches you to meet danger with a hardened exterior, trusting the rind to take the worst of the impact.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'bitter-edge',
                    'label' => 'Bitter Edge',
                    'level' => 6,
                    'summary' =>
                        'Punish creatures that press too closely against your defences.',
                    'detail' =>
                        'Your protective style develops an unpleasant edge, making enemies regret trying to crowd or pin you down.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'thick-skinned',
                    'label' => 'Thick-Skinned',
                    'level' => 10,
                    'summary' =>
                        'Ignore lesser attempts to wear down your resolve.',
                    'detail' =>
                        'Years of trusting the rind leave you exceptionally difficult to intimidate, harry or grind down through repeated pressure.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'unbroken-peel',
                    'label' => 'Unbroken Peel',
                    'level' => 14,
                    'summary' =>
                        'Stand behind a primal defence that seems impossible to crack.',
                    'detail' =>
                        'At the Path’s height, your Rage wraps you in a near-mythic toughness that rewards holding your ground.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'path-of-the-butchered-rage' => [
            'label' => 'Path of the Butchered Rage',
            'gifts' => [
                [
                    'key' => 'bloodied-cleaver',
                    'label' => 'Bloodied Cleaver',
                    'level' => 3,
                    'summary' =>
                        'Turn Rage into brutal close-range pressure and uncompromising strikes.',
                    'detail' =>
                        'You fight with the merciless momentum of the Butcher Isles, using heavy commitment and threatening weapon work to overwhelm nearby foes.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'butchers-instinct',
                    'label' => 'Butcher’s Instinct',
                    'level' => 6,
                    'summary' =>
                        'Read an injured opponent and know exactly where to apply more pressure.',
                    'detail' =>
                        'Your Rage sharpens an ugly battlefield instinct: wounded enemies become easier to read, pursue and keep under relentless pressure.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'carving-frenzy',
                    'label' => 'Carving Frenzy',
                    'level' => 10,
                    'summary' =>
                        'Build momentum through a savage sequence of committed attacks.',
                    'detail' =>
                        'The Path rewards staying in the thick of the fight, turning repeated close combat into mounting intimidation and violence.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'slaughterhouse-fury',
                    'label' => 'Slaughterhouse Fury',
                    'level' => 14,
                    'summary' =>
                        'Become a terrifying engine of close-quarters destruction.',
                    'detail' =>
                        'At full mastery, your Rage carries the dreadful certainty of the Butcher Isles: once you close the distance, enemies struggle to escape your pressure.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'path-of-the-sugarrush' => [
            'label' => 'Path of the Sugarrush',
            'gifts' => [
                [
                    'key' => 'sugar-spike',
                    'label' => 'Sugar Spike',
                    'level' => 3,
                    'summary' =>
                        'Explode into motion when Rage begins.',
                    'detail' =>
                        'Your fury arrives like an impossible burst of sweetness and energy, favouring immediate movement and aggressive tempo.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'hyperactive-fury',
                    'label' => 'Hyperactive Fury',
                    'level' => 6,
                    'summary' =>
                        'Keep moving while other combatants struggle to match your pace.',
                    'detail' =>
                        'The Path rewards constant motion, sudden repositioning and the refusal to stay where an enemy expects.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'crash-proof',
                    'label' => 'Crash-Proof',
                    'level' => 10,
                    'summary' =>
                        'Fight through the exhaustion that follows explosive effort.',
                    'detail' =>
                        'You learn to carry the rush beyond its natural limit, preventing a dramatic burst of activity from leaving you helpless afterward.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'endless-rush',
                    'label' => 'Endless Rush',
                    'level' => 14,
                    'summary' =>
                        'Become a blur of furious, sugar-charged momentum.',
                    'detail' =>
                        'At the height of the Path, your Rage feels almost inexhaustibly energetic, making you exceptionally difficult to slow or contain.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'path-of-the-pickled-rage' => [
            'label' => 'Path of the Pickled Rage',
            'gifts' => [
                [
                    'key' => 'brined-fury',
                    'label' => 'Brined Fury',
                    'level' => 3,
                    'summary' =>
                        'Let sharp, preserved Rage harden your body against hardship.',
                    'detail' =>
                        'Your fury has been steeped in adversity, giving it a sour resilience that thrives under unpleasant conditions.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'vinegar-bite',
                    'label' => 'Vinegar Bite',
                    'level' => 6,
                    'summary' =>
                        'Make close combat unusually unpleasant for anyone who challenges you.',
                    'detail' =>
                        'The Path gives your retaliation a sharp edge, favouring stubborn counter-pressure and a refusal to be handled gently.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'preserved-temper',
                    'label' => 'Preserved Temper',
                    'level' => 10,
                    'summary' =>
                        'Hold onto Rage through conditions that would normally spoil momentum.',
                    'detail' =>
                        'Your fury has learned to preserve itself, remaining potent even when the battlefield becomes awkward, hostile or exhausting.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'perfectly-pickled',
                    'label' => 'Perfectly Pickled',
                    'level' => 14,
                    'summary' =>
                        'Become a monument to sharp-tempered endurance.',
                    'detail' =>
                        'At full mastery, hardship only seems to deepen your flavour: the worse the situation becomes, the more stubbornly your Rage survives.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'path-of-the-butterbound' => [
            'label' => 'Path of the Butterbound',
            'gifts' => [
                [
                    'key' => 'buttered-momentum',
                    'label' => 'Buttered Momentum',
                    'level' => 3,
                    'summary' =>
                        'Use surprisingly fluid movement to carry Rage through the battlefield.',
                    'detail' =>
                        'Your fury moves with improbable smoothness, helping you slip through pressure and maintain forward momentum.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'slippery-grip',
                    'label' => 'Slippery Grip',
                    'level' => 6,
                    'summary' =>
                        'Become unusually difficult to restrain or hold in place.',
                    'detail' =>
                        'Butterbound training turns attempts to grab, pin or contain you into an exercise in frustration.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'golden-fury',
                    'label' => 'Golden Fury',
                    'level' => 10,
                    'summary' =>
                        'Turn smooth movement into heavy, confident impact.',
                    'detail' =>
                        'The Path balances slipperiness with weight, allowing graceful repositioning to end in brutally committed attacks.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'unstoppable-spread',
                    'label' => 'Unstoppable Spread',
                    'level' => 14,
                    'summary' =>
                        'Flow through a battlefield that can no longer keep you contained.',
                    'detail' =>
                        'At full mastery, you seem to spread through every opening, constantly reappearing where your Rage can cause the most trouble.',
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
                'Unknown Barbarian Primal Path gift progression.'
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
            static fn (string $path): self =>
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
