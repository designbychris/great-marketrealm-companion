<?php

declare(strict_types=1);

namespace {
    if (! class_exists('WP_Post')) {
        class WP_Post
        {
            public int $ID;
            public string $post_type = '';
            public string $post_status = 'publish';
            public string $post_title = '';
            public int $post_author = 0;

            public function __construct(object|array $data = [])
            {
                foreach ((array) $data as $key => $value) {
                    $this->{$key} = $value;
                }
            }
        }
    }
}

namespace GreatMarketrealmCompanion\Modules\Parties\Repositories {
    use WP_Post;

    final class PartyRepositoryWordPressState
    {
        public static array $posts = [];
        public static array $meta = [];
        public static int $nextPostId = 1;

        public static function reset(): void
        {
            self::$posts = [];
            self::$meta = [];
            self::$nextPostId = 1;
        }
    }

    function get_posts(array $args = []): array
    {
        return array_values(array_filter(
            array_values(PartyRepositoryWordPressState::$posts),
            static function (WP_Post $post) use ($args): bool {
                if (isset($args['post_type']) && $post->post_type !== $args['post_type']) return false;
                if (isset($args['post_status']) && $post->post_status !== $args['post_status']) return false;
                if (isset($args['author']) && $post->post_author !== (int) $args['author']) return false;
                if (isset($args['meta_key'], $args['meta_value'])) {
                    return (string) (PartyRepositoryWordPressState::$meta[$post->ID][$args['meta_key']] ?? '')
                        === (string) $args['meta_value'];
                }
                return true;
            }
        ));
    }

    function wp_insert_post(array $data, bool $returnError = false): int
    {
        unset($returnError);
        $id = PartyRepositoryWordPressState::$nextPostId++;
        PartyRepositoryWordPressState::$posts[$id] = new WP_Post([
            'ID' => $id,
            'post_type' => $data['post_type'] ?? '',
            'post_status' => $data['post_status'] ?? 'publish',
            'post_title' => $data['post_title'] ?? '',
            'post_author' => (int) ($data['post_author'] ?? 0),
        ]);
        return $id;
    }

    function wp_update_post(array $data, bool $returnError = false): int
    {
        unset($returnError);
        $id = (int) ($data['ID'] ?? 0);
        if (!isset(PartyRepositoryWordPressState::$posts[$id])) return 0;
        PartyRepositoryWordPressState::$posts[$id]->post_title = (string) ($data['post_title'] ?? '');
        return $id;
    }

    function wp_delete_post(int $id, bool $force = false): ?WP_Post
    {
        unset($force);
        $post = PartyRepositoryWordPressState::$posts[$id] ?? null;
        if (!$post instanceof WP_Post) return null;
        unset(PartyRepositoryWordPressState::$posts[$id], PartyRepositoryWordPressState::$meta[$id]);
        return $post;
    }

    function update_post_meta(int $id, string $key, mixed $value): bool
    {
        PartyRepositoryWordPressState::$meta[$id][$key] = $value;
        return true;
    }

    function get_post_meta(int $id, string $key, bool $single = false): mixed
    {
        unset($single);
        return PartyRepositoryWordPressState::$meta[$id][$key] ?? '';
    }

