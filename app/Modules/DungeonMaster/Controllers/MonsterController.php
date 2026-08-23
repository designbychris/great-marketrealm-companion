<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Monster;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories\CanonicalBestiary;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\MonsterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\SaveMonsterRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use RuntimeException;

defined('ABSPATH') || exit;

final class MonsterController
{
    public function __construct(
        private MonsterRepository $monsters,
        private CanonicalBestiary $canonicalBestiary,
        private DungeonMasterAccess $access,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {}

    public function index(): string
    {
        $ownerId = $this->ownerId();
        return $this->render('dungeonmaster.monsters.index', [
            'monsters' => $this->monsters->allForOwner($ownerId),
            'canonicalMonsters' => $this->canonicalBestiary->all(),
        ]);
    }

    public function create(): string
    {
        $this->guard();
        return $this->render('dungeonmaster.monsters.create', ['monster' => null]);
    }

    public function store(SaveMonsterRequest $request): RedirectResponse
    {
        $ownerId = $this->ownerId();
        $monster = Monster::create(
            $ownerId,
            $request->name(),
            $request->creatureType(),
            $request->size(),
            $request->armorClass(),
            $request->maxHp(),
            $request->speed(),
            $request->strength(),
            $request->dexterity(),
            $request->constitution(),
            $request->intelligence(),
            $request->wisdom(),
            $request->charisma(),
            $request->challenge(),
            $request->traits(),
            $request->actions(),
            $request->notes()
        );
        $this->monsters->save($monster);
        $this->flash->success('The creature has been entered into the Monster Ledger.');
        return $this->responses->redirect($this->url($monster->id()));
    }

    public function showCanonical(string $monsterKey): string
    {
        return $this->renderCanonical($monsterKey);
    }

    public function show(string $monsterId): string
    {
        $this->guard();
        if (str_starts_with($monsterId, 'canonical:')) {
            return $this->renderCanonical($monsterId);
        }

        return $this->render('dungeonmaster.monsters.show', [
            'monster' => $this->monster($monsterId),
        ]);
    }

    public function edit(string $monsterId): string
    {
        return $this->render('dungeonmaster.monsters.edit', [
            'monster' => $this->monster($monsterId),
        ]);
    }

    public function update(string $monsterId, SaveMonsterRequest $request): RedirectResponse
    {
        $monster = $this->monster($monsterId);
        if ($monster->isArchived()) {
            throw new RuntimeException('Archived Monster Ledger entries are preserved as read-only records.');
        }
        $monster->update(
            $request->name(),
            $request->creatureType(),
            $request->size(),
            $request->armorClass(),
            $request->maxHp(),
            $request->speed(),
            $request->strength(),
            $request->dexterity(),
            $request->constitution(),
            $request->intelligence(),
            $request->wisdom(),
            $request->charisma(),
            $request->challenge(),
            $request->traits(),
            $request->actions(),
            $request->notes()
        );
        $this->monsters->save($monster);
        $this->flash->success('The Monster Ledger stat block has been updated.');
        return $this->responses->redirect($this->url($monster->id()));
    }

    public function archive(string $monsterId): RedirectResponse
    {
        $monster = $this->monster($monsterId);
        $monster->archive();
        $this->monsters->save($monster);
        $this->flash->success('The creature has been archived without disturbing existing Encounter snapshots.');
        return $this->responses->redirect($this->registerUrl());
    }

    private function renderCanonical(string $monsterId): string
    {
        $this->guard();
        $monster = $this->canonicalBestiary->find($monsterId);
        if ($monster === null) {
            throw new RuntimeException('Canonical creature not found in the Marketrealm Bestiary.');
        }

        return $this->render('dungeonmaster.monsters.canonical', [
            'monster' => $monster,
        ]);
    }

    private function monster(string $monsterId): Monster
    {
        $ownerId = $this->ownerId();
        $monster = $this->monsters->findForOwner($monsterId, $ownerId);
        if (! $monster instanceof Monster) {
            throw new RuntimeException('Creature not found in this Dungeon Master’s Monster Ledger.');
        }
        return $monster;
    }

    private function ownerId(): int
    {
        $this->guard();
        return get_current_user_id();
    }

    private function guard(): void
    {
        if (! $this->access->allows()) {
            status_header(403);
            throw new RuntimeException('The Monster Ledger is sealed to Dungeon Masters.');
        }
    }

    /** @param array<string,mixed> $extra */
    private function render(string $view, array $extra): string
    {
        return $this->views->render(View::make($view, array_merge([
            'flash' => [
                'success' => $this->flash->get('success'),
                'error' => $this->flash->get('error'),
            ],
        ], $extra)));
    }

    private function url(string $monsterId): string
    {
        return add_query_arg(
            'gmrc_route',
            'dungeon-master/monsters/' . $monsterId,
            home_url('/companion/')
        );
    }

    private function registerUrl(): string
    {
        return add_query_arg(
            'gmrc_route',
            'dungeon-master/monsters',
            home_url('/companion/')
        );
    }
}
