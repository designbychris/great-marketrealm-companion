<?php

declare(strict_types=1);

/*
 * Namespaced WordPress-function replacement.
 *
 * StoreCharacterRequest is in this namespace, so its call to
 * is_user_logged_in() resolves here before the global function.
 */
namespace GreatMarketrealmCompanion\Modules\Characters\Requests {

    final class StoreCharacterRequestWordPressState
    {
        public static bool $loggedIn = true;

        public static function reset(): void
        {
            self::$loggedIn = true;
        }
    }

    function is_user_logged_in(): bool
    {
        return StoreCharacterRequestWordPressState::$loggedIn;
    }
}

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Requests {

    use GreatMarketrealmCompanion\Core\Http\Validation\ValidationException;
    use GreatMarketrealmCompanion\Modules\Characters\Requests\StoreCharacterRequest;
    use GreatMarketrealmCompanion\Modules\Characters\Requests\StoreCharacterRequestWordPressState;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
    use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
    use PHPUnit\Framework\Attributes\DataProvider;
    use PHPUnit\Framework\TestCase;

    final class StoreCharacterRequestTest extends TestCase
    {
        protected function setUp(): void
        {
            $_GET = [];
            $_POST = [];
            $_SERVER = [];

            StoreCharacterRequestWordPressState::reset();
        }

        protected function tearDown(): void
        {
            $_GET = [];
            $_POST = [];
            $_SERVER = [];

            StoreCharacterRequestWordPressState::reset();
        }

        public function testAuthorisesLoggedInUsers(): void
        {
            StoreCharacterRequestWordPressState::$loggedIn = true;

            self::assertTrue(
                (new StoreCharacterRequest())->authorize()
            );
        }

        public function testRejectsGuests(): void
        {
            StoreCharacterRequestWordPressState::$loggedIn = false;

            self::assertFalse(
                (new StoreCharacterRequest())->authorize()
            );
        }

        public function testIsAuthorizedUsesTheRequestAuthorisationRule(): void
        {
            StoreCharacterRequestWordPressState::$loggedIn = false;

            self::assertFalse(
                (new StoreCharacterRequest())->isAuthorized()
            );

            StoreCharacterRequestWordPressState::$loggedIn = true;

            self::assertTrue(
                (new StoreCharacterRequest())->isAuthorized()
            );
        }

        public function testDefinesCharacterAndPortraitCreationFields(): void
        {
            $rules = (new StoreCharacterRequest())->rules();

            self::assertSame(
            [
                'name',
                'race',
                'class',
                'heritage',
                'subclass',
                'portrait_seed',
                'portrait_background',
                'portrait_body',
                'portrait_head',
                'portrait_eyes',
                'portrait_mouth',
                'portrait_palette',
                'portrait_heritage',
                'portrait_outfit',
                'portrait_equipment',
                'portrait_accessory',
                'portrait_class_effects',
                'portrait_guild_ornament',
                'portrait_frame',
                'portrait_effects',
            ],
            array_keys(
                $rules
            )
        );

            self::assertArrayNotHasKey(
                'level',
                $rules
            );
        }

        public function testDefinesTheExpectedNameRules(): void
        {
            self::assertSame(
                [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                ],
                (new StoreCharacterRequest())
                    ->rules()['name']
            );
        }

        public function testDefinesTheExpectedRaceRules(): void
        {
            $request = new StoreCharacterRequest();
        
            self::assertSame(
                [
                    'required',
                    'string',
                    'max:100',
                    'in:' . implode(
                        ',',
                        Race::identifiers()
                    ),
                ],
                $request->rules()['race']
            );
        }

        public function testDefinesTheExpectedClassRules(): void
        {
            $request = new StoreCharacterRequest();
        
            self::assertSame(
                [
                    'required',
                    'string',
                    'max:100',
                    'in:' . implode(
                        ',',
                        CharacterClass::identifiers()
                    ),
                ],
                $request->rules()['class']
            );
        }
        
        public function testReturnsValidatedCharacterData(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $data = (new StoreCharacterRequest())
                ->characterData();

            self::assertSame(
                [
                    'name' => 'Sir Allium',
                    'race' => 'fructan',
                    'class' => 'grocer',
                ],
                $data
            );
        }

        public function testCharacterDataContainsOnlyPrimitiveStrings(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $data = (new StoreCharacterRequest())
                ->characterData();

            foreach ($data as $value) {
                self::assertIsString($value);
            }
        }

        public function testIgnoresASubmittedLevel(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
                'level' => '20',
            ];

            $data = (new StoreCharacterRequest())
                ->characterData();

            self::assertArrayNotHasKey(
                'level',
                $data
            );

            self::assertSame(
                [
                    'name' => 'Sir Allium',
                    'race' => 'fructan',
                    'class' => 'grocer',
                ],
                $data
            );
        }

        public function testIgnoresUnrecognisedInput(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
                'unexpected' => 'should not survive',
            ];

            $data = (new StoreCharacterRequest())
                ->characterData();

            self::assertArrayNotHasKey(
                'unexpected',
                $data
            );
        }

        #[DataProvider('missingRequiredFieldProvider')]
        public function testRejectsMissingRequiredFields(
            string $missingField
        ): void {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            unset($_POST[$missingField]);

            try {
                (new StoreCharacterRequest())
                    ->characterData();

                self::fail(
                    sprintf(
                        'Expected validation to fail for missing field [%s].',
                        $missingField
                    )
                );
            } catch (ValidationException $exception) {
                self::assertTrue(
                    $exception->has($missingField)
                );

                self::assertSame(
                    $_POST,
                    $exception->oldInput()
                );
            }
        }

        /**
         * @return array<string,array{string}>
         */
        public static function missingRequiredFieldProvider(): array
        {
            return [
                'name' => ['name'],
                'race' => ['race'],
                'class' => ['class'],
            ];
        }

        #[DataProvider('emptyRequiredFieldProvider')]
        public function testRejectsEmptyRequiredFields(
            string $field,
            mixed $value
        ): void {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $_POST[$field] = $value;

            try {
                (new StoreCharacterRequest())
                    ->characterData();

                self::fail(
                    sprintf(
                        'Expected validation to fail for empty field [%s].',
                        $field
                    )
                );
            } catch (ValidationException $exception) {
                self::assertTrue(
                    $exception->has($field)
                );
            }
        }

        /**
         * @return array<string,array{string,mixed}>
         */
        public static function emptyRequiredFieldProvider(): array
        {
            return [
                'empty name' => [
                    'name',
                    '',
                ],
                'whitespace name' => [
                    'name',
                    '   ',
                ],
                'empty race' => [
                    'race',
                    '',
                ],
                'whitespace race' => [
                    'race',
                    '   ',
                ],
                'empty class' => [
                    'class',
                    '',
                ],
                'whitespace class' => [
                    'class',
                    '   ',
                ],
            ];
        }

        public function testRejectsANameShorterThanTwoCharacters(): void
        {
            $_POST = [
                'name' => 'A',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $this->expectValidationErrorFor(
                'name'
            );
        }

        public function testAcceptsANameAtTheMinimumLength(): void
        {
            $_POST = [
                'name' => 'Al',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            self::assertSame(
                'Al',
                (new StoreCharacterRequest())
                    ->characterData()['name']
            );
        }

        public function testAcceptsANameAtTheMaximumLength(): void
        {
            $name = str_repeat('A', 100);

            $_POST = [
                'name' => $name,
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            self::assertSame(
                $name,
                (new StoreCharacterRequest())
                    ->characterData()['name']
            );
        }

        public function testRejectsANameLongerThanOneHundredCharacters(): void
        {
            $_POST = [
                'name' => str_repeat('A', 101),
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $this->expectValidationErrorFor(
                'name'
            );
        }

        public function testRejectsARaceLongerThanOneHundredCharacters(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => str_repeat('R', 101),
                'class' => 'grocer',
            ];

            $this->expectValidationErrorFor(
                'race'
            );
        }

        public function testRejectsAClassLongerThanOneHundredCharacters(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => str_repeat('C', 101),
            ];

            $this->expectValidationErrorFor(
                'class'
            );
        }

        #[DataProvider('nonStringFieldProvider')]
        public function testRejectsNonStringValues(
            string $field,
            mixed $value
        ): void {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $_POST[$field] = $value;

            $this->expectValidationErrorFor(
                $field
            );
        }

        /**
         * @return array<string,array{string,mixed}>
         */
        public static function nonStringFieldProvider(): array
        {
            return [
                'array name' => [
                    'name',
                    ['Sir Allium'],
                ],
                'integer name' => [
                    'name',
                    123,
                ],
                'array race' => [
                    'race',
                    ['fructan'],
                ],
                'integer race' => [
                    'race',
                    123,
                ],
                'array class' => [
                    'class',
                    ['grocer'],
                ],
                'integer class' => [
                    'class',
                    123,
                ],
            ];
        }

        public function testValidatedInputIsCached(): void
        {
            $_POST = [
                'name' => 'Sir Allium',
                'race' => 'fructan',
                'class' => 'grocer',
            ];

            $request = new StoreCharacterRequest();

            $first = $request->validated();

            $_POST['name'] = 'Lady Leek';

            $second = $request->validated();

            self::assertSame(
                $first,
                $second
            );

            self::assertSame(
                'Sir Allium',
                $second->string('name')
            );
        }

        private function expectValidationErrorFor(
            string $field
        ): void {
            try {
                (new StoreCharacterRequest())
                    ->characterData();

                self::fail(
                    sprintf(
                        'Expected validation to fail for field [%s].',
                        $field
                    )
                );
            } catch (ValidationException $exception) {
                self::assertTrue(
                    $exception->has($field)
                );

                self::assertNotNull(
                    $exception->first($field)
                );
            }
        }
    }
}
