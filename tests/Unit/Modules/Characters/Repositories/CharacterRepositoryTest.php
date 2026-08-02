<?php

declare(strict_types=1);

/*
 * WordPress test doubles used by CharacterRepository.
 */
namespace {

    if (! class_exists('WP_Post')) {
        class WP_Post
        {
            public int $ID;

            public string $post_type = '';

            public string $post_status = 'publish';

            public string $post_title = '';

            public int $post_author = 0;

            public function __construct(
                object|array $data = []
            ) {
                foreach ((array) $data as $key => $value) {
                    $this->{$key} = $value;
                }
            }
        }
    }
}

/*
 * Namespaced WordPress-function replacements.
 *
 * CharacterRepository is in this namespace, so PHP will
 * resolve these functions before global WordPress functions.
 */
namespace GreatMarketrealmCompanion\Modules\Characters\Repositories {

    use WP_Post;

    final class CharacterRepositoryWordPressState
    {
        /**
         * @var array<int,WP_Post>
         */
        public static array $posts = [];

        /**
         * @var array<int,array<string,mixed>>
         */
        public static array $meta = [];

        public static int $nextPostId = 1;

        public static int $currentUserId = 42;

        public static function reset(): void
        {
            self::$posts = [];
            self::$meta = [];
            self::$nextPostId = 1;
            self::$currentUserId = 42;
        }
    }

    /**
     * @param array<string,mixed> $arguments
     *
     * @return WP_Post[]
     */
    function get_posts(array $arguments = []): array
    {
        $posts = array_values(
            CharacterRepositoryWordPressState::$posts
        );

        return array_values(
            array_filter(
                $posts,
                static function (
                    WP_Post $post
                ) use ($arguments): bool {
                    if (
                        isset($arguments['post_type'])
                        && $post->post_type
                            !== $arguments['post_type']
                    ) {
                        return false;
                    }

                    if (
                        isset($arguments['post_status'])
                        && $post->post_status
                            !== $arguments['post_status']
                    ) {
                        return false;
                    }

                    if (
                        isset($arguments['author'])
                        && $post->post_author
                            !== (int) $arguments['author']
                    ) {
                        return false;
                    }

                    if (
                        isset(
                            $arguments['meta_key'],
                            $arguments['meta_value']
                        )
                    ) {
                        $stored =
                            CharacterRepositoryWordPressState::$meta[
                                $post->ID
                            ][$arguments['meta_key']] ?? null;

                        if (
                            (string) $stored
                            !== (string) $arguments['meta_value']
                        ) {
                            return false;
                        }
                    }

                    return true;
                }
            )
        );
    }

    function get_current_user_id(): int
    {
        return CharacterRepositoryWordPressState::$currentUserId;
    }

    /**
     * @param array<string,mixed> $data
     */
    function wp_insert_post(
        array $data,
        bool $returnError = false
    ): int {
        unset($returnError);

        $postId =
            CharacterRepositoryWordPressState::$nextPostId++;

        CharacterRepositoryWordPressState::$posts[$postId] =
            new WP_Post([
                'ID' => $postId,
                'post_type' => (string) (
                    $data['post_type'] ?? ''
                ),
                'post_status' => (string) (
                    $data['post_status'] ?? 'publish'
                ),
                'post_title' => (string) (
                    $data['post_title'] ?? ''
                ),
                'post_author' => (int) (
                    $data['post_author'] ?? 0
                ),
            ]);

        return $postId;
    }

    /**
     * @param array<string,mixed> $data
     */
    function wp_update_post(
        array $data,
        bool $returnError = false
    ): int {
        unset($returnError);

        $postId = (int) ($data['ID'] ?? 0);

        $post = CharacterRepositoryWordPressState::$posts[
            $postId
        ] ?? null;

        if (! $post instanceof WP_Post) {
            return 0;
        }

        if (isset($data['post_title'])) {
            $post->post_title = (string) $data['post_title'];
        }

        return $postId;
    }

    function wp_delete_post(
        int $postId,
        bool $forceDelete = false
    ): ?WP_Post {
        unset($forceDelete);

        $post = CharacterRepositoryWordPressState::$posts[
            $postId
        ] ?? null;

        if (! $post instanceof WP_Post) {
            return null;
        }

        unset(
            CharacterRepositoryWordPressState::$posts[$postId],
            CharacterRepositoryWordPressState::$meta[$postId]
        );

        return $post;
    }

