<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services;

defined('ABSPATH') || exit;

/**
 * Supplied Cleric Domain spell tables.
 *
 * Golden Arches remains intentionally partial because only Grease and
 * Create Food and Water were explicitly supplied as signature spells.
 */
final class ClericDomainSpellCatalogue
{
    /** @var array<string,array<int,array{level:int,spells:array<int,string>}>> */
    private const TABLES = [
        'domain-of-sweetness' => [
            ['level'=>1,'spells'=>['Faerie Fire','Goodberry']],
            ['level'=>3,'spells'=>['Mirror Image','Aid']],
            ['level'=>5,'spells'=>['Hypnotic Pattern','Beacon of Hope']],
            ['level'=>7,'spells'=>['Freedom of Movement','Guardian of Faith']],
            ['level'=>9,'spells'=>['Wall of Force','Greater Restoration']],
        ],
        'domain-of-the-golden-arches' => [
            ['level'=>1,'spells'=>['Grease']],
            ['level'=>5,'spells'=>['Create Food and Water']],
        ],
        'domain-of-dairy' => [
            ['level'=>1,'spells'=>['Sanctuary','Grease']],
            ['level'=>3,'spells'=>['Misty Step','Hold Person']],
            ['level'=>5,'spells'=>['Create Food and Water','Slow']],
            ['level'=>7,'spells'=>['Guardian of Faith','Freedom of Movement']],
            ['level'=>9,'spells'=>['Wall of Force','Awaken']],
        ],
        'domain-of-seasoning' => [
            ['level'=>1,'spells'=>['Heroism','Faerie Fire']],
            ['level'=>3,'spells'=>['Enhance Ability','Spike Growth']],
            ['level'=>5,'spells'=>['Beacon of Hope','Stinking Cloud']],
            ['level'=>7,'spells'=>['Aura of Life','Freedom of Movement']],
            ['level'=>9,'spells'=>['Geas','Cloudkill']],
        ],
        'domain-of-cultivation' => [
            ['level'=>1,'spells'=>['Purify Food and Drink','Healing Word']],
            ['level'=>3,'spells'=>['Lesser Restoration','Calm Emotions']],
            ['level'=>5,'spells'=>['Create Food and Water','Remove Curse']],
            ['level'=>7,'spells'=>['Aura of Purity','Divination']],
            ['level'=>9,'spells'=>['Hallow','Greater Restoration']],
        ],
        'domain-of-fermentation' => [
            ['level'=>1,'spells'=>['Goodberry','Faerie Fire']],
            ['level'=>3,'spells'=>['Misty Step','Enlarge/Reduce']],
            ['level'=>5,'spells'=>['Stinking Cloud','Create Food and Water']],
            ['level'=>7,'spells'=>['Polymorph','Hallucinatory Terrain']],
            ['level'=>9,'spells'=>['Contagion','Wall of Stone']],
        ],
    ];

    /** @return array<int,array{level:int,spells:array<int,string>}> */
    public function forDomain(
        string $domainKey
    ): array {
        return self::TABLES[
            sanitize_key($domainKey)
        ] ?? [];
    }

    /** @return array<int,string> */
    public function unlocked(
        string $domainKey,
        int $clericLevel
    ): array {
        $spells = [];

        foreach (
            $this->forDomain($domainKey)
            as $grant
        ) {
            if ($grant['level'] <= $clericLevel) {
                $spells = array_merge(
                    $spells,
                    $grant['spells']
                );
            }
        }

        return $spells;
    }
}
