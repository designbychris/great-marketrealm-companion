<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use PHPUnit\Framework\TestCase;

final class UpdateCharacterActionTest extends TestCase
{
    public function testPersistsTheCharacter(): void
    {
        $repository = new CreateCharacterRepositorySpy();

        $action = new UpdateCharacterAction(
            $repository
        );

        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::average()
        );

        $returned = $action->handle(
            $character
        );

        self::assertSame(
            $character,
            $returned
        );

        self::assertSame(
            $character,
            $repository->savedCharacter
        );
    }

    public function testCallsSaveExactlyOnce(): void
    {
        $repository = new CreateCharacterRepositorySpy();

        $action = new CreateCharacterAction(
            $repository
        );

        $action->handle(
            $this->character()
        );

        self::assertSame(
            1,
            $repository->saveCalls
        );
    }

    private function character(): Character
    {
        return Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::average()
        );
    }
}

final class CreateCharacterRepositorySpy implements CharacterRepositoryInterface
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