    function is_wp_error(mixed $value): bool { return false; }
}

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Repositories {
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
    use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
    use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
    use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
    use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
    use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
    use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
    use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepositoryWordPressState;
    use PHPUnit\Framework\TestCase;
    use RuntimeException;

    final class PartyRepositoryTest extends TestCase
    {
        protected function setUp(): void
        {
            PartyRepositoryWordPressState::reset();
        }

        public function testSavesAndReloadsPartyMemberships(): void
        {
            $repo = new PartyRepository();
            $party = $this->party(42);
            $first = CharacterId::generate();
            $second = CharacterId::generate();

            $party->addMember($first, PartyMembershipRole::leader());
            $party->addMember($second);

            $repo->save($party);
            $found = $repo->findForOwner($party->id(), PartyOwnerId::fromInt(42));

            self::assertInstanceOf(Party::class, $found);
            self::assertSame(2, $found->memberCount());
            self::assertTrue($found->hasMember($first));
            self::assertTrue($found->membership($first)?->role()->isLeader());
        }

        public function testReadsAreStrictlyOwnerScoped(): void
        {
            $repo = new PartyRepository();
            $party = $this->party(42);
            $repo->save($party);

            self::assertNull(
                $repo->findForOwner(
                    $party->id(),
                    PartyOwnerId::fromInt(99)
                )
            );

            self::assertCount(
                0,
                $repo->allForOwner(PartyOwnerId::fromInt(99))
            );
        }

        public function testSaveUpdatesExistingPartyInsteadOfDuplicatingIt(): void
        {
            $repo = new PartyRepository();
            $party = $this->party(42);
            $repo->save($party);

            $party->rename(PartyName::fromString('The Heroic Trolley'));
            $repo->save($party);

            self::assertCount(1, PartyRepositoryWordPressState::$posts);
            self::assertSame(
                'The Heroic Trolley',
                array_values(PartyRepositoryWordPressState::$posts)[0]->post_title
            );
        }

        public function testDeleteIsOwnerScoped(): void
        {
            $repo = new PartyRepository();
            $party = $this->party(42);
            $repo->save($party);

            $repo->delete($party->id(), PartyOwnerId::fromInt(99));
            self::assertCount(1, PartyRepositoryWordPressState::$posts);

            $repo->delete($party->id(), PartyOwnerId::fromInt(42));
            self::assertCount(0, PartyRepositoryWordPressState::$posts);
        }

        public function testMalformedMembershipRowsAreSkippedOnHydration(): void
        {
            $repo = new PartyRepository();
            $party = $this->party(42);
            $valid = CharacterId::generate();
            $party->addMember($valid);
            $repo->save($party);

            $postId = array_key_first(PartyRepositoryWordPressState::$posts);
            PartyRepositoryWordPressState::$meta[$postId]['_gmrc_party_memberships'][] = [
                'character_id' => 'not-a-ulid',
                'role' => 'member',
            ];
            PartyRepositoryWordPressState::$meta[$postId]['_gmrc_party_memberships'][] = [
                'character_id' => CharacterId::generate()->value(),
                'role' => 'dragon-emperor',
            ];

            $found = $repo->findForOwner($party->id(), PartyOwnerId::fromInt(42));

            self::assertInstanceOf(Party::class, $found);
            self::assertSame(1, $found->memberCount());
            self::assertTrue($found->hasMember($valid));
        }

        public function testDuplicateStoredMembershipRowsAreCollapsedSafely(): void
        {
            $repo = new PartyRepository();
            $party = $this->party(42);
            $member = CharacterId::generate();
            $party->addMember($member);
            $repo->save($party);

            $postId = array_key_first(PartyRepositoryWordPressState::$posts);
            $row = PartyRepositoryWordPressState::$meta[$postId]['_gmrc_party_memberships'][0];
            PartyRepositoryWordPressState::$meta[$postId]['_gmrc_party_memberships'][] = $row;

            $found = $repo->findForOwner($party->id(), PartyOwnerId::fromInt(42));
            self::assertSame(1, $found?->memberCount());
        }

        public function testDuplicatePartyRecordsFailClosed(): void
        {
            $repo = new PartyRepository();
            $party = $this->party(42);
            $repo->save($party);

            $firstId = array_key_first(PartyRepositoryWordPressState::$posts);
            $duplicateId = 999;
            PartyRepositoryWordPressState::$posts[$duplicateId] = new \WP_Post([
                'ID' => $duplicateId,
                'post_type' => 'gmrc_party',
                'post_status' => 'publish',
                'post_title' => 'Duplicate',
                'post_author' => 42,
            ]);
            PartyRepositoryWordPressState::$meta[$duplicateId] =
                PartyRepositoryWordPressState::$meta[$firstId];

            $this->expectException(RuntimeException::class);
            $repo->findForOwner($party->id(), PartyOwnerId::fromInt(42));
        }

        private function party(int $owner): Party
        {
            return Party::create(
                PartyId::generate(),
                PartyName::fromString('The Pantry Fellowship'),
                PartyOwnerId::fromInt($owner)
            );
        }
    }
}
