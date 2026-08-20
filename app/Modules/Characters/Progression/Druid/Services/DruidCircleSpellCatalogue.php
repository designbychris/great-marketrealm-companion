<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services;

defined('ABSPATH') || exit;

/**
 * Circle spell grants explicitly supplied for Circle of the Churn.
 */
final class DruidCircleSpellCatalogue
{
    /** @return array<int,array{level:int,spells:array<int,string>}> */
    public function forCircle(string $circleKey): array
    {
        if (sanitize_key($circleKey) !== 'circle-of-the-churn') {
            return [];
        }

        return [
            ['level'=>3,'spells'=>['Ice Knife','Goodberry']],
            ['level'=>5,'spells'=>['Lesser Restoration','Snilloc\'s Snowball Swarm']],
            ['level'=>7,'spells'=>['Aura of Vitality','Sleet Storm']],
            ['level'=>9,'spells'=>['Freedom of Movement','Ice Storm']],
        ];
    }

    /** @return array<int,string> */
    public function unlocked(string $circleKey, int $druidLevel): array
    {
        $spells=[];
        foreach ($this->forCircle($circleKey) as $grant) {
            if ($grant['level'] <= $druidLevel) {
                $spells=array_merge($spells,$grant['spells']);
            }
        }
        return $spells;
    }
}
