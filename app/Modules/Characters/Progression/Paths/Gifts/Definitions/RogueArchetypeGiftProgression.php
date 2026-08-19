<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Companion-authored gift progression for the six repository-defined
 * Rogue Archetypes.
 *
 * The source player catalogue supplies Archetype identities but no traits.
 * III.12.4B therefore defines the Great Marketrealm specialist gifts here.
 */
final class RogueArchetypeGiftProgression implements PathGiftProgressionDefinitionInterface
{
    private const PATHS = [
        'the-cheetoblade' => [
            'label' => 'The Cheetoblade',
            'gifts' => [
                [
                    'key' => 'cheetle-dust-feint',
                    'label' => 'Cheetle-Dust Feint',
                    'level' => 3,
                    'summary' =>
                        'Blind the eye with colour, crumbs and misdirection before striking from the opening.',
                    'detail' =>
                        'Cheetoblades fight with shameless snack-aisle flair. A sudden flourish of bright seasoning, a false step and a quick change of angle make their first specialist technique ideal for creating confusion around a precise attack.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'dangerously-cheesy',
                    'label' => 'Dangerously Cheesy',
                    'level' => 9,
                    'summary' =>
                        'Turn outrageous confidence into a distraction enemies cannot quite ignore.',
                    'detail' =>
                        'Your style becomes so brazen that opponents begin watching the performance instead of the knife. The Cheetoblade thrives on drawing attention at exactly the wrong moment and slipping pressure toward a more vulnerable angle.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'crunch-time',
                    'label' => 'Crunch Time',
                    'level' => 13,
                    'summary' =>
                        'Become faster and more decisive when the fight reaches its messiest moment.',
                    'detail' =>
                        'When plans crumble, the Cheetoblade gets sharper. You learn to use clutter, panic and collapsing battle lines as cover for sudden movement and opportunistic precision.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'flamin-finish',
                    'label' => 'Flamin’ Finish',
                    'level' => 17,
                    'summary' =>
                        'End the sequence with a spectacular burst of speed, spice and precision.',
                    'detail' =>
                        'At full Archetype mastery, your attacks arrive with the theatrical heat of a legendary snack aisle duel: distracting, audacious and extremely difficult to read until the final cut has landed.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'spiceblade' => [
            'label' => 'Spiceblade',
            'gifts' => [
                [
                    'key' => 'seasoned-edge',
                    'label' => 'Seasoned Edge',
                    'level' => 3,
                    'summary' =>
                        'Layer precise attacks with the distracting sting of Marketrealm spicecraft.',
                    'detail' =>
                        'Spiceblades treat seasoning as part of battlefield technique. Scent, heat and drifting spice become tools for controlling attention while the blade finds the opening that matters.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'pepperstep',
                    'label' => 'Pepperstep',
                    'level' => 9,
                    'summary' =>
                        'Reposition through danger with sharp footwork that leaves pursuers blinking behind you.',
                    'detail' =>
                        'Your movement carries the sudden bite of cracked pepper: quick, irritating and difficult to follow. The technique rewards changing angles before an enemy can settle into your rhythm.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'perfect-blend',
                    'label' => 'Perfect Blend',
                    'level' => 13,
                    'summary' =>
                        'Combine stealth, timing and precision into one carefully balanced assault.',
                    'detail' =>
                        'Like a master seasoning a dish, you learn exactly how much pressure a situation needs. Too little is wasted opportunity; too much gives the trick away.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'master-of-seasoning',
                    'label' => 'Master of Seasoning',
                    'level' => 17,
                    'summary' =>
                        'Command the battlefield through an expert mixture of distraction and lethal precision.',
                    'detail' =>
                        'At the height of the Spiceblade tradition, every movement has purpose. Heat, scent, misdirection and steel combine into a style whose true danger is recognised only after it has already passed.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'the-breadknife' => [
            'label' => 'The Breadknife',
            'gifts' => [
                [
                    'key' => 'serrated-opportunity',
                    'label' => 'Serrated Opportunity',
                    'level' => 3,
                    'summary' =>
                        'Saw through stubborn defences by patiently exploiting small openings.',
                    'detail' =>
                        'Breadknives are not elegant duelling blades, and that is precisely the point. Your style favours persistence, awkward angles and the ability to worry at a defence until a useful gap finally appears.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'crumbtrail',
                    'label' => 'Crumbtrail',
                    'level' => 9,
                    'summary' =>
                        'Leave misleading signs and false routes for anyone trying to follow your movements.',
                    'detail' =>
                        'You become an expert at turning tiny traces into deliberate misinformation. Pursuers find the evidence they expect, only to discover that the trail was laid for them.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'crustbreaker',
                    'label' => 'Crustbreaker',
                    'level' => 13,
                    'summary' =>
                        'Find the weak seam in a hardened defence and work it open.',
                    'detail' =>
                        'Armour, barricades and guarded positions all have a crust. The Breadknife tradition teaches patience: identify the seam, keep pressure on it and let persistence do what brute force cannot.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'last-slice',
                    'label' => 'Last Slice',
                    'level' => 17,
                    'summary' =>
                        'Reserve one final, perfectly timed cut for the moment an enemy believes the danger has passed.',
                    'detail' =>
                        'At mastery, you embody the irritating certainty of the last slice that refuses to separate cleanly. Your best opening often arrives after everyone else thinks the exchange is finished.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'mastermind-of-the-aisles' => [
            'label' => 'Mastermind of the Aisles',
            'gifts' => [
                [
                    'key' => 'aisle-scheme',
                    'label' => 'Aisle Scheme',
                    'level' => 3,
                    'summary' =>
                        'Read routes, bottlenecks and attention to put allies and enemies exactly where you want them.',
                    'detail' =>
                        'The Mastermind sees a battlefield like a busy market floor: lanes, obstructions, witnesses and predictable traffic. Your first discipline is learning to exploit that invisible map.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'planned-distraction',
                    'label' => 'Planned Distraction',
                    'level' => 9,
                    'summary' =>
                        'Turn another creature’s attention into a resource for the whole Fellowship.',
                    'detail' =>
                        'A dropped crate, a shouted warning or a perfectly timed gesture can redirect a fight. You specialise in making an enemy look at the wrong problem while an ally acts on the real one.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'three-aisles-ahead',
                    'label' => 'Three Aisles Ahead',
                    'level' => 13,
                    'summary' =>
                        'Prepare contingencies before anyone else realises they are necessary.',
                    'detail' =>
                        'The best plan is the one that already accounted for the obvious failure. Your schemes begin to include escape routes, fallback positions and opportunities that only become visible several moves later.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'grand-market-scheme',
                    'label' => 'Grand Market Scheme',
                    'level' => 17,
                    'summary' =>
                        'Conduct a complicated encounter as though every participant had stepped into your plan.',
                    'detail' =>
                        'At full mastery, the battlefield feels less like chaos and more like a market map covered in annotations. You excel when information, positioning and coordinated allies matter as much as the blade.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'aisle-stalker' => [
            'label' => 'Aisle Stalker',
            'gifts' => [
                [
                    'key' => 'endcap-ambush',
                    'label' => 'Endcap Ambush',
                    'level' => 3,
                    'summary' =>
                        'Use shelving, corners and broken sightlines to appear where prey least expects you.',
                    'detail' =>
                        'Aisle Stalkers specialise in patient pursuit through cluttered spaces. You learn to treat cover and narrow lanes as hunting terrain, waiting for the instant a target becomes isolated.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'silent-trolley',
                    'label' => 'Silent Trolley',
                    'level' => 9,
                    'summary' =>
                        'Move through occupied ground with uncanny quiet and controlled momentum.',
                    'detail' =>
                        'You learn the strange art of being present without being noticed: slipping through traffic, matching ambient movement and never creating the sound an enemy is listening for.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'closing-time-hunter',
                    'label' => 'Closing-Time Hunter',
                    'level' => 13,
                    'summary' =>
                        'Become more dangerous as the battlefield empties and isolated targets lose support.',
                    'detail' =>
                        'The Aisle Stalker is at home after the crowds disappear. You read lonely routes and exposed positions instinctively, turning separation into opportunity.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'nowhere-left-to-hide',
                    'label' => 'Nowhere Left to Hide',
                    'level' => 17,
                    'summary' =>
                        'Pursue a marked quarry with relentless awareness until every escape route feels watched.',
                    'detail' =>
                        'At mastery, hiding from you becomes a contest of patience few creatures can win. You understand routes, cover and habits well enough to keep pressure on prey without revealing your own position.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'taffy-trickster' => [
            'label' => 'Taffy Trickster',
            'gifts' => [
                [
                    'key' => 'sticky-fingers',
                    'label' => 'Sticky Fingers',
                    'level' => 3,
                    'summary' =>
                        'Use elastic misdirection, sleight of hand and improbable reach to interfere with nearby plans.',
                    'detail' =>
                        'Taffy Tricksters fight like pulled sugar: flexible, surprising and annoyingly difficult to pin down. Their first gift turns playful sleight of hand into a practical battlefield nuisance.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'pulled-sugar-escape',
                    'label' => 'Pulled-Sugar Escape',
                    'level' => 9,
                    'summary' =>
                        'Slip out of situations that should have left you thoroughly stuck.',
                    'detail' =>
                        'Your movement becomes almost comically elastic. Grabs, crowded spaces and bad positioning become opportunities for an impossible-looking twist, stretch or reversal.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'sweet-deception',
                    'label' => 'Sweet Deception',
                    'level' => 13,
                    'summary' =>
                        'Wrap a convincing lie around the truth until enemies willingly choose the wrong answer.',
                    'detail' =>
                        'The Taffy Trickster learns that the best deception has something real at its centre. You stretch expectations without quite breaking them, making your tricks unusually difficult to dismiss.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'impossible-knot',
                    'label' => 'Impossible Knot',
                    'level' => 17,
                    'summary' =>
                        'Tie movement, distraction and deception into a trick nobody can untangle in time.',
                    'detail' =>
                        'At full mastery, your plans resemble pulled taffy folded over itself again and again. Enemies can see pieces of what happened, but reconstructing the whole trick comes far too late.',
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
                'Unknown Rogue Archetype gift progression.'
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

    /** @return array<int,self> */
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