    function update_post_meta(
        int $postId,
        string $key,
        mixed $value
    ): bool {
        CharacterRepositoryWordPressState::$meta[
            $postId
        ][$key] = $value;

        return true;
    }

    function get_post_meta(
        int $postId,
        string $key,
        bool $single = false
    ): mixed {
        unset($single);

        return CharacterRepositoryWordPressState::$meta[
            $postId
        ][$key] ?? '';
    }

    function is_wp_error(mixed $value): bool
    {
        return false;
    }
}

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Repositories {

    use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
    use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
    use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepositoryWordPressState;
    use PHPUnit\Framework\TestCase;

    final class CharacterRepositoryTest extends TestCase
    {
        protected function setUp(): void
        {
            CharacterRepositoryWordPressState::reset();
        }

        public function testSavesANewCharacter(): void
        {
            $repository = new CharacterRepository();

            $character = $this->character();

            $repository->save($character);

            self::assertCount(
                1,
                CharacterRepositoryWordPressState::$posts
            );

            $post = array_values(
                CharacterRepositoryWordPressState::$posts
            )[0];

            self::assertSame(
                'Sir Allium',
                $post->post_title
            );

            self::assertSame(
                $character->id()->value(),
                CharacterRepositoryWordPressState::$meta[
                    $post->ID
                ]['_gmrc_character_id']
            );
        }

        public function testPersistsCharacterDomainState(): void
        {
            $repository = new CharacterRepository();

            $character = $this->character();

            $repository->save($character);

            $postId = array_key_first(
                CharacterRepositoryWordPressState::$posts
            );

            self::assertIsInt($postId);

            $meta = CharacterRepositoryWordPressState::$meta[
                $postId
            ];

            self::assertSame(
                'fructan',
                $meta['_gmrc_race']
            );

            self::assertSame(
                'fighter',
                $meta['_gmrc_class']
            );

            self::assertSame(
                7,
                $meta['_gmrc_level']
            );

            self::assertSame(
                26000,
                $meta['_gmrc_experience']
            );

            self::assertSame(
                34,
                $meta['_gmrc_hp_current']
            );

            self::assertSame(
                42,
                $meta['_gmrc_hp_maximum']
            );

            self::assertSame(
                5,
                $meta['_gmrc_hp_temporary']
            );

            self::assertSame(
                15,
                $meta['_gmrc_strength']
            );

            self::assertSame(
                14,
                $meta['_gmrc_dexterity']
            );

            self::assertSame(
                13,
                $meta['_gmrc_constitution']
            );

            self::assertSame(
                12,
                $meta['_gmrc_intelligence']
            );

            self::assertSame(
                10,
                $meta['_gmrc_wisdom']
            );

            self::assertSame(
                8,
                $meta['_gmrc_charisma']
            );
        }

        public function testFindsACharacterByItsUlid(): void
        {
            $repository = new CharacterRepository();

            $character = $this->character();

            $repository->save($character);

            $found = $repository->find(
                $character->id()
            );

            self::assertInstanceOf(
                Character::class,
                $found
            );

            self::assertTrue(
                $found->id()->equals(
                    $character->id()
                )
            );

            self::assertTrue(
                $found->name()->equals(
                    $character->name()
                )
            );

            self::assertTrue(
                $found->race()->equals(
                    $character->race()
                )
            );

            self::assertTrue(
                $found->characterClass()->equals(
                    $character->characterClass()
                )
            );

            self::assertTrue(
                $found->level()->equals(
                    $character->level()
                )
            );

            self::assertTrue(
                $found->experience()->equals(
                    $character->experience()
                )
            );

            self::assertTrue(
                $found->hitPoints()->equals(
                    $character->hitPoints()
                )
            );

            self::assertTrue(
                $found->abilityScores()->equals(
                    $character->abilityScores()
                )
            );
        }

        public function testReturnsNullWhenCharacterDoesNotExist(): void
        {
            $repository = new CharacterRepository();

            self::assertNull(
                $repository->find(
                    CharacterId::generate()
                )
            );
        }

        public function testSavingAnExistingCharacterUpdatesItsPost(): void
        {
            $repository = new CharacterRepository();

            $character = $this->character();

            $repository->save($character);

            $character->rename(
                CharacterName::fromString(
                    'Sir Allium the Brave'
                )
            );

            $repository->save($character);

            self::assertCount(
                1,
                CharacterRepositoryWordPressState::$posts
            );

            $post = array_values(
                CharacterRepositoryWordPressState::$posts
            )[0];

            self::assertSame(
                'Sir Allium the Brave',
                $post->post_title
            );
        }

        public function testRetrievesAllCharactersForCurrentUser(): void
        {
            $repository = new CharacterRepository();

            $first = $this->character();

            $second = Character::create(
                CharacterId::generate(),
                CharacterName::fromString('Lady Leek'),
                Race::fromString('vegfolk'),
                CharacterClass::fromString('wizard'),
                HitPoints::full(6),
                AbilityScores::average()
            );

            $repository->save($first);
            $repository->save($second);

            $characters = $repository->all();

            self::assertCount(
                2,
                $characters
            );

            self::assertContainsOnlyInstancesOf(
                Character::class,
                $characters
            );

            self::assertTrue(
                $characters[0]->race()->equals(
                    $first->race()
                )
            );

            self::assertTrue(
                $characters[1]->race()->equals(
                    $second->race()
                )
            );
        }

        public function testDeletesACharacterByItsUlid(): void
        {
            $repository = new CharacterRepository();

            $character = $this->character();

            $repository->save($character);

            $repository->delete(
                $character->id()
            );

            self::assertNull(
                $repository->find(
                    $character->id()
                )
            );

            self::assertSame(
                [],
                CharacterRepositoryWordPressState::$posts
            );
        }

        private function character(): Character
        {
            return Character::reconstitute(
                CharacterId::generate(),
                CharacterName::fromString('Sir Allium'),
                Race::fromString('fructan'),
                CharacterClass::fromString('fighter'),
                Level::fromInt(7),
                Experience::fromInt(26000),
                HitPoints::fromValues(
                    current: 34,
                    maximum: 42,
                    temporary: 5
                ),
                AbilityScores::fromScores(
                    strength: AbilityScore::fromInt(15),
                    dexterity: AbilityScore::fromInt(14),
                    constitution: AbilityScore::fromInt(13),
                    intelligence: AbilityScore::fromInt(12),
                    wisdom: AbilityScore::fromInt(10),
                    charisma: AbilityScore::fromInt(8),
                )
            );
        }

        public function testMigratesALegacyCharacterWithoutADomainIdentifier(): void
        {
            $repository = new CharacterRepository();
        
            $postId =
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\wp_insert_post(
                [
                    'post_type' => 'gmrc_character',
                    'post_status' => 'publish',
                    'post_title' => 'Legacy Adventurer',
                    'post_author' => 42,
                ]
            );
        
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\update_post_meta(
                $postId,
                '_gmrc_race',
                'fructan'
            );
        
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\update_post_meta(
                $postId,
                '_gmrc_class',
                'fighter'
            );
        
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\update_post_meta(
                $postId,
                '_gmrc_level',
                1
            );
        
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\update_post_meta(
                $postId,
                '_gmrc_experience',
                0
            );
        
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\update_post_meta(
                $postId,
                '_gmrc_hp_current',
                10
            );
        
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\update_post_meta(
                $postId,
                '_gmrc_hp_maximum',
                10
            );
        
            \GreatMarketrealmCompanion\Modules\Characters\Repositories\update_post_meta(
                $postId,
                '_gmrc_hp_temporary',
                0
            );
        
            foreach (
                [
                    'strength',
                    'dexterity',
                    'constitution',
                    'intelligence',
                    'wisdom',
                    'charisma',
                ] as $ability
            ) {
                update_post_meta(
                    $postId,
                    '_gmrc_' . $ability,
                    10
                );
            }
        
            $characters = $repository->all();
        
            self::assertCount(
                1,
                $characters
            );
        
            $storedId =
                CharacterRepositoryWordPressState::$meta[
                    $postId
                ]['_gmrc_character_id'] ?? '';
        
            self::assertNotSame(
                '',
                $storedId
            );
        
            self::assertSame(
                $storedId,
                $characters[0]->id()->value()
            );
        }
    }
}
