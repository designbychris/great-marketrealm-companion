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
use GreatMarketrealmCompanion\Modules\Parties\Actions\ChangePartyMemberOfficeAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\CreatePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\DeletePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RemovePartyMemberAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RenamePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\UpdatePartyStandardAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\UpdatePartyCharterAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\DepositPartyTreasuryAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\WithdrawPartyTreasuryAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\AddPartyChronicleNoteAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\TransferCoinBetweenCharacterAndPartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOffice;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyStandard;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCharter;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCoinTransferDirection;
use GreatMarketrealmCompanion\Modules\Parties\Requests\AddPartyMemberRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\StorePartyRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\UpdatePartyMemberRoleRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\UpdatePartyMemberOfficeRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\UpdatePartyRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\UpdatePartyStandardRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\UpdatePartyCharterRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\DepositPartyTreasuryRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\WithdrawPartyTreasuryRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\AddPartyChronicleNoteRequest;
use GreatMarketrealmCompanion\Modules\Parties\Requests\TransferPartyCoinRequest;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use GreatMarketrealmCompanion\Modules\Parties\Services\SharedFellowshipAccess;
use GreatMarketrealmCompanion\Modules\Parties\Presenters\FellowshipPresenter;
use RuntimeException;

defined('ABSPATH') || exit;

final class PartyController
{
    public function __construct(
        private PartyFinder $parties,
        private SharedFellowshipAccess $sharedFellowships,
        private CharacterRepositoryInterface $characters,
        private FellowshipPresenter $fellowships,
        private CreatePartyAction $createParty,
        private AddPartyMemberAction $addMember,
        private RemovePartyMemberAction $removeMember,
        private ChangePartyMemberRoleAction $changeRole,
        private ChangePartyMemberOfficeAction $changeOffice,
        private RenamePartyAction $renameParty,
        private UpdatePartyStandardAction $updateStandard,
        private UpdatePartyCharterAction $updateCharter,
        private DepositPartyTreasuryAction $depositTreasury,
        private WithdrawPartyTreasuryAction $withdrawTreasury,
        private AddPartyChronicleNoteAction $addChronicleNote,
        private TransferCoinBetweenCharacterAndPartyAction $transferCoin,
        private DeletePartyAction $deleteParty,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {
    }

    public function index(): string
    {
        $accountId = $this->accountId();
        $presented = $this->fellowships->presentMany(
            $this->sharedFellowships->allForAccount($accountId)
        );

        foreach ($presented as &$fellowship) {
            $party = $fellowship['party'] ?? null;
            $fellowship['can_manage'] = $party instanceof \GreatMarketrealmCompanion\Modules\Parties\Models\Party
                && $this->sharedFellowships->canManage($party, $accountId);
        }
        unset($fellowship);

        return $this->views->render(
            View::make(
                'parties.index',
                ['fellowships' => $presented]
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
        $accountId = $this->accountId();
        $party = $this->sharedFellowships->findForAccount(
            PartyId::fromString($id),
            $accountId
        );
        $presentation = $this->fellowships->present($party);
        $presentation['can_manage'] =
            $this->sharedFellowships->canManage($party, $accountId);

        return $this->views->render(
            View::make('parties.show', $presentation)
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

    public function updateStandard(
        string $id,
        UpdatePartyStandardRequest $request
    ): RedirectResponse {
        $party = $this->updateStandard->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            PartyStandard::make(
                $request->palette(),
                $request->emblem(),
                $request->ornament()
            )
        );

        $this->flash->success(
            'The Fellowship Standard has been updated.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
        );
    }

    public function updateCharter(
        string $id,
        UpdatePartyCharterRequest $request
    ): RedirectResponse {
        $party = $this->updateCharter->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            PartyCharter::make(
                $request->motto(),
                $request->description(),
                $request->statement()
            )
        );

        $this->flash->success(
            'The Company Charter has been updated.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
        );
    }

    public function depositTreasury(
        string $id,
        DepositPartyTreasuryRequest $request
    ): RedirectResponse {
        $party = $this->depositTreasury->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            PartyTreasuryMoney::fromCoins(
                $request->gold(),
                $request->silver(),
                $request->copper()
            ),
            $request->note()
        );

        $this->flash->success(
            'Funds have been deposited into the Fellowship Treasury.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
        );
    }

    public function withdrawTreasury(
        string $id,
        WithdrawPartyTreasuryRequest $request
    ): RedirectResponse {
        $party = $this->withdrawTreasury->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            PartyTreasuryMoney::fromCoins(
                $request->gold(),
                $request->silver(),
                $request->copper()
            ),
            $request->note()
        );

        $this->flash->success(
            'Funds have been withdrawn from the Fellowship Treasury.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
        );
    }

    public function transferCoin(
        string $id,
        TransferPartyCoinRequest $request
    ): RedirectResponse {
        $direction = PartyCoinTransferDirection::fromString(
            $request->direction()
        );

        $amount = PartyTreasuryMoney::fromCoins(
            $request->gold(),
            $request->silver(),
            $request->copper()
        );

        if ($amount->isZero()) {
            $this->flash->error(
                'A Fellowship transfer must contain at least one coin.'
            );

            return $this->responses->redirect(
                $this->partyUrl(
                    PartyId::fromString($id),
                    'treasury'
                )
            );
        }

        try {
            $accessibleParty = $this->sharedFellowships->findForAccount(
                PartyId::fromString($id),
                $this->accountId()
            );
            $party = $this->transferCoin->handle(
                $accessibleParty->id(),
                $accessibleParty->ownerId(),
                CharacterId::fromString(
                    $request->characterId()
                ),
                $direction,
                $amount,
                $request->transferId(),
                $request->note()
            );
        } catch (\Throwable $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->partyUrl(
                    PartyId::fromString($id),
                    'treasury'
                )
            );
        }

        $this->flash->success(
            $direction->isToTreasury()
                ? 'Coin has moved from the adventurer’s purse into the Fellowship Treasury.'
                : 'Coin has moved from the Fellowship Treasury into the adventurer’s purse.'
        );

        return $this->responses->redirect(
            $this->partyUrl(
                $party->id(),
                'treasury'
            )
        );
    }

