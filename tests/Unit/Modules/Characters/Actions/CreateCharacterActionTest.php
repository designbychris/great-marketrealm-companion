<?php

declare(strict_types=1);

namespace {

    if (! function_exists('sanitize_key')) {
        function sanitize_key(string $value): string
        {
            $value = strtolower($value);

            return preg_replace(
                '/[^a-z0-9_\-]/',
                '',
                $value
            ) ?? '';
        }
    }
}

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Actions {

    use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
    use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
    use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
    use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;
    use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitLayerRegistryInterface;
    use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;
    use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRecipeGenerator;
    use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitSeed;
    use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\PortraitRecipe;
    use PHPUnit\Framework\TestCase;

    final class CreateCharacterActionTest extends TestCase
    {
        public function testPersistsTheCharacter(): void
        {
            $characters = new CharacterRepositorySpy();
            $portraits = new CharacterPortraitRepositorySpy();

            $action = $this->action(
                $characters,
                $portraits
            );

            $character = $this->character();

            $returned = $action->handle(
                $character
            );

            self::assertSame(
                $character,
                $returned
            );

            self::assertSame(
                $character,
                $characters->savedCharacter
            );
        }

        public function testCallsCharacterSaveExactlyOnce(): void
        {
            $characters = new CharacterRepositorySpy();
            $portraits = new CharacterPortraitRepositorySpy();

            $this->action(
                $characters,
                $portraits
            )->handle(
                $this->character()
            );

            self::assertSame(
                1,
                $characters->saveCalls
            );
        }

        public function testPersistsAGeneratedPortrait(): void
        {
            $characters = new CharacterRepositorySpy();
            $portraits = new CharacterPortraitRepositorySpy();

            $character = $this->character();

            $this->action(
                $characters,
                $portraits
            )->handle(
                $character
            );

            self::assertSame(
                1,
                $portraits->saveCalls
            );

            self::assertNotNull(
                $portraits->savedCharacterId
            );

            self::assertTrue(
                $portraits
                    ->savedCharacterId
                    ->equals(
                        $character->id()
                    )
            );

            self::assertInstanceOf(
                CharacterPortrait::class,
                $portraits->savedPortrait
            );

            self::assertTrue(
                $portraits
                    ->savedPortrait
                    ->mode()
                    ->isGenerated()
            );

            self::assertNotNull(
                $portraits
                    ->savedPortrait
                    ->recipe()
            );
        }

        public function testGeneratedPortraitUsesTheCharacterSeed(): void
        {
            $characters = new CharacterRepositorySpy();
            $portraits = new CharacterPortraitRepositorySpy();

            $character = $this->character();

            $this->action(
                $characters,
                $portraits
            )->handle(
                $character
            );

            $portrait = $portraits->savedPortrait;

            self::assertNotNull($portrait);

            $recipe = $portrait->recipe();

            self::assertNotNull($recipe);

            self::assertTrue(
                $recipe
                    ->seed()
                    ->equals(
                        PortraitSeed::fromCharacterId(
                            $character->id()
                        )
                    )
            );
        }

        public function testGeneratedRecipeIsDeterministic(): void
        {
            $character = $this->character();

            $firstCharacters =
                new CharacterRepositorySpy();

            $firstPortraits =
                new CharacterPortraitRepositorySpy();

            $secondCharacters =
                new CharacterRepositorySpy();

            $secondPortraits =
                new CharacterPortraitRepositorySpy();

            $this->action(
                $firstCharacters,
                $firstPortraits
            )->handle(
                $character
            );

            $this->action(
                $secondCharacters,
                $secondPortraits
            )->handle(
                $character
            );

            $firstRecipe =
                $firstPortraits
                    ->savedPortrait
                    ?->recipe();

            $secondRecipe =
                $secondPortraits
                    ->savedPortrait
                    ?->recipe();

            self::assertNotNull($firstRecipe);
            self::assertNotNull($secondRecipe);

            self::assertTrue(
                $firstRecipe->equals(
                    $secondRecipe
                )
            );
        }

        private function action(
            CharacterRepositoryInterface $characters,
            CharacterPortraitRepositoryInterface $portraits
        ): CreateCharacterAction {
            return new CreateCharacterAction(
                $characters,
                $portraits,
                new PortraitRecipeGenerator(
                    new CharacterActionPortraitLayerRegistry()
                )
            );
        }

        private function character(): Character
        {
            return Character::create(
                CharacterId::generate(),
                CharacterName::fromString(
                    'Sir Allium'
                ),
                Race::fromString(
                    'fructan'
                ),
                CharacterClass::fromString(
                    'fighter'
                ),
                HitPoints::full(12),
                AbilityScores::average()
            );
        }
    }

    final class CharacterRepositorySpy implements
        CharacterRepositoryInterface
    {
        public ?Character $savedCharacter = null;

        public int $saveCalls = 0;

        public function all(): array
        {
            return [];
        }

        public function find(
            CharacterId $id
        ): ?Character {
            return null;
        }

        public function save(
            Character $character
        ): void {
            $this->saveCalls++;

            $this->savedCharacter = $character;
        }

        public function delete(
            CharacterId $id
        ): void {
        }
    }

    final class CharacterPortraitRepositorySpy implements
        CharacterPortraitRepositoryInterface
    {
        public ?CharacterId $savedCharacterId = null;

        public ?CharacterPortrait $savedPortrait = null;

        public int $saveCalls = 0;

        public function find(
            CharacterId $characterId
        ): ?CharacterPortrait {
            return null;
        }

        public function save(
            CharacterId $characterId,
            CharacterPortrait $portrait
        ): void {
            $this->saveCalls++;

            $this->savedCharacterId =
                $characterId;

            $this->savedPortrait =
                $portrait;
        }

        public function delete(
            CharacterId $characterId
        ): void {
        }
    }

    final class CharacterActionPortraitLayerRegistry implements
        PortraitLayerRegistryInterface
    {
        public function shared(): array
        {
            return [
                'background' => [
                    'background-test-01',
                    'background-test-02',
                ],
                'eyes' => [
                    'eyes-test-01',
                    'eyes-test-02',
                ],
            ];
        }

        public function forRace(
            string $race
        ): array {
            return [
                'body' => [
                    $race . '-body-test-01',
                    $race . '-body-test-02',
                ],
                'head' => [
                    $race . '-head-test-01',
                    $race . '-head-test-02',
                ],
            ];
        }

        public function forClass(
            string $characterClass
        ): array {
            return [
                'outfit' => [
                    $characterClass
                        . '-outfit-test-01',
                    $characterClass
                        . '-outfit-test-02',
                ],
                'equipment' => [
                    $characterClass
                        . '-equipment-test-01',
                    $characterClass
                        . '-equipment-test-02',
                ],
            ];
        }

        public function supports(
            string $slot,
            string $layerId,
            string $race,
            string $characterClass
        ): bool {
            $available = array_merge(
                $this->shared(),
                $this->forRace($race),
                $this->forClass(
                    $characterClass
                )
            );

            return isset($available[$slot])
                && in_array(
                    $layerId,
                    $available[$slot],
                    true
                );
        }

        public function testUsesASubmittedPortraitRecipe(): void
        {
            $characters =
                new CharacterRepositorySpy();
        
            $portraits =
                new CharacterPortraitRepositorySpy();
        
            $character = $this->character();
        
            $submittedRecipe =
                PortraitRecipe::create(
                    PortraitSeed::fromString(
                        '1234567890abcdef'
                    ),
                    [
                        'background' =>
                            'background-parchment-01',
                        'body' =>
                            'fructan-body-01',
                        'head' =>
                            'fructan-head-01',
                        'eyes' =>
                            'eyes-round-01',
                        'mouth' =>
                            'mouth-smile-01',
                        'palette' =>
                            'fructan-palette-01',
                        'heritage' =>
                            'fructan-heritage-none',
                        'outfit' =>
                            'fighter-outfit-01',
                        'equipment' =>
                            'fighter-equipment-01',
                        'class_accessory' =>
                            'fighter-accessory-none',
                        'frame' =>
                            'frame-guild-gold-01',
                        'effects' =>
                            'effects-none',
                    ]
                );
        
            $this->action(
                $characters,
                $portraits
            )->handle(
                $character,
                $submittedRecipe
            );
        
            $savedRecipe =
                $portraits
                    ->savedPortrait
                    ?->recipe();
        
            self::assertNotNull(
                $savedRecipe
            );
        
            self::assertTrue(
                $submittedRecipe->equals(
                    $savedRecipe
                )
            );
        }
    }
}
