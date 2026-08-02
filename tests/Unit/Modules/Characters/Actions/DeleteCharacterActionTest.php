<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use PHPUnit\Framework\TestCase;

final class DeleteCharacterActionTest extends TestCase
{
    public function testDeletesTheCharacter(): void
    {
        $repository = new DeleteRepositorySpy();

        $action = new DeleteCharacterAction(
            $repository
        );

        $id = CharacterId::generate();

        $action->handle($id);

        self::assertTrue(
            $repository->deletedId->equals($id)
        );
    }

    public function testDeleteIsCalledExactlyOnce(): void
    {
        $repository = new DeleteRepositorySpy();

        $action = new DeleteCharacterAction(
            $repository
        );

        $action->handle(
            CharacterId::generate()
        );

        self::assertSame(
            1,
            $repository->deleteCalls
        );
    }
}

final class DeleteRepositorySpy implements CharacterRepositoryInterface
{
    public ?CharacterId $deletedId = null;

    public int $deleteCalls = 0;

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
    }

    public function delete(
        CharacterId $id
    ): void {
        $this->deleteCalls++;

        $this->deletedId = $id;
    }
}
