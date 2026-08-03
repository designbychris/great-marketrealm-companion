<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CharacterClassTest extends TestCase
{
    public function testCanBeCreatedFromAString(): void
    {
        $class = CharacterClass::fromString(
            'fighter'
        );

        self::assertSame(
            'fighter',
            $class->value()
        );
    }

    public function testNormalisesUppercaseInput(): void
    {
        $class = CharacterClass::fromString(
            'FIGHTER'
        );

        self::assertSame(
            'fighter',
            $class->value()
        );
    }

    public function testTrimsWhitespace(): void
    {
        $class = CharacterClass::fromString(
            '  fighter  '
        );

        self::assertSame(
            'fighter',
            $class->value()
        );
    }

    public function testNormalisesSpacesToHyphens(): void
    {
        $class = CharacterClass::fromString(
            'Cleaver Saint'
        );

        self::assertSame(
            'cleaver-saint',
            $class->value()
        );
    }

    public function testNormalisesUnderscoresToHyphens(): void
    {
        $class = CharacterClass::fromString(
            'cleaver_saint'
        );

        self::assertSame(
            'cleaver-saint',
            $class->value()
        );
    }

    public function testReturnsStandardClassDisplayLabel(): void
    {
        self::assertSame(
            'Fighter',
            CharacterClass::fromString(
                'fighter'
            )->label()
        );
    }

    public function testReturnsMarketrealmClassDisplayLabel(): void
    {
        self::assertSame(
            'Cleaver Saint',
            CharacterClass::fromString(
                'cleaver-saint'
            )->label()
        );
    }

    public function testCanBeConvertedToAString(): void
    {
        $class = CharacterClass::fromString(
            'grocer'
        );

        self::assertSame(
            'grocer',
            (string) $class
        );
    }

    public function testEqualClassesAreEqual(): void
    {
        $first = CharacterClass::fromString(
            'cleaver-saint'
        );

        $second = CharacterClass::fromString(
            'Cleaver Saint'
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentClassesAreNotEqual(): void
    {
        $grocer = CharacterClass::fromString(
            'grocer'
        );

        $fighter = CharacterClass::fromString(
            'fighter'
        );

        self::assertFalse(
            $grocer->equals($fighter)
        );
    }

    public function testRejectsEmptyClass(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterClass::fromString('');
    }

    public function testRejectsWhitespaceOnlyClass(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterClass::fromString('   ');
    }

    public function testRejectsUnsupportedClass(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterClass::fromString(
            'sandwich-knight'
        );
    }

    public function testSupportsNormalisedClassIdentifiers(): void
    {
        self::assertTrue(
            CharacterClass::supports(
                ' Cleaver Saint '
            )
        );

        self::assertTrue(
            CharacterClass::supports(
                'cleaver_saint'
            )
        );
    }

    public function testDoesNotSupportUnknownClassIdentifiers(): void
    {
        self::assertFalse(
            CharacterClass::supports(
                'sandwich-knight'
            )
        );
    }

    #[DataProvider('hitDieProvider')]
    public function testReturnsTheCorrectHitDie(
        string $className,
        int $expectedHitDie
    ): void {
        self::assertSame(
            $expectedHitDie,
            CharacterClass::fromString(
                $className
            )->hitDie()
        );
    }


    #[DataProvider('savingThrowProficiencyProvider')]
        public function testReturnsTheCorrectSavingThrowProficiencies(
            string $className,
            array $expectedProficiencies
        ): void {
            self::assertSame(
                $expectedProficiencies,
                CharacterClass::fromString(
                    $className
                )->savingThrowProficiencies()
            );
        }
        
        /**
         * @return array<string,array{
         *     0:string,
         *     1:array<int,string>
         * }>
         */
        public static function savingThrowProficiencyProvider(): array
        {
            return [
                'grocer' => [
                    'grocer',
                    [
                        'wisdom',
                        'charisma',
                    ],
                ],
                'cleaver saint' => [
                    'cleaver-saint',
                    [
                        'wisdom',
                        'charisma',
                    ],
                ],
                'artificer' => [
                    'artificer',
                    [
                        'constitution',
                        'intelligence',
                    ],
                ],
                'barbarian' => [
                    'barbarian',
                    [
                        'strength',
                        'constitution',
                    ],
                ],
                'bard' => [
                    'bard',
                    [
                        'dexterity',
                        'charisma',
                    ],
                ],
                'cleric' => [
                    'cleric',
                    [
                        'wisdom',
                        'charisma',
                    ],
                ],
                'druid' => [
                    'druid',
                    [
                        'intelligence',
                        'wisdom',
                    ],
                ],
                'fighter' => [
                    'fighter',
                    [
                        'strength',
                        'constitution',
                    ],
                ],
                'monk' => [
                    'monk',
                    [
                        'strength',
                        'dexterity',
                    ],
                ],
                'paladin' => [
                    'paladin',
                    [
                        'wisdom',
                        'charisma',
                    ],
                ],
                'ranger' => [
                    'ranger',
                    [
                        'strength',
                        'dexterity',
                    ],
                ],
                'rogue' => [
                    'rogue',
                    [
                        'dexterity',
                        'intelligence',
                    ],
                ],
                'sorcerer' => [
                    'sorcerer',
                    [
                        'constitution',
                        'charisma',
                    ],
                ],
                'warlock' => [
                    'warlock',
                    [
                        'wisdom',
                        'charisma',
                    ],
                ],
                'wizard' => [
                    'wizard',
                    [
                        'intelligence',
                        'wisdom',
                    ],
                ],
            ];
        }
        
        #[DataProvider('savingThrowProficiencyProvider')]
        public function testEveryClassHasExactlyTwoSavingThrowProficiencies(
            string $className,
            array $expectedProficiencies
        ): void {
            unset($expectedProficiencies);
        
            self::assertCount(
                2,
                CharacterClass::fromString(
                    $className
                )->savingThrowProficiencies()
            );
        }
        
        #[DataProvider('proficientSavingThrowProvider')]
        public function testRecognisesSavingThrowProficiency(
            string $className,
            string $ability
        ): void {
            self::assertTrue(
                CharacterClass::fromString(
                    $className
                )->isProficientInSavingThrow(
                    $ability
                )
            );
        }
        
        /**
         * @return array<string,array{string,string}>
         */
        public static function proficientSavingThrowProvider(): array
        {
            return [
                'fighter strength' => [
                    'fighter',
                    'strength',
                ],
                'fighter constitution' => [
                    'fighter',
                    'constitution',
                ],
                'wizard intelligence' => [
                    'wizard',
                    'intelligence',
                ],
                'wizard wisdom' => [
                    'wizard',
                    'wisdom',
                ],
                'paladin wisdom' => [
                    'paladin',
                    'wisdom',
                ],
                'paladin charisma' => [
                    'paladin',
                    'charisma',
                ],
                'grocer wisdom' => [
                    'grocer',
                    'wisdom',
                ],
                'grocer charisma' => [
                    'grocer',
                    'charisma',
                ],
            ];
        }
        
        public function testSavingThrowProficiencyCheckNormalisesCase(): void
        {
            $class = CharacterClass::fromString(
                'fighter'
            );
        
            self::assertTrue(
                $class->isProficientInSavingThrow(
                    'STRENGTH'
                )
            );
        
            self::assertTrue(
                $class->isProficientInSavingThrow(
                    'Constitution'
                )
            );
        }
        
        public function testSavingThrowProficiencyCheckTrimsWhitespace(): void
        {
            self::assertTrue(
                CharacterClass::fromString(
                    'wizard'
                )->isProficientInSavingThrow(
                    '  wisdom  '
                )
            );
        }
        
        #[DataProvider('nonProficientSavingThrowProvider')]
        public function testRejectsNonProficientSavingThrows(
            string $className,
            string $ability
        ): void {
            self::assertFalse(
                CharacterClass::fromString(
                    $className
                )->isProficientInSavingThrow(
                    $ability
                )
            );
        }
        
        /**
         * @return array<string,array{string,string}>
         */
        public static function nonProficientSavingThrowProvider(): array
        {
            return [
                'fighter dexterity' => [
                    'fighter',
                    'dexterity',
                ],
                'fighter wisdom' => [
                    'fighter',
                    'wisdom',
                ],
                'wizard strength' => [
                    'wizard',
                    'strength',
                ],
                'wizard charisma' => [
                    'wizard',
                    'charisma',
                ],
                'paladin strength' => [
                    'paladin',
                    'strength',
                ],
                'grocer intelligence' => [
                    'grocer',
                    'intelligence',
                ],
            ];
        }
        
        public function testSavingThrowProficienciesUseCanonicalAbilityIdentifiers(): void
        {
            $supportedAbilities = [
                'strength',
                'dexterity',
                'constitution',
                'intelligence',
                'wisdom',
                'charisma',
            ];
        
            foreach (CharacterClass::all() as $class) {
                foreach (
                    $class->savingThrowProficiencies()
                    as $ability
                ) {
                    self::assertContains(
                        $ability,
                        $supportedAbilities
                    );
                }
            }
        }
        
        public function testSavingThrowProficienciesContainNoDuplicates(): void
        {
            foreach (CharacterClass::all() as $class) {
                $proficiencies =
                    $class->savingThrowProficiencies();
        
                self::assertSame(
                    $proficiencies,
                    array_values(
                        array_unique($proficiencies)
                    )
                );
            }
        }

    /**
     * @return array<string,array{string,int}>
     */
    public static function hitDieProvider(): array
    {
        return [
            'grocer' => ['grocer', 8],
            'cleaver saint' => ['cleaver-saint', 10],
            'artificer' => ['artificer', 8],
            'barbarian' => ['barbarian', 12],
            'bard' => ['bard', 8],
            'cleric' => ['cleric', 8],
            'druid' => ['druid', 8],
            'fighter' => ['fighter', 10],
            'monk' => ['monk', 8],
            'paladin' => ['paladin', 10],
            'ranger' => ['ranger', 10],
            'rogue' => ['rogue', 8],
            'sorcerer' => ['sorcerer', 6],
            'warlock' => ['warlock', 8],
            'wizard' => ['wizard', 6],
        ];
    }

    #[DataProvider('startingHitPointsProvider')]
    public function testCalculatesStartingHitPoints(
        string $className,
        int $constitutionScore,
        int $expectedHitPoints
    ): void {
        $class = CharacterClass::fromString(
            $className
        );

        $constitution = AbilityScore::fromInt(
            $constitutionScore
        );

        self::assertSame(
            $expectedHitPoints,
            $class->startingHitPoints(
                $constitution
            )
        );
    }

    /**
     * @return array<string,array{string,int,int}>
     */
    public static function startingHitPointsProvider(): array
    {
        return [
            'grocer with +2 constitution' => [
                'grocer',
                14,
                10,
            ],
            'cleaver saint with +2 constitution' => [
                'cleaver-saint',
                14,
                12,
            ],
            'barbarian with +3 constitution' => [
                'barbarian',
                16,
                15,
            ],
            'fighter with +2 constitution' => [
                'fighter',
                14,
                12,
            ],
            'cleric with +1 constitution' => [
                'cleric',
                12,
                9,
            ],
            'wizard with no modifier' => [
                'wizard',
                10,
                6,
            ],
            'sorcerer with -1 constitution' => [
                'sorcerer',
                8,
                5,
            ],
            'wizard with very low constitution' => [
                'wizard',
                1,
                1,
            ],
        ];
    }

    #[DataProvider('supportedClassProvider')]
    public function testSupportsEveryRegisteredClass(
        string $className
    ): void {
        self::assertTrue(
            CharacterClass::supports(
                $className
            )
        );
    }

    /**
     * @return array<string,array{string}>
     */
    public static function supportedClassProvider(): array
    {
        return [
            'grocer' => ['grocer'],
            'cleaver saint' => ['cleaver-saint'],
            'artificer' => ['artificer'],
            'barbarian' => ['barbarian'],
            'bard' => ['bard'],
            'cleric' => ['cleric'],
            'druid' => ['druid'],
            'fighter' => ['fighter'],
            'monk' => ['monk'],
            'paladin' => ['paladin'],
            'ranger' => ['ranger'],
            'rogue' => ['rogue'],
            'sorcerer' => ['sorcerer'],
            'warlock' => ['warlock'],
            'wizard' => ['wizard'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testReturnsTheCorrectLabel(
        string $className,
        string $expectedLabel
    ): void {
        self::assertSame(
            $expectedLabel,
            CharacterClass::fromString(
                $className
            )->label()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function labelProvider(): array
    {
        return [
            'grocer' => [
                'grocer',
                'Grocer',
            ],
            'cleaver saint' => [
                'cleaver-saint',
                'Cleaver Saint',
            ],
            'artificer' => [
                'artificer',
                'Artificer',
            ],
            'barbarian' => [
                'barbarian',
                'Barbarian',
            ],
            'bard' => [
                'bard',
                'Bard',
            ],
            'cleric' => [
                'cleric',
                'Cleric',
            ],
            'druid' => [
                'druid',
                'Druid',
            ],
            'fighter' => [
                'fighter',
                'Fighter',
            ],
            'monk' => [
                'monk',
                'Monk',
            ],
            'paladin' => [
                'paladin',
                'Paladin',
            ],
            'ranger' => [
                'ranger',
                'Ranger',
            ],
            'rogue' => [
                'rogue',
                'Rogue',
            ],
            'sorcerer' => [
                'sorcerer',
                'Sorcerer',
            ],
            'warlock' => [
                'warlock',
                'Warlock',
            ],
            'wizard' => [
                'wizard',
                'Wizard',
            ],
        ];
    }

    public function testReturnsEverySupportedClass(): void
    {
        $classes = CharacterClass::all();

        self::assertCount(
            15,
            $classes
        );

        self::assertContainsOnlyInstancesOf(
            CharacterClass::class,
            $classes
        );
    }

    public function testAllReturnsEveryExpectedClassIdentifier(): void
    {
        $values = array_map(
            static fn (
                CharacterClass $class
            ): string => $class->value(),
            CharacterClass::all()
        );

        self::assertSame(
            [
                'grocer',
                'cleaver-saint',
                'artificer',
                'barbarian',
                'bard',
                'cleric',
                'druid',
                'fighter',
                'monk',
                'paladin',
                'ranger',
                'rogue',
                'sorcerer',
                'warlock',
                'wizard',
            ],
            $values
        );
    }
}
