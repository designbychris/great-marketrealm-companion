<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Encounter;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\InitiativeTable;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\EncounterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\SaveInitiativeRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use RuntimeException;

defined('ABSPATH') || exit;

final class InitiativeController
{
    public function __construct(
        private CampaignRepository $campaigns,
        private EncounterRepository $encounters,
        private CampaignRosterRepository $rosters,
        private CharacterRepository $characters,
        private DungeonMasterAccess $access,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {}

    public function index(string $id, string $encounterId): string
    {
        $campaign = $this->campaign($id);
        $encounter = $this->encounter($encounterId, $campaign);

        return $this->views->render(
            View::make(
                'dungeonmaster.initiative.index',
                [
                    'campaign' => $campaign,
                    'encounter' => $encounter,
                    'table' => $this->table($encounter, $campaign),
                    'flash' => [
                        'success' => $this->flash->get('success'),
                        'error' => $this->flash->get('error'),
                    ],
                ]
            )
        );
    }

    public function update(
        string $id,
        string $encounterId,
        SaveInitiativeRequest $request
    ): RedirectResponse {
        $campaign = $this->campaign($id);
        if ($campaign->isArchived()) {
            throw new RuntimeException(
                'Archived campaigns have a read-only Combat Console.'
            );
        }

        $encounter = $this->encounter($encounterId, $campaign);
        $current = $this->table($encounter, $campaign);
        $before = $current->combatants();
        $combatants = $this->sanitiseCombatants(
            $request->combatants(),
            $current
        );
        $round = $request->round();
        $turn = $request->turnIndex();
        $action = $request->action();
        $log = $current->log();

        if ($action === 'reset') {
            $combatants = $this->seedCombatants($encounter, $campaign);
            $round = 1;
            $turn = 0;
            $log = $this->log($log, 1, 'Combat Console reset from Encounter.');
        } else {
            if ($action === 'add') {
                $combatants = $this->addCombatant($combatants, $request);
            }
            if ($action === 'remove') {
                $combatants = $this->removeCombatant(
                    $combatants,
                    $request->removeId()
                );
                $turn = $this->normaliseTurn($turn, $combatants);
            }

            $log = $this->logChanges($log, $before, $combatants, $round);
        }

        if ($action === 'sort') {
            usort(
                $combatants,
                static fn (array $a, array $b): int =>
                    ((int) $b['initiative'] <=> (int) $a['initiative'])
                    ?: strcmp((string) $a['name'], (string) $b['name'])
            );
            $turn = 0;
            $log = $this->log($log, $round, 'Initiative order sorted.');
        }

        if ($action === 'advance' && $combatants !== []) {
            $turn++;
            if ($turn >= count($combatants)) {
                $turn = 0;
                $round++;
                $log = $this->log(
                    $log,
                    $round,
                    'A new combat round began.'
                );
            } else {
                $log = $this->log(
                    $log,
                    $round,
                    'Turn advanced to ' . $combatants[$turn]['name'] . '.'
                );
            }
        }

        if ($action === 'rewind' && $combatants !== []) {
            $turn--;
            if ($turn < 0) {
                if ($round > 1) {
                    $round--;
                    $turn = count($combatants) - 1;
                } else {
                    $turn = 0;
                }
            }
            $log = $this->log(
                $log,
                $round,
                'Turn rewound to ' . $combatants[$turn]['name'] . '.'
            );
        }

        if ($action === 'complete') {
            $encounter->update(
                $encounter->title(),
                $encounter->sessionId(),
                Encounter::STATUS_COMPLETED,
                $encounter->threat(),
                $encounter->location(),
                $encounter->adversaries(),
                $encounter->notes(),
                $encounter->characterIds(),
                $encounter->monsterGroups()
            );
            $this->encounters->save($encounter, $campaign);
            $log = $this->log($log, $round, 'Encounter completed.');
        } elseif ($encounter->status() === Encounter::STATUS_PREPARED) {
            $encounter->update(
                $encounter->title(),
                $encounter->sessionId(),
                Encounter::STATUS_RUNNING,
                $encounter->threat(),
                $encounter->location(),
                $encounter->adversaries(),
                $encounter->notes(),
                $encounter->characterIds(),
                $encounter->monsterGroups()
            );
            $this->encounters->save($encounter, $campaign);
        }

        $this->encounters->saveInitiative(
            $encounter->id(),
            $campaign,
            [
                'round' => $round,
                'turn_index' => $this->normaliseTurn($turn, $combatants),
                'combatants' => $combatants,
                'log' => array_values(array_slice($log, -80)),
            ]
        );

        $this->flash->success(
            $action === 'complete'
                ? 'The Encounter has been completed.'
                : 'The Combat Console has been updated.'
        );

        return $this->responses->redirect(
            $this->url($campaign->id(), $encounter->id())
        );
    }

    private function table(
        Encounter $encounter,
        Campaign $campaign
    ): InitiativeTable {
        $state = $this->encounters->initiativeForCampaign(
            $encounter->id(),
            $campaign
        );

        if ($state === []) {
            return InitiativeTable::fresh(
                $this->seedCombatants($encounter, $campaign)
            );
        }

        return InitiativeTable::restore(
            (int) ($state['round'] ?? 1),
            (int) ($state['turn_index'] ?? 0),
            is_array($state['combatants'] ?? null)
                ? $state['combatants']
                : [],
            is_array($state['log'] ?? null)
                ? $state['log']
                : []
        );
    }

    /** @return array<int,array<string,mixed>> */
    private function seedCombatants(
        Encounter $encounter,
        Campaign $campaign
    ): array {
        $out = [];
        $wanted = $encounter->characterIds();

        foreach ($this->rosters->members($campaign) as $member) {
            $owner = (int) ($member['player_id'] ?? 0);
            foreach (($member['character_ids'] ?? []) as $cid) {
                $cid = (string) $cid;
                if (! in_array($cid, $wanted, true)) {
                    continue;
                }
                try {
                    $character = $this->characters->findForOwner(
                        CharacterId::fromString($cid),
                        $owner
                    );
                    if ($character === null) {
                        continue;
                    }
                    $hp = $character->hitPoints();
                    $out[] = $this->combatantDefaults([
                        'id' => 'pc-' . $cid,
                        'type' => 'character',
                        'source_id' => $cid,
                        'name' => $character->name()->value(),
                        'modifier' => $character->initiative()->value(),
                        'current_hp' => $hp->current(),
                        'max_hp' => $hp->maximum(),
                        'temp_hp' => $hp->temporary(),
                    ]);
                } catch (\Throwable) {
                    continue;
                }
            }
        }

        foreach ($encounter->monsterGroups() as $group) {
            if (! is_array($group)) {
                continue;
            }
            $monsterId = sanitize_text_field(
                (string) ($group['monster_id'] ?? '')
            );
            $name = sanitize_text_field(
                (string) ($group['name'] ?? 'Creature')
            );
            $quantity = max(1, min(20, (int) ($group['quantity'] ?? 1)));
            $maxHp = max(1, min(99999, (int) ($group['max_hp'] ?? 1)));
            $modifier = max(
                -20,
                min(20, (int) ($group['initiative_modifier'] ?? 0))
            );

            for ($i = 1; $i <= $quantity; $i++) {
                $out[] = $this->combatantDefaults([
                    'id' => 'monster-'
                        . substr(sha1($monsterId . '-' . $i), 0, 12),
                    'type' => 'adversary',
                    'source_id' => $monsterId,
                    'name' => $quantity > 1 ? $name . ' #' . $i : $name,
                    'modifier' => $modifier,
                    'armor_class' => max(
                        0,
                        min(99, (int) ($group['armor_class'] ?? 0))
                    ),
                    'challenge' => sanitize_text_field(
                        (string) ($group['challenge'] ?? '')
                    ),
                    'current_hp' => $maxHp,
                    'max_hp' => $maxHp,
                ]);
            }
        }

        $lines = preg_split('/\R+/', trim($encounter->adversaries())) ?: [];
        foreach ($lines as $i => $line) {
            $name = trim($line);
            if ($name === '') {
                continue;
            }
            $out[] = $this->combatantDefaults([
                'id' => 'foe-' . substr(sha1($name . '-' . $i), 0, 12),
                'type' => 'adversary',
                'source_id' => '',
                'name' => $name,
            ]);
        }

        return $out;
    }

    /**
     * @param array<mixed> $submitted
     * @return array<int,array<string,mixed>>
     */
    private function sanitiseCombatants(
        array $submitted,
        InitiativeTable $current
    ): array {
        $allowed = [];
        foreach ($current->combatants() as $combatant) {
            $allowed[(string) ($combatant['id'] ?? '')] = $combatant;
        }

        $out = [];
        foreach ($submitted as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = sanitize_text_field((string) ($row['id'] ?? ''));
            if (! isset($allowed[$id])) {
                continue;
            }

            $base = $this->combatantDefaults($allowed[$id]);
            $base['initiative'] = max(
                -20,
                min(99, (int) ($row['initiative'] ?? 0))
            );
            $base['current_hp'] = max(
                0,
                min(99999, (int) ($row['current_hp'] ?? 0))
            );
            $base['max_hp'] = max(
                0,
                min(99999, (int) ($row['max_hp'] ?? 0))
            );
            $base['temp_hp'] = max(
                0,
                min(99999, (int) ($row['temp_hp'] ?? 0))
            );
            $base['conditions'] = sanitize_text_field(
                (string) ($row['conditions'] ?? '')
            );
            $base['state'] = in_array(
                (string) ($row['state'] ?? ''),
                ['standing', 'unconscious', 'defeated'],
                true
            ) ? (string) $row['state'] : 'standing';
            $base['concentrating'] = ! empty($row['concentrating']);
            $base['notes'] = sanitize_textarea_field(
                (string) ($row['notes'] ?? '')
            );
            $base['defeated'] = $base['state'] === 'defeated';
            $out[] = $base;
        }

        return $out;
    }

    /** @param array<string,mixed> $values */
    private function combatantDefaults(array $values): array
    {
        return array_merge(
            [
                'id' => '',
                'type' => 'adversary',
                'source_id' => '',
                'name' => 'Combatant',
                'initiative' => 0,
                'modifier' => 0,
                'armor_class' => 0,
                'challenge' => '',
                'current_hp' => 0,
                'max_hp' => 0,
                'temp_hp' => 0,
                'conditions' => '',
                'state' => ! empty($values['defeated'])
                    ? 'defeated'
                    : 'standing',
                'concentrating' => false,
                'notes' => '',
                'defeated' => false,
            ],
            $values
        );
    }

    /** @param array<int,array<string,mixed>> $combatants */
    private function addCombatant(
        array $combatants,
        SaveInitiativeRequest $request
    ): array {
        $name = sanitize_text_field($request->newName());
        if ($name === '') {
            return $combatants;
        }

        $maxHp = max(0, min(99999, $request->newMaxHp()));
        $combatants[] = $this->combatantDefaults([
            'id' => 'manual-' . wp_generate_uuid4(),
            'type' => $request->newType() === 'ally' ? 'ally' : 'adversary',
            'name' => $name,
            'modifier' => max(-20, min(20, $request->newModifier())),
            'current_hp' => $maxHp,
            'max_hp' => $maxHp,
        ]);

        return $combatants;
    }

    /**
     * @param array<int,array<string,mixed>> $combatants
     * @return array<int,array<string,mixed>>
     */
    private function removeCombatant(array $combatants, string $removeId): array
    {
        return array_values(
            array_filter(
                $combatants,
                static fn (array $combatant): bool =>
                    (string) ($combatant['id'] ?? '') !== $removeId
            )
        );
    }

    /** @param array<int,array<string,mixed>> $combatants */
    private function normaliseTurn(int $turn, array $combatants): int
    {
        if ($combatants === []) {
            return 0;
        }

        return min(max(0, $turn), count($combatants) - 1);
    }

    /**
     * @param array<int,array<string,mixed>> $log
     * @param array<int,array<string,mixed>> $before
     * @param array<int,array<string,mixed>> $after
     * @return array<int,array<string,mixed>>
     */
    private function logChanges(
        array $log,
        array $before,
        array $after,
        int $round
    ): array {
        $beforeMap = [];
        foreach ($before as $combatant) {
            $beforeMap[(string) ($combatant['id'] ?? '')] = $combatant;
        }
        $afterMap = [];

        foreach ($after as $combatant) {
            $id = (string) ($combatant['id'] ?? '');
            $afterMap[$id] = $combatant;
            if (! isset($beforeMap[$id])) {
                $log = $this->log(
                    $log,
                    $round,
                    $combatant['name'] . ' joined the combat.'
                );
                continue;
            }

            $old = $beforeMap[$id];
            $name = (string) ($combatant['name'] ?? 'Combatant');
            $hpDelta = (int) ($combatant['current_hp'] ?? 0)
                - (int) ($old['current_hp'] ?? 0);
            if ($hpDelta !== 0) {
                $log = $this->log(
                    $log,
                    $round,
                    $name . ($hpDelta < 0 ? ' took ' : ' recovered ')
                    . abs($hpDelta) . ' HP.'
                );
            }

            $tempDelta = (int) ($combatant['temp_hp'] ?? 0)
                - (int) ($old['temp_hp'] ?? 0);
            if ($tempDelta !== 0) {
                $log = $this->log(
                    $log,
                    $round,
                    $name . ' temporary HP changed to '
                    . (int) ($combatant['temp_hp'] ?? 0) . '.'
                );
            }

            if (($combatant['conditions'] ?? '') !== ($old['conditions'] ?? '')) {
                $conditions = trim((string) ($combatant['conditions'] ?? ''));
                $log = $this->log(
                    $log,
                    $round,
                    $name . ($conditions === ''
                        ? ' cleared all conditions.'
                        : ' conditions: ' . $conditions . '.')
                );
            }

            if (
                ! empty($combatant['concentrating'])
                !== ! empty($old['concentrating'])
            ) {
                $log = $this->log(
                    $log,
                    $round,
                    $name . (! empty($combatant['concentrating'])
                        ? ' began concentrating.'
                        : ' stopped concentrating.')
                );
            }

            if (($combatant['state'] ?? '') !== ($old['state'] ?? '')) {
                $log = $this->log(
                    $log,
                    $round,
                    $name . ' is now ' . (string) $combatant['state'] . '.'
                );
            }
        }

        foreach ($beforeMap as $id => $combatant) {
            if (! isset($afterMap[$id])) {
                $log = $this->log(
                    $log,
                    $round,
                    (string) ($combatant['name'] ?? 'Combatant')
                    . ' was removed from the live table.'
                );
            }
        }

        return $log;
    }

    /**
     * @param array<int,array<string,mixed>> $log
     * @return array<int,array<string,mixed>>
     */
    private function log(array $log, int $round, string $message): array
    {
        $log[] = [
            'round' => max(1, $round),
            'message' => sanitize_text_field($message),
            'recorded_at' => current_time('mysql'),
        ];

        return array_values(array_slice($log, -80));
    }

    private function campaign(string $id): Campaign
    {
        if (! $this->access->allows()) {
            status_header(403);
            throw new RuntimeException(
                'The Combat Console is sealed to Dungeon Masters.'
            );
        }
        $campaign = $this->campaigns->findForOwner(
            $id,
            get_current_user_id()
        );
        if (! $campaign instanceof Campaign) {
            throw new RuntimeException(
                'Campaign not found in this Dungeon Master’s Register.'
            );
        }
        return $campaign;
    }

    private function encounter(string $id, Campaign $campaign): Encounter
    {
        $encounter = $this->encounters->findForCampaign($id, $campaign);
        if (! $encounter instanceof Encounter) {
            throw new RuntimeException(
                'Encounter not found on this Campaign Board.'
            );
        }
        return $encounter;
    }

    private function url(string $campaignId, string $encounterId): string
    {
        return add_query_arg(
            'gmrc_route',
            'dungeon-master/campaigns/' . $campaignId
            . '/encounters/' . $encounterId . '/initiative',
            home_url('/companion/')
        );
    }
}