    public function addChronicleNote(
        string $id,
        AddPartyChronicleNoteRequest $request
    ): RedirectResponse {
        $party = $this->addChronicleNote->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            $request->title(),
            $request->content(),
            get_current_user_id()
        );

        $this->flash->success(
            'The adventure note has been added to the Company Chronicle.'
        );

        return $this->responses->redirect(
            $this->partyUrl($party->id())
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

    public function updateMemberOffice(
        string $id,
        string $character,
        UpdatePartyMemberOfficeRequest $request
    ): RedirectResponse {
        $party = $this->changeOffice->handle(
            PartyId::fromString($id),
            $this->ownerId(),
            CharacterId::fromString(
                $character
            ),
            PartyOffice::fromString(
                $request->office()
            )
        );

        $this->flash->success(
            'The Company Office has been updated.'
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
        return PartyOwnerId::fromInt($this->accountId());
    }

    private function accountId(): int
    {
        if (! is_user_logged_in()) {
            throw new RuntimeException(
                'A signed-in Guild account is required to access Fellowships.'
            );
        }

        return get_current_user_id();
    }

    private function partyUrl(
        PartyId $partyId,
        ?string $hallTab = null
    ): string {
        $url = add_query_arg(
            'gmrc_route',
            'parties/' . rawurlencode(
                $partyId->value()
            ),
            home_url('/companion/')
        );

        if ($hallTab !== null) {
            $url = add_query_arg(
                'gmrc_fellowship_tab',
                sanitize_key($hallTab),
                $url
            );
        }

        return $url;
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
