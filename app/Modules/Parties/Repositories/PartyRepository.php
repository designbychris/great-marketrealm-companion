<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyMembership;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use RuntimeException;
use Throwable;
use WP_Post;

defined('ABSPATH') || exit;

final class PartyRepository implements PartyRepositoryInterface
{
    private const POST_TYPE = 'gmrc_party';
    private const META_PARTY_ID = '_gmrc_party_id';
    private const META_MEMBERSHIPS = '_gmrc_party_memberships';

    public function allForOwner(PartyOwnerId $ownerId): array
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $ownerId->value(),
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        return array_values(array_filter(array_map(
            fn (WP_Post $post): ?Party => $this->mapPost($post),
            $posts
        )));
    }

    public function findForOwner(
        PartyId $id,
        PartyOwnerId $ownerId
    ): ?Party {
        $post = $this->findPost($id, $ownerId);

        return $post instanceof WP_Post
            ? $this->mapPost($post)
            : null;
    }

    public function save(Party $party): void
    {
        $post = $this->findPost(
            $party->id(),
            $party->ownerId()
        );

        $postId = $post instanceof WP_Post
            ? $this->updatePost($post, $party)
            : $this->insertPost($party);

        update_post_meta(
            $postId,
            self::META_PARTY_ID,
            $party->id()->value()
        );

        update_post_meta(
            $postId,
            self::META_MEMBERSHIPS,
            array_map(
                static fn (PartyMembership $membership): array => [
                    'character_id' =>
                        $membership->characterId()->value(),
                    'role' => $membership->role()->value(),
                ],
                $party->memberships()
            )
        );
    }

    public function delete(
        PartyId $id,
        PartyOwnerId $ownerId
    ): void {
        $post = $this->findPost($id, $ownerId);

        if (! $post instanceof WP_Post) {
            return;
        }

        $deleted = wp_delete_post($post->ID, true);

        if (! $deleted instanceof WP_Post) {
            throw new RuntimeException(
                'The Fellowship record could not be deleted.'
            );
        }
    }

    private function findPost(
        PartyId $id,
        PartyOwnerId $ownerId
    ): ?WP_Post {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'author' => $ownerId->value(),
            'meta_key' => self::META_PARTY_ID,
            'meta_value' => $id->value(),
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);

        if (count($posts) > 1) {
            throw new RuntimeException(
                sprintf(
                    'The Fellowship Register contains duplicate records for Party %s.',
                    $id->value()
                )
            );
        }

        $post = $posts[0] ?? null;

        return $post instanceof WP_Post ? $post : null;
    }

    private function insertPost(Party $party): int
    {
        $postId = wp_insert_post([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $party->name()->value(),
            'post_author' => $party->ownerId()->value(),
        ], true);

        if (is_wp_error($postId)) {
            throw new RuntimeException(
                $postId->get_error_message()
            );
        }

        return (int) $postId;
    }

    private function updatePost(
        WP_Post $post,
        Party $party
    ): int {
        $postId = wp_update_post([
            'ID' => $post->ID,
            'post_title' => $party->name()->value(),
        ], true);

        if (is_wp_error($postId) || (int) $postId < 1) {
            throw new RuntimeException(
                'The Fellowship record could not be updated.'
            );
        }

        return (int) $postId;
    }

    private function mapPost(WP_Post $post): ?Party
    {
        try {
            $id = PartyId::fromString((string) get_post_meta(
                $post->ID,
                self::META_PARTY_ID,
                true
            ));

            $name = PartyName::fromString(
                trim((string) $post->post_title)
            );

            $ownerId = PartyOwnerId::fromInt(
                (int) $post->post_author
            );

            return Party::reconstitute(
                $id,
                $name,
                $ownerId,
                $this->memberships($post->ID)
            );
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return PartyMembership[]
     */
    private function memberships(int $postId): array
    {
        $stored = get_post_meta(
            $postId,
            self::META_MEMBERSHIPS,
            true
        );

        if (is_string($stored) && $stored !== '') {
            $decoded = json_decode($stored, true);
            $stored = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($stored)) {
            return [];
        }

        $memberships = [];
        $seen = [];

        foreach ($stored as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            try {
                $characterId = CharacterId::fromString(
                    (string) ($entry['character_id'] ?? '')
                );

                if (isset($seen[$characterId->value()])) {
                    continue;
                }

                $role = PartyMembershipRole::fromString(
                    (string) ($entry['role'] ?? '')
                );

                $seen[$characterId->value()] = true;
                $memberships[] = PartyMembership::withRole(
                    $characterId,
                    $role
                );
            } catch (Throwable) {
                continue;
            }
        }

        return $memberships;
    }
}
