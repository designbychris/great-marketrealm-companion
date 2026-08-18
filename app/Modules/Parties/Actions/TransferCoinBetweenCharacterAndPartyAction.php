<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Actions;

use GreatMarketrealmCompanion\Core\Actions\Action;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterPurse;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyTreasury;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCoinTransferDirection;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use RuntimeException;
use Throwable;

defined('ABSPATH') || exit;

/**
 * Moves one exact amount of coin between a Character purse and Fellowship
 * Treasury while preserving both aggregate boundaries.
 */
final class TransferCoinBetweenCharacterAndPartyAction extends Action
{
    public function __construct(
        private CharacterRepositoryInterface $characters,
        private PartyRepositoryInterface $parties,
        private PartyFinder $finder
    ) {
    }

    public function handle(
        PartyId $partyId,
        PartyOwnerId $ownerId,
        CharacterId $characterId,
        PartyCoinTransferDirection $direction,
        PartyTreasuryMoney $amount,
        string $transferId,
        string $note = ''
    ): Party {
        $party = $this->finder->find(
            $partyId,
            $ownerId
        );

        $character = $this->characters->find(
            $characterId
        );

        if (! $character instanceof Character) {
            throw new RuntimeException(
                'The adventurer could not be found for this Guild account.'
            );
        }

        if (! $party->hasMember($characterId)) {
            throw new RuntimeException(
                'Only a member of this Fellowship may transfer coin with its Treasury.'
            );
        }

        $transferId = trim($transferId);

        if (
            $transferId === ''
            || mb_strlen($transferId) > 64
        ) {
            throw new RuntimeException(
                'The Fellowship transfer identifier is invalid.'
            );
        }

        if (
            $party
                ->treasury()
                ->hasTransferId($transferId)
        ) {
            return $party;
        }

        if ($amount->isZero()) {
            throw new RuntimeException(
                'A Fellowship transfer must contain at least one copper piece.'
            );
        }

        $originalPurse = $character->purse();
        $originalTreasury = PartyTreasury::reconstitute(
            PartyTreasuryMoney::fromCopper(
                $party->treasury()->balance()->copper()
            ),
            $party->treasury()->transactions()
        );

        $transferNote = $this->transferNote(
            $character,
            $direction,
            $note
        );

        if ($direction->isToTreasury()) {
            $character->withdrawFromPurse(
                CharacterPurse::fromCopper(
                    $amount->copper()
                )
            );

            $party->transferIntoTreasury(
                $amount,
                $transferNote,
                $characterId,
                $transferId
            );
        } else {
            $party->transferOutOfTreasury(
                $amount,
                $transferNote,
                $characterId,
                $transferId
            );

            $character->depositToPurse(
                CharacterPurse::fromCopper(
                    $amount->copper()
                )
            );
        }

        try {
            $this->characters->save($character);
            $this->parties->save($party);
        } catch (Throwable $failure) {
            $this->compensate(
                $character,
                $party,
                $originalPurse,
                $originalTreasury,
                $failure
            );
        }

        return $party;
    }

    private function transferNote(
        Character $character,
        PartyCoinTransferDirection $direction,
        string $note
    ): string {
        $prefix = $direction->isToTreasury()
            ? 'From ' . $character->name()->value()
            : 'To ' . $character->name()->value();

        $note = trim($note);

        if ($note === '') {
            return $prefix;
        }

        $available = max(
            0,
            160 - mb_strlen($prefix) - 3
        );

        return $prefix
            . ' — '
            . mb_substr(
                $note,
                0,
                $available
            );
    }

    private function compensate(
        Character $character,
        Party $party,
        CharacterPurse $originalPurse,
        PartyTreasury $originalTreasury,
        Throwable $failure
    ): never {
        $character->replacePurse(
            $originalPurse
        );

        $party->replaceTreasury(
            $originalTreasury
        );

        $rollbackFailed = false;

        try {
            $this->characters->save($character);
        } catch (Throwable) {
            $rollbackFailed = true;
        }

        try {
            $this->parties->save($party);
        } catch (Throwable) {
            $rollbackFailed = true;
        }

        if ($rollbackFailed) {
            throw new RuntimeException(
                'The coin transfer failed and the Guild could not completely restore the previous balances. The permanent records require review.',
                0,
                $failure
            );
        }

        throw new RuntimeException(
            'The coin transfer could not be completed. Both balances were restored.',
            0,
            $failure
        );
    }
}
