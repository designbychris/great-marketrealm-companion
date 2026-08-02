<?php

declare(strict_types=1);

/*
 * WordPress functions required by the controller dependencies
 * but not currently supplied by tests/bootstrap.php.
 */
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

    if (! function_exists('wp_validate_redirect')) {
        function wp_validate_redirect(
            string $location,
            string $fallback = ''
        ): string {
            return $location !== ''
                ? $location
                : $fallback;
        }
    }

    if (! function_exists('is_user_logged_in')) {
        function is_user_logged_in(): bool
        {
            return true;
        }
    }
}

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Controllers {

    use GreatMarketrealmCompanion\Core\Http\Request;
    use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
    use GreatMarketrealmCompanion\Core\Session\FlashStore;
    use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
    use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
    use GreatMarketrealmCompanion\Modules\Characters\Actions\UpdateCharacterAction;
    use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
    use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
    use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
    use GreatMarketrealmCompanion\Modules\Characters\Requests\StoreCharacterRequest;
    use GreatMarketrealmCompanion\Modules\Characters\Rules\CharacterCreationRules;
    use GreatMarketrealmCompanion\Modules\Characters\Services\CharacterFactory;
    use GreatMarketrealmCompanion\Services\Auby\Auby;
    use GreatMarketrealmCompanion\Services\Auby\Quote;
    use GreatMarketrealmCompanion\Services\Auby\QuoteCategories;
    use GreatMarketrealmCompanion\Services\Auby\QuoteCollection;
    use GreatMarketrealmCompanion\Services\Auby\QuoteRepository;
    use GreatMarketrealmCompanion\Services\Characters\ClassRegistry;
    use GreatMarketrealmCompanion\Services\Characters\RaceRegistry;
    use GreatMarketrealmCompanion\Services\Definitions\Definitions;
    use GreatMarketrealmCompanion\Services\Guild\GuildSealRegistry;
    use GreatMarketrealmCompanion\Tests\Stubs\SessionStoreStub;
    use GreatMarketrealmCompanion\Tests\Stubs\ViewFactorySpy;
    use PHPUnit\Framework\TestCase;
    use RuntimeException;

    final class CharacterControllerTest extends TestCase
    {
        protected function setUp(): void
        {
            $_GET = [];
            $_POST = [];
            $_SERVER = [];
        }

        protected function tearDown(): void
        {
            $_GET = [];
            $_POST = [];
            $_SERVER = [];
        }

        public function testIndexRendersTheCharacterIndexView(): void
        {
            $repository = new CharacterControllerRepositorySpy();

            $character = $this->character();

            $repository->characters = [
                $character,
            ];

            $views = new ViewFactorySpy();

            $views->willRender(
                '<characters-index />'
            );

            $controller = $this->controller(
                repository: $repository,
                views: $views
            );

            $response = $controller->index();

            self::assertSame(
                '<characters-index />',
                $response
            );

            $view = $views->lastView();

            self::assertNotNull($view);

            self::assertSame(
                'characters.index',
                $view->name()
            );

            self::assertSame(
                [$character],
                $view->data()['characters']
            );
        }

        public function testIndexSuppliesAnAubyRegisterQuote(): void
        {
            $views = new ViewFactorySpy();

            $controller = $this->controller(
                views: $views
            );

            $controller->index();

            $view = $views->lastView();

            self::assertNotNull($view);

            $quote = $view->data()['aubyQuote'];

            self::assertInstanceOf(
                Quote::class,
                $quote
            );

            self::assertSame(
                QuoteCategories::REGISTER,
                $quote->category()
            );
        }

        public function testIndexSuppliesTheGuildSealRegistry(): void
        {
            $views = new ViewFactorySpy();

            $sealRegistry = new GuildSealRegistry();

            $controller = $this->controller(
                views: $views,
                sealRegistry: $sealRegistry
            );

            $controller->index();

            $view = $views->lastView();

            self::assertNotNull($view);

            self::assertSame(
                $sealRegistry,
                $view->data()['sealRegistry']
            );
        }

        public function testCreateRendersTheCharacterCreationView(): void
        {
            $views = new ViewFactorySpy();

            $views->willRender(
                '<character-create />'
            );

            $controller = $this->controller(
                views: $views
            );

            $response = $controller->create();

            self::assertSame(
                '<character-create />',
                $response
            );

            $view = $views->lastView();

            self::assertNotNull($view);

            self::assertSame(
                'characters.create',
                $view->name()
            );
        }

        public function testCreateSuppliesRaceOptions(): void
        {
            $views = new ViewFactorySpy();

            $controller = $this->controller(
                views: $views
            );

            $controller->create();

            $view = $views->lastView();

            self::assertNotNull($view);

            self::assertSame(
                [
                    'fructan' => 'Fructan',
                    'vegfolk' => 'Vegfolk',
                    'capsicumite' => 'Capsicumite',
                    'fungifolk' => 'Fungifolk',
                    'rootkin' => 'Rootkin',
                ],
                $view->data()['raceOptions']
            );
        }

        public function testCreateSuppliesClassOptions(): void
        {
            $views = new ViewFactorySpy();

            $controller = $this->controller(
                views: $views
            );

            $controller->create();

            $view = $views->lastView();

            self::assertNotNull($view);

            self::assertSame(
                [
                    'grocer' => 'Grocer',
                    'cleaver-saint' => 'Cleaver Saint',
                ],
                $view->data()['classOptions']
            );
        }

        public function testStoreCreatesAndPersistsACharacter(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $repository = new CharacterControllerRepositorySpy();

            $controller = $this->controller(
                repository: $repository
            );

            $controller->store(
                new StoreCharacterRequest()
            );

            self::assertSame(
                1,
                $repository->saveCalls
            );

            self::assertInstanceOf(
                Character::class,
                $repository->savedCharacter
            );

            self::assertSame(
                'Sir Allium',
                $repository
                    ->savedCharacter
                    ->name()
                    ->value()
            );

            self::assertSame(
                'fructan',
                $repository
                    ->savedCharacter
                    ->race()
                    ->value()
            );

            self::assertSame(
                'grocer',
                $repository
                    ->savedCharacter
                    ->characterClass()
                    ->value()
            );
        }

        public function testStoreCalculatesStartingHitPoints(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $repository = new CharacterControllerRepositorySpy();

            $controller = $this->controller(
                repository: $repository
            );

            $controller->store(
                new StoreCharacterRequest()
            );

            self::assertNotNull(
                $repository->savedCharacter
            );

            /*
             * Grocer hit die: 8
             * Default Constitution: 10
             * Constitution modifier: 0
             */
            self::assertSame(
                8,
                $repository
                    ->savedCharacter
                    ->hitPoints()
                    ->maximum()
            );

            self::assertSame(
                8,
                $repository
                    ->savedCharacter
                    ->hitPoints()
                    ->current()
            );
        }

        public function testStoreFlashesASuccessMessage(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $flash = $this->flashStore();

            $controller = $this->controller(
                flash: $flash
            );

            $controller->store(
                new StoreCharacterRequest()
            );

            /*
             * Move newly flashed values into the current
             * request before retrieving them.
             */
            $flash->age();

            self::assertSame(
                'Your character has entered the Marketrealm!',
                $flash->success()
            );
        }

        public function testStoreRedirectsToTheCharacterIndex(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $controller = $this->controller();

            $response = $controller->store(
                new StoreCharacterRequest()
            );

            self::assertSame(
                303,
                $response->status()
            );

            self::assertSame(
                'https://example.test/companion/'
                    . '?gmrc_route=characters',
                $response->destination()
            );

            self::assertSame(
                $response->destination(),
                $response->headers()['Location']
            );
        }

        public function testUpdateRenamesAndPersistsAnExistingCharacter(): void
        {
            $character = $this->character();

            $repository = new CharacterControllerRepositorySpy();

            $repository->characters = [
                $character,
            ];

            $_POST = [
                'name' => 'Sir Allium the Brave',
            ];

            $controller = $this->controller(
                repository: $repository
            );

            $returned = $controller->update(
                $character->id()->value()
            );

            self::assertSame(
                $character,
                $returned
            );

            self::assertSame(
                'Sir Allium the Brave',
                $character->name()->value()
            );

            self::assertSame(
                1,
                $repository->saveCalls
            );

            self::assertSame(
                $character,
                $repository->savedCharacter
            );
        }

        public function testUpdateLeavesNameUnchangedWhenNoNameIsSupplied(): void
        {
            $character = $this->character();

            $repository = new CharacterControllerRepositorySpy();

            $repository->characters = [
                $character,
            ];

            $controller = $this->controller(
                repository: $repository
            );

            $controller->update(
                $character->id()->value()
            );

            self::assertSame(
                'Sir Allium',
                $character->name()->value()
            );

            self::assertSame(
                1,
                $repository->saveCalls
            );
        }

        public function testUpdateThrowsWhenCharacterCannotBeFound(): void
        {
            $this->expectException(
                RuntimeException::class
            );

            $this->expectExceptionMessage(
                'The requested character could not be found.'
            );

            $controller = $this->controller();

            $controller->update(
                CharacterId::generate()->value()
            );
        }

        public function testDestroyDeletesACharacterByItsUlid(): void
        {
            $repository = new CharacterControllerRepositorySpy();

            $id = CharacterId::generate();

            $controller = $this->controller(
                repository: $repository
            );

            $result = $controller->destroy(
                $id->value()
            );

            self::assertTrue($result);

            self::assertSame(
                1,
                $repository->deleteCalls
            );

            self::assertInstanceOf(
                CharacterId::class,
                $repository->deletedId
            );

            self::assertTrue(
                $repository
                    ->deletedId
                    ->equals($id)
            );
        }

        private function controller(
            ?CharacterRepositoryInterface $repository = null,
            ?ViewFactorySpy $views = null,
            ?FlashStore $flash = null,
            ?GuildSealRegistry $sealRegistry = null
        ): CharacterController {
            $repository ??=
                new CharacterControllerRepositorySpy();

            $views ??= new ViewFactorySpy();

            $flash ??= $this->flashStore();

            $creationRules =
                new CharacterCreationRules();

            $characterFactory =
                new CharacterFactory(
                    $creationRules
                );

            $createCharacter =
                new CreateCharacterAction(
                    $repository
                );

            $updateCharacter =
                new UpdateCharacterAction(
                    $repository
                );

            $deleteCharacter =
                new DeleteCharacterAction(
                    $repository
                );

            $definitions = new Definitions();

            return new CharacterController(
                characters: $repository,
                views: $views,
                characterFactory: $characterFactory,
                createCharacter: $createCharacter,
                updateCharacter: $updateCharacter,
                deleteCharacter: $deleteCharacter,
                request: new Request(),
                responses: new ResponseFactory(),
                flash: $flash,
                auby: $this->auby(),
                sealRegistry: $sealRegistry
                    ?? new GuildSealRegistry(),
                raceRegistry: new RaceRegistry(
                    $definitions
                ),
                classRegistry: new ClassRegistry(
                    $definitions
                )
            );
        }

        private function flashStore(): FlashStore
        {
            return new FlashStore(
                new SessionStoreStub()
            );
        }

        private function auby(): Auby
        {
            $quotes = new QuoteCollection([
                new Quote(
                    text: 'Every hero begins with a name.',
                    author: 'Auby',
                    category: QuoteCategories::REGISTER,
                    allowCoffeeStain: false,
                    allowInkBlot: false
                ),
            ]);

            return new Auby(
                new QuoteRepository($quotes)
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

    /**
     * In-memory Character repository used by controller tests.
     */
    final class CharacterControllerRepositorySpy implements
        CharacterRepositoryInterface
    {
        /**
         * @var Character[]
         */
        public array $characters = [];

        public ?Character $savedCharacter = null;

        public ?CharacterId $deletedId = null;

        public int $saveCalls = 0;

        public int $deleteCalls = 0;

        /**
         * @return Character[]
         */
        public function all(): array
        {
            return $this->characters;
        }

        public function find(
            CharacterId $id
        ): ?Character {
            foreach ($this->characters as $character) {
                if (
                    $character
                        ->id()
                        ->equals($id)
                ) {
                    return $character;
                }
            }

            return null;
        }

        public function save(
            Character $character
        ): void {
            $this->saveCalls++;
            $this->savedCharacter = $character;

            foreach (
                $this->characters
                as $index => $storedCharacter
            ) {
                if (
                    $storedCharacter
                        ->id()
                        ->equals(
                            $character->id()
                        )
                ) {
                    $this->characters[$index] =
                        $character;

                    return;
                }
            }

            $this->characters[] = $character;
        }

        public function delete(
            CharacterId $id
        ): void {
            $this->deleteCalls++;
            $this->deletedId = $id;

            $this->characters = array_values(
                array_filter(
                    $this->characters,
                    static fn (
                        Character $character
                    ): bool => ! $character
                        ->id()
                        ->equals($id)
                )
            );
        }


        public function testShowRendersTheCharacterShowView(): void
        {
            $character = $this->character();
        
            $repository = new CharacterControllerRepositorySpy();
        
            $repository->characters = [
                $character,
            ];
        
            $views = new ViewFactorySpy();
        
            $views->willRender(
                '<character-show />'
            );
        
            $controller = $this->controller(
                repository: $repository,
                views: $views
            );
        
            $response = $controller->show(
                $character->id()->value()
            );
        
            self::assertSame(
                '<character-show />',
                $response
            );
        
            $view = $views->lastView();
        
            self::assertNotNull($view);
        
            self::assertSame(
                'characters.show',
                $view->name()
            );
        }
        
        public function testShowSuppliesTheRequestedCharacter(): void
        {
            $character = $this->character();
        
            $repository = new CharacterControllerRepositorySpy();
        
            $repository->characters = [
                $character,
            ];
        
            $views = new ViewFactorySpy();
        
            $controller = $this->controller(
                repository: $repository,
                views: $views
            );
        
            $controller->show(
                $character->id()->value()
            );
        
            $view = $views->lastView();
        
            self::assertNotNull($view);
        
            self::assertSame(
                $character,
                $view->data()['character']
            );
        }
        
        public function testShowSuppliesTheGuildSealRegistry(): void
        {
            $character = $this->character();
        
            $repository = new CharacterControllerRepositorySpy();
        
            $repository->characters = [
                $character,
            ];
        
            $views = new ViewFactorySpy();
        
            $sealRegistry = new GuildSealRegistry();
        
            $controller = $this->controller(
                repository: $repository,
                views: $views,
                sealRegistry: $sealRegistry
            );
        
            $controller->show(
                $character->id()->value()
            );
        
            $view = $views->lastView();
        
            self::assertNotNull($view);
        
            self::assertSame(
                $sealRegistry,
                $view->data()['sealRegistry']
            );
        }
        
        public function testShowThrowsWhenCharacterCannotBeFound(): void
        {
            $this->expectException(
                RuntimeException::class
            );
        
            $this->expectExceptionMessage(
                'The requested character could not be found.'
            );
        
            $this->controller()->show(
                CharacterId::generate()->value()
            );
        }
        
        public function testEditRendersTheCharacterEditView(): void
        {
            $character = $this->character();
        
            $repository = new CharacterControllerRepositorySpy();
        
            $repository->characters = [
                $character,
            ];
        
            $views = new ViewFactorySpy();
        
            $views->willRender(
                '<character-edit />'
            );
        
            $controller = $this->controller(
                repository: $repository,
                views: $views
            );
        
            $response = $controller->edit(
                $character->id()->value()
            );
        
            self::assertSame(
                '<character-edit />',
                $response
            );
        
            $view = $views->lastView();
        
            self::assertNotNull($view);
        
            self::assertSame(
                'characters.edit',
                $view->name()
            );
        }
        
        public function testEditSuppliesCharacterAndFormOptions(): void
        {
            $character = $this->character();
        
            $repository = new CharacterControllerRepositorySpy();
        
            $repository->characters = [
                $character,
            ];
        
            $views = new ViewFactorySpy();
        
            $controller = $this->controller(
                repository: $repository,
                views: $views
            );
        
            $controller->edit(
                $character->id()->value()
            );
        
            $view = $views->lastView();
        
            self::assertNotNull($view);
        
            $data = $view->data();
        
            self::assertSame(
                $character,
                $data['character']
            );
        
            self::assertSame(
                [
                    'fructan' => 'Fructan',
                    'vegfolk' => 'Vegfolk',
                    'capsicumite' => 'Capsicumite',
                    'fungifolk' => 'Fungifolk',
                    'rootkin' => 'Rootkin',
                ],
                $data['raceOptions']
            );
        
            self::assertSame(
                [
                    'grocer' => 'Grocer',
                    'cleaver-saint' => 'Cleaver Saint',
                ],
                $data['classOptions']
            );
        }
        
        public function testEditThrowsWhenCharacterCannotBeFound(): void
        {
            $this->expectException(
                RuntimeException::class
            );
        
            $this->expectExceptionMessage(
                'The requested character could not be found.'
            );
        
            $this->controller()->edit(
                CharacterId::generate()->value()
            );
        }
    }
}
