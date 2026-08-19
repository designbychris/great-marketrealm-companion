<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services;

defined('ABSPATH') || exit;

/**
 * Player-facing guidance for specialist Path choices.
 *
 * This service deliberately enriches choice presentation without changing
 * persisted Path identity or progression rules.
 */
final class PathChoiceGuideCatalogue
{
    /**
     * @var array<string,array{
     *     playstyle:string,
     *     best_for:string,
     *     identity:string
     * }>
     */
    private const GUIDES = [
        'path-of-the-great-tony' => [
            'playstyle' =>
                'Bold, theatrical aggression with a larger-than-life battlefield presence.',
            'best_for' =>
                'Players who want their Barbarian to dominate attention and fight with swagger.',
            'identity' =>
                'A legendary aisle-bruiser style built around confidence, resilience and dramatic comebacks.',
        ],
        'path-of-the-expired' => [
            'playstyle' =>
                'Stubborn survival and refusing to leave the fight when things turn ugly.',
            'best_for' =>
                'Players who enjoy endurance, comeback moments and grimly comic resilience.',
            'identity' =>
                'A Barbarian who treats being written off as a personal challenge.',
        ],
        'path-of-the-marbled-rage' => [
            'playstyle' =>
                'Heavy physical pressure, toughness and committed close-range attacks.',
            'best_for' =>
                'Players who want a dense, hard-hitting frontline bruiser.',
            'identity' =>
                'Rage expressed through layered muscle, weight and relentless physical momentum.',
        ],
        'path-of-the-rind' => [
            'playstyle' =>
                'Defensive Rage, holding ground and becoming increasingly hard to wear down.',
            'best_for' =>
                'Players who like tanking pressure and protecting their place in the battle line.',
            'identity' =>
                'A hardened outer defence inspired by peel, rind and stubborn natural armour.',
        ],
        'path-of-the-butchered-rage' => [
            'playstyle' =>
                'Relentless close-quarters pressure with brutal weapon work and pursuit of wounded foes.',
            'best_for' =>
                'Players who want an aggressive melee Barbarian with strong Butcher Isles flavour.',
            'identity' =>
                'A savage Butcher Isles tradition that turns Rage into merciless momentum and frightening battlefield pressure.',
        ],
        'path-of-the-sugarrush' => [
            'playstyle' =>
                'Explosive speed, constant movement and sudden bursts of aggressive energy.',
            'best_for' =>
                'Players who want a fast Barbarian who is difficult to pin down.',
            'identity' =>
                'A hyperactive fury fuelled by impossible sweetness and momentum.',
        ],
        'path-of-the-pickled-rage' => [
            'playstyle' =>
                'Sour resilience, counter-pressure and Rage that survives unpleasant conditions.',
            'best_for' =>
                'Players who enjoy stubborn defensive aggression and unusual flavour.',
            'identity' =>
                'A brined, preserved fury that becomes sharper as circumstances worsen.',
        ],
        'path-of-the-butterbound' => [
            'playstyle' =>
                'Fluid movement, slipping free of control and turning repositioning into heavy attacks.',
            'best_for' =>
                'Players who want mobility and comic slipperiness without giving up frontline power.',
            'identity' =>
                'A surprisingly graceful primal style built around smooth movement and unstoppable spread.',
        ],
        'the-cheetoblade' => [
            'playstyle' =>
                'Flashy misdirection, sudden angle changes and opportunistic precision.',
            'best_for' =>
                'Players who want a playful, audacious Rogue who turns distraction into openings.',
            'identity' =>
                'A snack-aisle duellist whose bright seasoning, swagger and theatrical feints hide a genuinely dangerous blade.',
        ],
        'spiceblade' => [
            'playstyle' =>
                'Mobile precision, sensory distraction and carefully balanced pressure.',
            'best_for' =>
                'Players who like tactical movement and a Rogue whose tricks feel sharp rather than silly.',
            'identity' =>
                'A disciplined Rogue tradition that treats spice, scent and heat as extensions of stealth and steel.',
        ],
        'the-breadknife' => [
            'playstyle' =>
                'Patient infiltration, persistent pressure and exploiting stubborn defensive seams.',
            'best_for' =>
                'Players who enjoy methodical Rogues, misleading trails and winning through persistence.',
            'identity' =>
                'An underestimated specialist who turns an awkward serrated tool into a philosophy of patient, inevitable opportunity.',
        ],
        'mastermind-of-the-aisles' => [
            'playstyle' =>
                'Planning, ally support, battlefield information and manipulating attention.',
            'best_for' =>
                'Players who want to outthink encounters and make the whole Fellowship more dangerous.',
            'identity' =>
                'A market-floor schemer who reads routes, crowds and intentions several moves ahead.',
        ],
        'aisle-stalker' => [
            'playstyle' =>
                'Ambush, pursuit, stealth and isolating vulnerable targets.',
            'best_for' =>
                'Players who want a hunter-style Rogue built around patience and controlling sightlines.',
            'identity' =>
                'A quiet predator of shelves, corners and closing-time lanes who makes familiar aisles feel like hunting ground.',
        ],
        'taffy-trickster' => [
            'playstyle' =>
                'Sleight of hand, escape artistry, elastic movement and elaborate deception.',
            'best_for' =>
                'Players who want the strangest and most mischievous Rogue, with tricks that feel almost impossible.',
            'identity' =>
                'A pulled-sugar scoundrel whose plans stretch, fold and twist until enemies cannot tell where the trick began.',
        ],
        'way-of-the-spun-cloud' => [
            'playstyle' =>
                'Highly mobile skirmishing, evasive repositioning and graceful control of distance.',
            'best_for' =>
                'Players who want their Monk to feel light, elusive and difficult to contain.',
            'identity' =>
                'A confectionery martial tradition that moves like spun sugar caught on a market breeze.',
        ],
        'way-of-the-neon-crunch' => [
            'playstyle' =>
                'Explosive tempo, bright distractions and sudden bursts of aggressive pressure.',
            'best_for' =>
                'Players who want an energetic Monk who attacks encounters with speed and spectacle.',
            'identity' =>
                'A vivid snack-aisle discipline built around crackling momentum, confidence and impossible colour.',
        ],
        'way-of-the-vacuum-seal' => [
            'playstyle' =>
                'Patient defence, controlled positioning and denying enemies room to operate.',
            'best_for' =>
                'Players who enjoy a calm, resilient Monk who wins by containing danger.',
            'identity' =>
                'A precise preservation discipline that removes wasted motion and closes every opening.',
        ],
        'way-of-the-simmering-soul' => [
            'playstyle' =>
                'Measured buildup, inner resilience and turning sustained pressure into decisive releases.',
            'best_for' =>
                'Players who prefer patience, composure and power that grows rather than immediately explodes.',
            'identity' =>
                'A contemplative kitchen tradition that keeps inner heat controlled until exactly the right moment.',
        ],
        'way-of-the-whirling-utensil' => [
            'playstyle' =>
                'Weapon-like flourishes, rapid combinations and controlling nearby space through motion.',
            'best_for' =>
                'Players who want a theatrical martial artist with strong cutlery-and-crockery Marketrealm flavour.',
            'identity' =>
                'A kitchen-floor combat art that turns the rhythm of utensils into a disciplined storm of movement.',
        ],
        'way-of-the-spongecake-soul' => [
            'playstyle' =>
                'Flexible defence, recovery and absorbing pressure before springing back into action.',
            'best_for' =>
                'Players who want a forgiving, resilient Monk with whimsical Sweet Kingdoms flavour.',
            'identity' =>
                'A soft-looking but remarkably resilient discipline built around yielding without collapsing.',
        ],
        'oath-of-inventory' => [
            'playstyle' =>
                'Protective, methodical support with a strong focus on safeguarding allies, resources and battlefield order.',
            'best_for' =>
                'Players who enjoy being the dependable centre of the Fellowship and solving problems through preparation and protection.',
            'identity' =>
                'A sacred vow of stewardship, accountability and ensuring that nothing entrusted to the Paladin is carelessly lost, spoiled or abandoned.',
        ],
        'oath-of-the-colonel' => [
            'playstyle' =>
                'Commanding frontline pressure, disciplined aggression and rallying allies through bold martial leadership.',
            'best_for' =>
                'Players who want a charismatic battle leader with theatrical confidence and unmistakable fast-feast flavour.',
            'identity' =>
                'A legendary fried-feast oath built around discipline, command presence and the sacred responsibility of leading from the front.',
        ],
        'oath-of-the-creamfather' => [
            'playstyle' =>
                'Protective support, restorative presence and calm control wrapped in an unexpectedly formidable sacred persona.',
            'best_for' =>
                'Players who enjoy supporting allies, controlling the emotional tone of encounters and mixing kindness with intimidating authority.',
            'identity' =>
                'A rich and ceremonial oath of hospitality, protection and loyalty whose gentleness should never be mistaken for weakness.',
        ],
    ];

    /**
     * @return array<string,string>
     */
    public function forPath(
        string $pathKey
    ): array {
        return self::GUIDES[
            sanitize_key($pathKey)
        ] ?? [];
    }
}
