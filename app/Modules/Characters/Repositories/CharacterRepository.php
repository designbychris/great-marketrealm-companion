<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Languages;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Spellbook;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterPurse;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Character Repository.
 *
 * Handles WordPress persistence for Character entities.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterRepository implements CharacterRepositoryInterface
{
    private const META_CHARACTER_ID = '_gmrc_character_id';
    private const META_RACE = '_gmrc_race';
    private const META_CLASS = '_gmrc_class';
    private const META_LEVEL = '_gmrc_level';
    private const META_EXPERIENCE = '_gmrc_experience';
    private const META_HP_CURRENT = '_gmrc_hp_current';
    private const META_HP_MAXIMUM = '_gmrc_hp_maximum';
    private const META_HP_TEMPORARY = '_gmrc_hp_temporary';
    private const META_STRENGTH = '_gmrc_strength';
    private const META_DEXTERITY = '_gmrc_dexterity';
    private const META_CONSTITUTION = '_gmrc_constitution';
    private const META_INTELLIGENCE = '_gmrc_intelligence';
    private const META_WISDOM = '_gmrc_wisdom';
    private const META_CHARISMA = '_gmrc_charisma';
    private const META_BACKGROUND = '_gmrc_background';
    private const META_BACKGROUND_SKILLS = '_gmrc_background_skills';
    private const META_BACKGROUND_TOOLS = '_gmrc_background_tools';
    private const META_BACKGROUND_LABEL = '_gmrc_background_label';
    private const META_RACIAL_SKILLS = '_gmrc_racial_skills';
    private const META_SELECTED_LANGUAGES = '_gmrc_selected_languages';
    private const META_SELECTED_TOOLS = '_gmrc_selected_tools';
    private const META_SPELLBOOK = '_gmrc_spellbook';
    private const META_CALLING_PATH = '_gmrc_subclass';
    private const META_PATH_GIFTS = '_gmrc_path_gifts';
    private const META_PURSE = '_gmrc_character_purse_copper';

    private string $postType = 'gmrc_character';

    /**
     * Retrieve all Characters belonging to the current user.
     *
     * @return Character[]
     */
    public function all(): array
    {
        return $this->allForOwner(
            get_current_user_id()
        );
    }

    /**
     * Retrieve all Characters belonging to a specific Guild account.
     *
     * @return Character[]
     */
    public function allForOwner(int $ownerId): array
    {
        $posts = get_posts([
            'post_type' => $this->postType,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $ownerId,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        return array_map(
            fn (WP_Post $post): Character =>
                $this->mapPost($post),
            $posts
        );
    }

    /**
     * Find a Character by its domain identifier.
     */
    public function find(
        CharacterId $id
    ): ?Character {
        return $this->findForOwner(
            $id,
            get_current_user_id()
        );
    }

    /**
     * Find a Character belonging to a specific Guild account.
     */
    public function findForOwner(
        CharacterId $id,
        int $ownerId
    ): ?Character {
        $post = $this->findPostByCharacterIdForOwner(
            $id,
            $ownerId
        );

        return $post instanceof WP_Post
            ? $this->mapPost($post)
            : null;
    }

    /**
     * Resolve a Character by stable identifier regardless of account owner.
     *
     * This is reserved for trusted cross-account presentation surfaces such
     * as a Campaign-founded Fellowship whose membership was explicitly
     * assembled from Campaign roster nominations.
     */
    public function findAcrossOwners(CharacterId $id): ?Character
    {
        $posts = get_posts([
            'post_type' => $this->postType,
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'meta_key' => self::META_CHARACTER_ID,
            'meta_value' => $id->value(),
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);

        if (count($posts) > 1) {
            throw new RuntimeException(
                sprintf(
                    'The Guild Register contains duplicate records for Character %s.',
                    $id->value()
                )
            );
        }

        $post = $posts[0] ?? null;

        return $post instanceof WP_Post
            ? $this->mapPost($post)
            : null;
    }

    /**
     * Persist a Character.
     */
    public function save(
        Character $character
    ): void {
        $existingPost = $this->findPostByCharacterId(
            $character->id()
        );

        $postId = $existingPost instanceof WP_Post
            ? $this->updatePost(
                $existingPost,
                $character
            )
            : $this->insertPost($character);

        $this->saveMeta(
            $postId,
            $character
        );

        $this->clearPersistenceCache(
            $postId
        );

        $this->assertPersistedState(
            $postId,
            $character
        );
    }

    /**
     * Delete a Character.
     */
    public function delete(
        CharacterId $id
    ): void {
        $post = $this->findPostByCharacterId($id);

        if (! $post instanceof WP_Post) {
            return;
        }

        $deleted = wp_delete_post(
            $post->ID,
            true
        );

        if (! $deleted instanceof WP_Post) {
            throw new RuntimeException(
                'The character could not be deleted.'
            );
        }
    }

    /**
     * Find the WordPress post storing a Character.
     */
    private function findPostByCharacterId(
        CharacterId $id
    ): ?WP_Post {
        return $this->findPostByCharacterIdForOwner(
            $id,
            get_current_user_id()
        );
    }

    /**
     * Find a Character post owned by a specific Guild account.
     */
    private function findPostByCharacterIdForOwner(
        CharacterId $id,
        int $ownerId
    ): ?WP_Post {
        $posts = get_posts([
            'post_type' => $this->postType,
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'author' => $ownerId,
            'meta_key' => self::META_CHARACTER_ID,
            'meta_value' => $id->value(),
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);

        if (count($posts) > 1) {
            throw new RuntimeException(
                sprintf(
                    'The Guild Register contains duplicate records for Character %s. Advancement has been stopped to protect the permanent record.',
                    $id->value()
                )
            );
        }

        $post = $posts[0] ?? null;

        return $post instanceof WP_Post
            ? $post
            : null;
    }

    /**
     * Insert a new Character post.
     */
    private function insertPost(
        Character $character
    ): int {
        $postId = wp_insert_post(
            [
                'post_type' => $this->postType,
                'post_status' => 'publish',
                'post_title' => $character
                    ->name()
                    ->value(),
                'post_author' => get_current_user_id(),
            ],
            true
        );

        if (is_wp_error($postId)) {
            throw new RuntimeException(
                $postId->get_error_message()
            );
        }

        return (int) $postId;
    }

    /**
     * Update an existing Character post.
     */
    private function updatePost(
        WP_Post $post,
        Character $character
    ): int {
        $postId = wp_update_post(
            [
                'ID' => $post->ID,
                'post_title' => $character
                    ->name()
                    ->value(),
            ],
            true
        );

        if (is_wp_error($postId)) {
            throw new RuntimeException(
                $postId->get_error_message()
            );
        }

        return (int) $postId;
    }

    /**
     * Save Character metadata.
     */
    private function saveMeta(
        int $postId,
        Character $character
    ): void {
        $hitPoints = $character->hitPoints();
        $abilityScores = $character->abilityScores();

        update_post_meta(
            $postId,
            self::META_CHARACTER_ID,
            $character->id()->value()
        );

        update_post_meta(
            $postId,
            self::META_RACE,
            $character->race()->value()
        );

        update_post_meta(
            $postId,
            self::META_CLASS,
            $character
                ->characterClass()
                ->value()
        );
        
        update_post_meta(
            $postId,
            self::META_BACKGROUND,
            $character
                ->background()
                ->value()
        );

        $backgroundMechanics = $character->background()->mechanicsSnapshot();
        update_post_meta($postId, self::META_BACKGROUND_SKILLS, $backgroundMechanics['skills']);
        update_post_meta($postId, self::META_BACKGROUND_TOOLS, $backgroundMechanics['tools']);
        update_post_meta($postId, self::META_BACKGROUND_LABEL, $character->background()->label());
        update_post_meta(
            $postId,
            self::META_RACIAL_SKILLS,
            $character->racialSkillProficiencies()->proficiencies()
        );

        update_post_meta(
            $postId,
            self::META_SELECTED_LANGUAGES,
            $character
                ->selectedLanguages()
                ->values()
        );

        update_post_meta(
            $postId,
            self::META_SELECTED_TOOLS,
            $character
                ->selectedToolProficiencies()
                ->values()
        );
        
        update_post_meta(
            $postId,
            self::META_SPELLBOOK,
            $character->spellbook()->toArray()
        );

        update_post_meta(
            $postId,
            self::META_CALLING_PATH,
            $character->callingPath()->value()
        );

        update_post_meta(
            $postId,
            self::META_PATH_GIFTS,
            $character->pathGifts()->values()
        );

        update_post_meta(
            $postId,
            self::META_LEVEL,
            $character->level()->value()
        );

        update_post_meta(
            $postId,
            self::META_EXPERIENCE,
            $character->experience()->value()
        );

        update_post_meta(
            $postId,
            self::META_PURSE,
            $character->purse()->copper()
        );

        update_post_meta(
            $postId,
            self::META_HP_CURRENT,
            $hitPoints->current()
        );

        update_post_meta(
            $postId,
            self::META_HP_MAXIMUM,
            $hitPoints->maximum()
        );

        update_post_meta(
            $postId,
            self::META_HP_TEMPORARY,
            $hitPoints->temporary()
        );

        foreach ($abilityScores->all() as $key => $score) {
            update_post_meta(
                $postId,
                '_gmrc_' . $key,
                $score->value()
            );
        }
    }

    /**
     * Remove WordPress object-cache entries that can otherwise leave the
     * next request hydrating pre-certification Character metadata.
     */
    private function clearPersistenceCache(
        int $postId
    ): void {
        if (function_exists('clean_post_cache')) {
            \clean_post_cache($postId);
        }

        if (function_exists('wp_cache_delete')) {
            \wp_cache_delete(
                $postId,
                'post_meta'
            );
        }
    }

    /**
     * Certification must never be reported as successful unless the domain
     * level can be read back from the permanent WordPress record.
     */
    private function assertPersistedState(
        int $postId,
        Character $character
    ): void {
        $storedCharacterId = (string) get_post_meta(
            $postId,
            self::META_CHARACTER_ID,
            true
        );

        $storedLevel = (int) get_post_meta(
            $postId,
            self::META_LEVEL,
            true
        );

        $storedPathGifts = get_post_meta(
            $postId,
            self::META_PATH_GIFTS,
            true
        );

        $persistedPathGifts = PathGifts::fromArray(
            is_array($storedPathGifts)
                ? $storedPathGifts
                : []
        );

        if (
            $storedCharacterId !== $character->id()->value()
            || $storedLevel !== $character->level()->value()
            || $persistedPathGifts->values()
                !== $character->pathGifts()->values()
        ) {
            throw new RuntimeException(
                sprintf(
                    'The Guild could not verify Character persistence after saving. Expected Level %d but the permanent Register returned Level %d.',
                    $character->level()->value(),
                    $storedLevel
                )
            );
        }
    }

    /**
     * Convert a WordPress post into a Character entity.
     */
    private function mapPost(
        WP_Post $post
    ): Character {
        $characterId = (string) get_post_meta(
            $post->ID,
            self::META_CHARACTER_ID,
            true
        );
    
        if ($characterId === '') {
            $characterId =
                CharacterId::generate()->value();
    
            update_post_meta(
                $post->ID,
                self::META_CHARACTER_ID,
                $characterId
            );
        }
    
        return Character::reconstitute(
            id: CharacterId::fromString(
                $characterId
            ),
            name: CharacterName::fromString(
                $post->post_title
            ),
            race: $this->mapRace(
                $post->ID
            ),
            characterClass:
                $this->mapCharacterClass(
                    $post->ID
                ),
            level: Level::fromInt(
                max(
                    1,
                    (int) get_post_meta(
                        $post->ID,
                        self::META_LEVEL,
                        true
                    )
                )
            ),
            experience: Experience::fromInt(
                max(
                    0,
                    (int) get_post_meta(
                        $post->ID,
                        self::META_EXPERIENCE,
                        true
                    )
                )
            ),
            hitPoints: HitPoints::fromValues(
                current: max(
                    0,
                    (int) get_post_meta(
                        $post->ID,
                        self::META_HP_CURRENT,
                        true
                    )
                ),
                maximum: max(
                    1,
                    (int) get_post_meta(
                        $post->ID,
                        self::META_HP_MAXIMUM,
                        true
                    )
                ),
                temporary: max(
                    0,
                    (int) get_post_meta(
                        $post->ID,
                        self::META_HP_TEMPORARY,
                        true
                    )
                )
            ),
            abilityScores: AbilityScores::fromScores(
                strength: $this->mapAbilityScore(
                    $post->ID,
                    self::META_STRENGTH
                ),
                dexterity: $this->mapAbilityScore(
                    $post->ID,
                    self::META_DEXTERITY
                ),
                constitution: $this->mapAbilityScore(
                    $post->ID,
                    self::META_CONSTITUTION
                ),
                intelligence: $this->mapAbilityScore(
                    $post->ID,
                    self::META_INTELLIGENCE
                ),
                wisdom: $this->mapAbilityScore(
                    $post->ID,
                    self::META_WISDOM
                ),
                charisma: $this->mapAbilityScore(
                    $post->ID,
                    self::META_CHARISMA
                ),
            ),
            background: $this->mapBackground(
                $post->ID
            ),
            selectedLanguages:
                $this->mapSelectedLanguages(
                    $post->ID
                ),
            selectedToolProficiencies:
                $this->mapSelectedTools(
                    $post->ID
                ),
            spellbook: $this->mapSpellbook(
                $post->ID
            ),
            callingPath: $this->mapCallingPath(
                $post->ID
            ),
            pathGifts: $this->mapPathGifts(
                $post->ID
            ),
            purse: CharacterPurse::fromCopper(
                max(
                    0,
                    (int) get_post_meta(
                        $post->ID,
                        self::META_PURSE,
                        true
                    )
                )
            ),
            racialSkillProficiencies: SkillProficiencies::proficient(
                $this->mapStringArray($post->ID, self::META_RACIAL_SKILLS)
            )
        );
    }

    /**
     * Read a persisted list of canonical string identifiers.
     *
     * @return array<int,string>
     */
    private function mapStringArray(int $postId, string $metaKey): array
    {
        $stored = get_post_meta($postId, $metaKey, true);

        return is_array($stored)
            ? array_values(array_filter($stored, 'is_string'))
            : [];
    }

    /**
     * Rebuild the certified Character spellbook.
     */
    private function mapSpellbook(
        int $postId
    ): Spellbook {
        $stored = get_post_meta(
            $postId,
            self::META_SPELLBOOK,
            true
        );

        return is_array($stored)
            ? Spellbook::fromArray($stored)
            : Spellbook::empty();
    }

    /**
     * Rebuild the permanently certified Path of Calling.
     *
     * This intentionally reads the same `_gmrc_subclass` metadata used by
     * the Grand Catalogue build profile so older Characters migrate without
     * a duplicate field.
     */
    private function mapCallingPath(
        int $postId
    ): CallingPath {
        $stored = get_post_meta(
            $postId,
            self::META_CALLING_PATH,
            true
        );

        return CallingPath::fromString(
            is_string($stored)
                ? $stored
                : ''
        );
    }

    /**
     * Rebuild the permanently certified Gifts of the Path.
     */
    private function mapPathGifts(int $postId): PathGifts
    {
        $stored = get_post_meta(
            $postId,
            self::META_PATH_GIFTS,
            true
        );

        return PathGifts::fromArray(
            is_array($stored)
                ? $stored
                : []
        );
    }

    /**
     * Rebuild the Character race from post metadata.
     */
    private function mapRace(
        int $postId
    ): Race {
        $stored = get_post_meta(
            $postId,
            self::META_RACE,
            true
        );

        /*
         * Temporary compatibility fallback for older
         * Character records created before race persistence.
         */
        $value = is_string($stored) && $stored !== ''
            ? $stored
            : 'fructan';

        return Race::fromString($value);
    }

    /**
     * Rebuild the Character class from post metadata.
     */
    private function mapCharacterClass(
        int $postId
    ): CharacterClass {
        $stored = get_post_meta(
            $postId,
            self::META_CLASS,
            true
        );

        /*
         * Temporary compatibility fallback for older
         * Character records created before class persistence.
         */
        $value = is_string($stored) && $stored !== ''
            ? $stored
            : 'fighter';

        return CharacterClass::fromString($value);
    }

    /**
     * Rebuild the Character background from post metadata.
     *
     * Legacy Characters without stored background metadata
     * are migrated to the default Market Runner background.
     */
    private function mapBackground(
        int $postId
    ): Background {
        $stored = get_post_meta(
            $postId,
            self::META_BACKGROUND,
            true
        );
    
        if (
            ! is_string($stored)
            || trim($stored) === ''
        ) {
            $stored = 'market-runner';
    
            update_post_meta(
                $postId,
                self::META_BACKGROUND,
                $stored
            );
        }
    
        $skills = get_post_meta($postId, self::META_BACKGROUND_SKILLS, true);
        $tools = get_post_meta($postId, self::META_BACKGROUND_TOOLS, true);
        $label = get_post_meta($postId, self::META_BACKGROUND_LABEL, true);

        if (is_array($skills) && is_array($tools)) {
            return Background::fromStringWithMechanics(
                $stored,
                $skills,
                $tools,
                is_string($label) && trim($label) !== '' ? $label : null
            );
        }

        /*
         * Legacy Characters intentionally resolve the immutable bundled
         * Background baseline, never a later Steward override. Their next
         * save will persist that baseline as an explicit mechanics snapshot.
         */
        return Background::fromString($stored);
    }

    /**
     * Rebuild explicitly selected registration languages.
     */
    private function mapSelectedLanguages(
        int $postId
    ): Languages {
        $stored = get_post_meta(
            $postId,
            self::META_SELECTED_LANGUAGES,
            true
        );

        if (! is_array($stored)) {
            return Languages::none();
        }

        $languages = array_values(
            array_filter(
                $stored,
                static fn (
                    mixed $language
                ): bool =>
                    is_string($language)
                    && \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language::supports(
                        $language
                    )
            )
        );

        return Languages::fromStrings(
            $languages
        );
    }

    /**
     * Rebuild explicitly selected registration tools.
     */
    private function mapSelectedTools(
        int $postId
    ): ToolProficiencies {
        $stored = get_post_meta(
            $postId,
            self::META_SELECTED_TOOLS,
            true
        );

        if (! is_array($stored)) {
            return ToolProficiencies::none();
        }

        $tools = array_values(
            array_filter(
                $stored,
                static fn (
                    mixed $tool
                ): bool =>
                    is_string($tool)
                    && \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency::supports(
                        $tool
                    )
            )
        );

        return ToolProficiencies::fromStrings(
            $tools
        );
    }

    /**
     * Rebuild an ability score from post metadata.
     */
    private function mapAbilityScore(
        int $postId,
        string $metaKey
    ): AbilityScore {
        $stored = get_post_meta(
            $postId,
            $metaKey,
            true
        );

        $value = is_numeric($stored)
            ? (int) $stored
            : 10;

        return AbilityScore::fromInt(
            max(
                1,
                min(30, $value)
            )
        );
    }
}
