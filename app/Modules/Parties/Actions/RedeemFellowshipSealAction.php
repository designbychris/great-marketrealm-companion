<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Actions;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\FellowshipSealRepository;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Redeems a Fellowship Seal without weakening Character ownership boundaries.
 */
final class RedeemFellowshipSealAction
{
    public function __construct(
        private FellowshipSealRepository $seals,
        private PartyRepository $parties,
        private CharacterRepository $characters
    ) {
    }

    public function handle(
        string $code,
        CharacterId $characterId,
        int $accountId
    ): Party {
        $party = $this->seals->fellowshipForCode($code);

        if (! $party instanceof Party) {
            throw new RuntimeException(
                'That Fellowship Seal is invalid, expired, or revoked.'
            );
        }

        if ($party->ownerId()->value() === $accountId) {
            throw new RuntimeException(
                'A Fellowship custodian cannot redeem their own Seal.'
            );
        }

        $character = $this->characters->findForOwner(
            $characterId,
            $accountId
        );

        if (! $character instanceof Character) {
            throw new RuntimeException(
                'That adventurer is not available to this Guild account.'
            );
        }

        if ($party->hasMember($characterId)) {
            return $party;
        }

        $party->addMember(
            $characterId,
            PartyMembershipRole::member()
        );
        $this->parties->save($party);

        return $party;
    }
}
