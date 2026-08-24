<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Repositories;

use GreatMarketrealmCompanion\Core\Invitations\InviteCodeGenerator;
use GreatMarketrealmCompanion\Modules\Parties\Models\FellowshipSeal;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Persists one current Fellowship Seal against each Fellowship record.
 */
final class FellowshipSealRepository
{
    private const META_SEAL = '_gmrc_fellowship_seal';
    private const META_LOOKUP = '_gmrc_fellowship_seal_lookup';
    private const GENERATION_ATTEMPTS = 12;

    public function __construct(
        private PartyRepository $parties,
        private InviteCodeGenerator $codes
    ) {
    }

    public function current(Party $party): ?FellowshipSeal
    {
        $stored = get_post_meta(
            $this->postId($party),
            self::META_SEAL,
            true
        );

        if (! is_array($stored)) {
            return null;
        }

        $code = (string) ($stored['code'] ?? '');
        $issuedAt = (int) ($stored['issued_at'] ?? 0);
        $expiresAt = (int) ($stored['expires_at'] ?? 0);

        if (
            strlen(FellowshipSeal::normalise($code)) !== 8
            || $issuedAt < 1
            || $expiresAt <= $issuedAt
        ) {
            return null;
        }

        return FellowshipSeal::restore(
            $code,
            $issuedAt,
            $expiresAt,
            (string) ($stored['status'] ?? FellowshipSeal::STATUS_ACTIVE)
        );
    }

    public function issue(Party $party): FellowshipSeal
    {
        for ($attempt = 0; $attempt < self::GENERATION_ATTEMPTS; $attempt++) {
            $seal = FellowshipSeal::issue($this->codes->generate());

            if (! $this->lookupExists($seal->lookupCode())) {
                $this->write($party, $seal);
                return $seal;
            }
        }

        throw new RuntimeException(
            'A unique Fellowship Seal could not be issued. Please try again.'
        );
    }

    public function revoke(Party $party): void
    {
        $seal = $this->current($party);

        if (! $seal instanceof FellowshipSeal) {
            return;
        }

        $seal->revoke();
        $this->write($party, $seal);
    }

    public function fellowshipForCode(string $submittedCode): ?Party
    {
        $lookup = FellowshipSeal::normalise($submittedCode);

        if (strlen($lookup) !== 8) {
            return null;
        }

        $posts = get_posts([
            'post_type' => 'gmrc_party',
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'meta_key' => self::META_LOOKUP,
            'meta_value' => $lookup,
        ]);

        if (count($posts) !== 1 || ! $posts[0] instanceof WP_Post) {
            return null;
        }

        $partyId = (string) get_post_meta(
            (int) $posts[0]->ID,
            '_gmrc_party_id',
            true
        );

        if ($partyId === '') {
            return null;
        }

        try {
            $party = $this->parties->findAcrossOwners(
                PartyId::fromString($partyId)
            );
        } catch (\Throwable) {
            return null;
        }

        if (! $party instanceof Party) {
            return null;
        }

        $seal = $this->current($party);

        return $seal instanceof FellowshipSeal
            && hash_equals($seal->lookupCode(), $lookup)
            && $seal->isRedeemable()
                ? $party
                : null;
    }

    private function write(Party $party, FellowshipSeal $seal): void
    {
        $postId = $this->postId($party);

        update_post_meta($postId, self::META_SEAL, [
            'code' => $seal->code(),
            'issued_at' => $seal->issuedAt(),
            'expires_at' => $seal->expiresAt(),
            'status' => $seal->status(),
        ]);

        update_post_meta(
            $postId,
            self::META_LOOKUP,
            $seal->lookupCode()
        );
    }

    private function lookupExists(string $lookup): bool
    {
        return get_posts([
            'post_type' => 'gmrc_party',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'meta_key' => self::META_LOOKUP,
            'meta_value' => $lookup,
        ]) !== [];
    }

    private function postId(Party $party): int
    {
        $posts = get_posts([
            'post_type' => 'gmrc_party',
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'author' => $party->ownerId()->value(),
            'meta_key' => '_gmrc_party_id',
            'meta_value' => $party->id()->value(),
        ]);

        if (count($posts) !== 1 || ! $posts[0] instanceof WP_Post) {
            throw new RuntimeException(
                'The Fellowship Register record could not be found.'
            );
        }

        return (int) $posts[0]->ID;
    }
}
