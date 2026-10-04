<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Services;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Planning\PlanningRecordType;

defined('ABSPATH') || exit;

/**
 * Phase III.17 planning contract for the Dungeon Master's Desk.
 *
 * This intentionally contains no persistence. Later phases may add their own
 * repositories while the Desk, Conspiracy Board and Tabletop bridge share the
 * same planning vocabulary.
 */
final class KeeperWorkspace
{
    /** @return list<array{key:string,label:string,icon:string,description:string,next_phase:string,status?:string,route?:string,record_types:list<string>}> */
    public function forthcomingTools(): array
    {
        return [
            [
                'key' => 'gazetteer',
                'label' => 'Keeper’s Gazetteer',
                'icon' => '🗺️',
                'description' => 'Give campaign locations a permanent home before they become maps on the Tabletop.',
                'next_phase' => 'III.17.2',
                'status' => 'open',
                'route' => 'dungeon-master/campaigns',
                'record_types' => [PlanningRecordType::LOCATION],
            ],
            [
                'key' => 'dramatis-personae',
                'label' => 'Dramatis Personae',
                'icon' => '🎭',
                'description' => 'Keep the people and factions behind the adventure close to the campaign that owns them.',
                'next_phase' => 'III.17.3',
                'status' => 'open',
                'route' => 'dungeon-master/campaigns',
                'record_types' => [PlanningRecordType::PERSON, PlanningRecordType::FACTION],
            ],
            [
                'key' => 'evidence-register',
                'label' => 'Evidence Register',
                'icon' => '🔎',
                'description' => 'Preserve clues, secrets and plot threads as records that can later be pinned and connected.',
                'next_phase' => 'III.17.4',
                'status' => 'open',
                'route' => 'dungeon-master/campaigns',
                'record_types' => [PlanningRecordType::EVIDENCE, PlanningRecordType::THREAD],
            ],
            [
                'key' => 'conspiracy-board',
                'label' => 'Conspiracy Board',
                'icon' => '🧷',
                'description' => 'Arrange campaign records on a tactile planning board without making the board their source of truth.',
                'next_phase' => 'III.17.5',
                'status' => 'open',
                'route' => 'dungeon-master/campaigns',
                'record_types' => PlanningRecordType::all(),
            ],
            [
                'key' => 'cartographers-bench',
                'label' => 'Cartographer’s Bench',
                'icon' => '🏘️',
                'description' => 'Hand campaign locations to the Tabletop scene builder when the GMRT workshop is ready.',
                'next_phase' => 'IV.36',
                'record_types' => [PlanningRecordType::LOCATION],
            ],
        ];
    }
}
