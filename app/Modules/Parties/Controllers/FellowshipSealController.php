<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RedeemFellowshipSealAction;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\FellowshipSealRepository;
use GreatMarketrealmCompanion\Modules\Parties\Requests\RedeemFellowshipSealRequest;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use RuntimeException;

defined('ABSPATH') || exit;

final class FellowshipSealController
{
    public function __construct(
        private FellowshipSealRepository $seals,
        private PartyFinder $parties,
        private CharacterRepository $characters,
        private RedeemFellowshipSealAction $redeemSeal,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {
    }

    public function index(): string
    {
        $accountId = $this->guardPlayer();

        return $this->views->render(
            View::make('parties.fellowship-seal', [
                'characters' => $this->characters->allForOwner($accountId),
                'flash' => $this->flashData(),
            ])
        );
    }

    public function redeem(
        RedeemFellowshipSealRequest $request
    ): RedirectResponse {
        $accountId = $this->guardPlayer();

        try {
            $party = $this->redeemSeal->handle(
                $request->code(),
                CharacterId::fromString($request->characterId()),
                $accountId
            );
        } catch (\Throwable $exception) {
            $this->flash->error($exception->getMessage());
            return $this->responses->redirect($this->redeemUrl());
        }

        $this->flash->success(
            'Fellowship Seal accepted. Your adventurer has joined '
            . $party->name()->value()
            . '.'
        );

        return $this->responses->redirect($this->partyUrl($party));
    }

    public function manage(string $id): string
    {
        $party = $this->ownedParty($id);

        return $this->views->render(
            View::make('parties.fellowship-seal-manage', [
                'party' => $party,
                'seal' => $this->seals->current($party),
                'flash' => $this->flashData(),
            ])
        );
    }

    public function issue(string $id): RedirectResponse
    {
        $party = $this->ownedParty($id);
        $seal = $this->seals->issue($party);

        $this->flash->success(
            'A fresh Fellowship Seal has been issued: ' . $seal->code()
        );

        return $this->responses->redirect($this->manageUrl($party));
    }

    public function revoke(string $id): RedirectResponse
    {
        $party = $this->ownedParty($id);
        $this->seals->revoke($party);
        $this->flash->success(
            'The Fellowship Seal has been revoked. Existing members remain in the company.'
        );

        return $this->responses->redirect($this->manageUrl($party));
    }

    private function ownedParty(string $id): Party
    {
        $accountId = get_current_user_id();

        if ($accountId < 1 || ! user_can($accountId, 'gmrc_access_companion')) {
            throw new RuntimeException(
                'Fellowship Seal controls require a signed-in Guild account.'
            );
        }

        return $this->parties->find(
            PartyId::fromString($id),
            PartyOwnerId::fromInt($accountId)
        );
    }

    private function guardPlayer(): int
    {
        $accountId = get_current_user_id();

        if (
            $accountId < 1
            || ! user_can($accountId, 'gmrc_access_companion')
            || GuildProfile::accountType($accountId) !== AccountType::PLAYER
        ) {
            status_header(403);
            throw new RuntimeException(
                'Fellowship Seal redemption is available to registered Guild Players.'
            );
        }

        return $accountId;
    }

    /** @return array{success:mixed,error:mixed} */
    private function flashData(): array
    {
        return [
            'success' => $this->flash->get('success'),
            'error' => $this->flash->get('error'),
        ];
    }

    private function redeemUrl(): string
    {
        return add_query_arg(
            'gmrc_route',
            'fellowship-seal',
            home_url('/companion/')
        );
    }

    private function manageUrl(Party $party): string
    {
        return add_query_arg(
            'gmrc_route',
            'parties/' . rawurlencode($party->id()->value()) . '/seal',
            home_url('/companion/')
        );
    }

    private function partyUrl(Party $party): string
    {
        return add_query_arg(
            'gmrc_route',
            'parties/' . rawurlencode($party->id()->value()),
            home_url('/companion/')
        );
    }
}
