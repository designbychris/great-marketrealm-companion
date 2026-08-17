<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Contracts;

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;

defined('ABSPATH') || exit;

/**
 * Persistence contract for Party aggregates.
 *
 * WordPress storage is intentionally deferred to Phase III.11.1B.
 */
interface PartyRepositoryInterface
{
    /**
     * @return Party[]
     */
    public function allForOwner(
        PartyOwnerId $ownerId
    ): array;

    public function findForOwner(
        PartyId $id,
        PartyOwnerId $ownerId
    ): ?Party;

    public function save(
        Party $party
    ): void;

    public function delete(
        PartyId $id,
        PartyOwnerId $ownerId
    ): void;
}
