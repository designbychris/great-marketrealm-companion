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
        'oath-of-aroma' => [
            'playstyle' =>
                'Perceptive support, subtle control and influencing encounters through presence rather than brute force.',
            'best_for' =>
                'Players who like awareness, social pressure and a Paladin whose power feels atmospheric and difficult to ignore.',
            'identity' =>
                'A sacred tradition that treats scent, presence and memory as invisible banners capable of changing a room before steel is drawn.',
        ],
        'oath-of-clearance' => [
            'playstyle' =>
                'Decisive frontline play focused on endings, breaking stalemates and creating space for allies to move forward.',
            'best_for' =>
                'Players who enjoy making hard calls, finishing dangerous encounters and turning closure into momentum.',
            'identity' =>
                'A solemn oath that accepts every shelf, season and struggle eventually reaches its final mark-down.',
        ],
        'oath-of-seasoning' => [
            'playstyle' =>
                'Adaptive support and judgement, strengthening the Fellowship by applying the right pressure at the right moment.',
            'best_for' =>
                'Players who enjoy flexibility, tactical balance and improving the whole party rather than relying on one signature trick.',
            'identity' =>
                'A measured sacred philosophy built on the belief that greatness comes from balance, restraint and exactly the right finishing touch.',
        ],
        'oath-of-carbonation' => [
            'playstyle' =>
                'High-energy support, sudden bursts of momentum and an uplifting presence that keeps the Fellowship moving.',
            'best_for' =>
                'Players who want a lively Paladin with fast tempo, celebratory energy and explosive moments.',
            'identity' =>
                'A sparkling vow of pressure, uplift and sacred fizz that refuses to let courage go flat.',
        ],
        'oath-of-the-cleaver-saint' => [
            'playstyle' =>
                'Heavy frontline defence, disciplined weapon work and protecting allies by confronting the greatest threat directly.',
            'best_for' =>
                'Players who want a martial Paladin with strong Butcher Isles flavour and a clear protector role.',
            'identity' =>
                'A severe but honourable oath of sacred steel, precise force and standing where danger is thickest.',
        ],
        'pact-of-the-mascot' => [
            'playstyle' =>
                'Charm, spectacle, uncanny brand-loyalty magic and manipulating attention through a larger-than-life Patron presence.',
            'best_for' =>
                'Players who want a social, theatrical Warlock whose bargain feels weirdly cheerful until it becomes unsettling.',
            'identity' =>
                'A contract with an impossible Marketrealm mascot whose smiling presence may be far older, stranger and more demanding than the costume suggests.',
        ],
        'the-forgotten-freezer' => [
            'playstyle' =>
                'Cold control, eerie endurance and battlefield pressure inspired by abandoned aisles and things left too long in the dark.',
            'best_for' =>
                'Players who enjoy unsettling atmosphere, cold-themed magic and a Patron that feels distant, ancient and half-buried in frost.',
            'identity' =>
                'A pact sworn to something sleeping beyond the humming doors of a freezer aisle nobody remembers stocking.',
        ],
        'the-spoilfather' => [
            'playstyle' =>
                'Attrition, decay, curses and making enemies feel as though every passing round is turning against them.',
            'best_for' =>
                'Players who want a grim Warlock centred on corruption, inevitability and the dangerous power of rot.',
            'identity' =>
                'An agreement with a patient sovereign of spoilage who understands that all fresh things eventually change.',
        ],
        'the-sugar-fiend' => [
            'playstyle' =>
                'Temptation, bursts of magical energy, emotional manipulation and rewards that may carry a hidden cost.',
            'best_for' =>
                'Players who want a colourful, volatile Warlock whose magic is enticing, excessive and just slightly too sweet.',
            'identity' =>
                'A dazzling confectionery Patron offering impossible sweetness in exchange for promises that become harder to read the longer you stare at them.',
        ],
        'circle-of-eating-fresh' => [
            'playstyle' => 'Restore, cleanse and reward allies who remain close to natural terrain.',
            'best_for' => 'Players who want a bright support Druid centred on freshness, healing and purification.',
            'identity' => 'An embodiment of crisp air, revitalizing moisture, organic purity and vibrant greenery.',
        ],
        'circle-of-the-groveflame' => [
            'playstyle' => 'Empower fire magic with Wisdom, enhance Goodberry and develop fiery Wild Shape options.',
            'best_for' => 'Players who want an aggressive spice-and-flame Druid with strong Capsicum flavour.',
            'identity' => 'A keeper of sacred earth-fire who channels capsicum heat and the vitality of spice.',
        ],
        'circle-of-the-deep-soil' => [
            'playstyle' => 'Read memories from roots, halt movement and grow into an immovable earth-bound survivor.',
            'best_for' => 'Players who want slow, durable root magic and strong battlefield control.',
            'identity' => 'An earthy Druid who listens to buried memory and draws patient strength from soil and roots.',
        ],
        'circle-of-the-compost' => [
            'playstyle' => 'Turn death and decay into healing, poison, terrain control and eventually elemental transformation.',
            'best_for' => 'Players who want a complex decay-and-rebirth Druid with reactive support and offensive rot.',
            'identity' => 'A guardian of the cycle who knows that nothing is truly wasted and every ending feeds new growth.',
        ],
        'circle-of-curdle' => [
            'playstyle' => 'Spread microbial debuffs, manipulate spoilage and animate rotten matter into an ally.',
            'best_for' => 'Players who want a fermentation-and-decay controller with strong Dairyfolk and Fungifolk flavour.',
            'identity' => 'A microbial-cycle guardian who treats spoilage as transformation rather than failure.',
        ],
        'circle-of-the-churn' => [
            'playstyle' => 'Blend frost, healing and dairy transformation through Curd Form and cold battlefield control.',
            'best_for' => 'Players who want a Frostreem-themed Druid balancing restorative magic with frost-laced defence.',
            'identity' => 'A keeper of sacred dairy rituals who churns cream into power and milk into frost-bound magic.',
        ],
        'aislewarden-conclave' => [
            'playstyle' =>
                'Track a chosen quarry, ambush efficiently and keep moving through terrain and enemy pressure.',
            'best_for' =>
                'Players who want the classic mobile Marketrealm tracker and monster-hunter Ranger.',
            'identity' =>
                'A watcher of forgotten service passages, abandoned aisles and wild roads who always knows where the trail continues.',
        ],
        'deep-root-warden' => [
            'playstyle' =>
                'Control movement with roots, hold ground and become increasingly difficult to dislodge.',
            'best_for' =>
                'Players who want a durable battlefield controller with strong Rootlands flavour.',
            'identity' =>
                'A protector who fights beside the living earth and turns the ground itself into an ally.',
        ],
        'cold-vault-stalker' => [
            'playstyle' =>
                'Deal cold damage, slow enemy movement and thrive in frozen or icy environments.',
            'best_for' =>
                'Players who want a frost-themed hunter built around survival, pursuit and speed control.',
            'identity' =>
                'A hunter of the Cold Vaults who follows movement through frost long after safer travellers have turned back.',
        ],
        'conclave-of-the-forager' => [
            'playstyle' =>
                'Prepare herbal remedies, support allies, heal conditions and switch between restorative and harmful concoctions.',
            'best_for' =>
                'Players who want a non-spell-focused support Ranger with an apothecary toolkit.',
            'identity' =>
                'A travelling herbalist, healer, poisoner and survival expert carrying an entire field apothecary.',
        ],
        'spice-trail-hunter' => [
            'playstyle' =>
                'Infuse attacks with changing elemental seasonings and reposition after elemental strikes.',
            'best_for' =>
                'Players who enjoy adaptable damage types, mobile ranged combat and Sizzlarian flavour.',
            'identity' =>
                'A tracker who follows the smoke and treats enchanted seasoning as perfectly sensible ammunition technology.',
        ],
        'rindrunner' => [
            'playstyle' =>
                'Fight defensively at range, hunt underground threats and survive heavy retaliation.',
            'best_for' =>
                'Players who want a sturdy cave hunter with strong Dairy Dominion and Cheddar Cliffs flavour.',
            'identity' =>
                'A border and cave patrol specialist who makes sure the things beneath the Cheddar Cliffs stay there.',
        ],
        'seedshot-conclave' => [
            'playstyle' =>
                'Fire magical seed ammunition for restraint, area damage, forced movement, light and healing.',
            'best_for' =>
                'Players who want the most visually distinctive trick-shot Ranger and a broad tactical toolkit.',
            'identity' =>
                'A Melonian sharpshooter who grows ammunition and turns every impact point into the beginning of a plant problem.',
        ],
        'expiry-hunter' => [
            'playstyle' =>
                'Detect corruption, punish undead and decaying creatures, deny their healing and stop their return.',
            'best_for' =>
                'Players who want a darker specialist monster hunter focused on undead, aberrations and spoilage.',
            'identity' =>
                'A Recalled hunter who knows some things were discarded for a reason—and makes sure they stay gone.',
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
