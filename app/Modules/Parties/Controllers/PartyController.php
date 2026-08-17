<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Actions\AddPartyMemberAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\ChangePartyMemberRoleAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\CreatePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\DeletePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RemovePartyMemberAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RenamePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Requests\AddPartyMemberRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\StorePartyRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\UpdatePartyMemberRoleRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\UpdatePartyRequest;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use RuntimeException;

defined('ABSPATH') || exit;

final class PartyController
{
    public function __construct(
        private PartyFinder $parties,
        private CharacterRepositoryInterface $characters,
        private CreatePartyAction $createParty,
        private AddPartyMemberAction $addMember,
        private RemovePartyMemberAction $removeMember,
        private ChangePartyMemberRoleAction $changeRole,
        private RenamePartyAction $renameParty,
        private DeletePartyAction $deleteParty,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {
    }

    public function index(): string
    {
        return $this->views->render(
            View::make(
                'parties.index',
                [
                    'parties' => $this->parties->all(
                        $this->ownerId()
                    ),
                ]
            )
        );
    }

    public function create(): string
    {
        return $this->views->render(
            View::make('parties.create')
        );
    }

    public function store(
        StorePartyRequest $request
    ): RedirectResponse {
        $party = $this->createParty->handle(
            PartyName::fromString(
                $request->name()
            ),
            $this->ownerId()
        );

        $this->flash->success(
            'Your Fellowship has been entered into the Guild Register.'
        );

        return $this->responses->redirect(
            $this->partyUrl(
                $party->id()
            )
        );
    }

    public function show(string $id): string
    {
        $party = $this->party($id);

        return $this->views->render(
            View::make(
                'parties.show',
                [
                    'party' => $party,
                    'characters' => $this
                        ->characters
                        ->all(),
                ]
            )
        );
    }

    public function edit(string $id): string
    {
        return $this->views->render(
            View::make(
                'parties.edit',
                [
                    'party' => $this->party($id),
                ]
            )
        );
    }

    public function update(
        string $id,
        UpdatePartyRequest $request
    ): RedirectResponse {
        $party = $this->renameParty->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            PartyName::fromString(
                $request->name()
            )
        );

        $this->flash->success(
            'The Fellowship name has been updated.'
        );

        return $this->responses->redirect(
            $this->partyUrl(
                $party->id()
            )
        );
    }

    public function destroy(
        string $id
    ): RedirectResponse {
        $party = $this->party($id);
        $name = $party->name()->value();

        $this->deleteParty->handle(
            $party->id(),
            $this->ownerId()
        );

        $this->flash->success(
            sprintf(
                '%s has been removed from the Fellowship Register.',
                $name
            )
        );

        return $this->responses->redirect(
            $this->partiesUrl()
        );
    }

    public function addMember(
        string $id,
        AddPartyMemberRequest $request
    ): RedirectResponse {
        $party = $this->addMember->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            CharacterId::fromString(
                $request->characterId()
            ),
            PartyMembershipRole::fromString(
                $request->role()
            )
        );

        $this->flash->success(
            'The adventurer has joined the Fellowship.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
        );
    }

    public function removeMember(
        string $id,
        string $character
    ): RedirectResponse {
        $party = $this->removeMember->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            CharacterId::fromString(
                $character
            )
        );

        $this->flash->success(
            'The adventurer has left the Fellowship.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
        );
    }

    public function updateMemberRole(
        string $id,
        string $character,
        UpdatePartyMemberRoleRequest $request
    ): RedirectResponse {
        $party = $this->changeRole->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            CharacterId::fromString(
                $character
            ),
            PartyMembershipRole::fromString(
                $request->role()
            )
        );

        $this->flash->success(
            'The Fellowship role has been updated.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
        );
    }

    private function party(string $id): \GreatMarketrealmCompanion\Modules\Parties\Models\Party
    {
        return $this->parties->find(
            PartyId::fromString($id),
            $this->ownerId()
        );
    }

    private function ownerId(): PartyOwnerId
    {
        if (! is_user_logged_in()) {
            throw new RuntimeException(
                'A signed-in Guild account is required to access Fellowships.'
            );
        }

        return PartyOwnerId::fromInt(
            get_current_user_id()
        );
    }

    private function partyUrl(
        PartyId $partyId
    ): string {
        return add_query_arg(
            'gmrc_route',
            'parties/' . rawurlencode(
                $partyId->value()
            ),
            home_url('/companion/')
        );
    }

    private function partiesUrl(): string
    {
        return add_query_arg(
            'gmrc_route',
            'parties',
            home_url('/companion/')
        );
    }
}
