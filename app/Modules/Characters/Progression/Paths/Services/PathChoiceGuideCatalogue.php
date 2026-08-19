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
