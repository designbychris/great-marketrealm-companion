<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Application;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Parties\Actions\AddPartyMemberAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\ChangePartyMemberRoleAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\CreatePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\DeletePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RemovePartyMemberAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RenamePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Exceptions\PartyCharacterUnavailable;
use GreatMarketrealmCompanion\Modules\Parties\Exceptions\PartyNotFound;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use PHPUnit\Framework\TestCase;

final class PartyApplicationLayerTest extends TestCase
{
    public function testCreatesAndListsFellowshipForOwner(): void
    {
        $parties = new InMemoryPartyRepository();
        $action = new CreatePartyAction($parties);
        $owner = PartyOwnerId::fromInt(42);

        $party = $action->handle(
            PartyName::fromString('The Pantry Fellowship'),
            $owner
        );

        self::assertSame(
            'The Pantry Fellowship',
            $party->name()->value()
        );
        self::assertSame(42, $party->ownerId()->value());

        $found = (new PartyFinder($parties))->all($owner);

        self::assertCount(1, $found);
        self::assertTrue(
            $found[0]->id()->equals($party->id())
        );
    }

    public function testFindFailsClosedForWrongOwner(): void
    {
        $parties = new InMemoryPartyRepository();
        $party = $this->savedParty($parties, 42);

        $this->expectException(
            PartyNotFound::class
        );

        (new PartyFinder($parties))->find(
            $party->id(),
            PartyOwnerId::fromInt(99)
        );
    }

    public function testOwnedCharacterCanJoinFellowship(): void
    {
        $parties = new InMemoryPartyRepository();
        $characters = new InMemoryCharacterRepository();
        $party = $this->savedParty($parties, 42);
        $character = $this->character();
        $characters->save($character);

        $updated = (new AddPartyMemberAction(
            $parties,
            $characters,
            new PartyFinder($parties)
        ))->handle(
            $party->id(),
            PartyOwnerId::fromInt(42),
            $character->id()
        );

        self::assertTrue(
            $updated->hasMember($character->id())
        );
        self::assertSame(
            'member',
            $updated->membership($character->id())
                ?->role()
                ->value()
        );
    }

    public function testUnavailableCharacterCannotBeAddedByUlid(): void
    {
        $parties = new InMemoryPartyRepository();
        $party = $this->savedParty($parties, 42);

        $this->expectException(
            PartyCharacterUnavailable::class
        );

        (new AddPartyMemberAction(
            $parties,
            new InMemoryCharacterRepository(),
            new PartyFinder($parties)
        ))->handle(
            $party->id(),
            PartyOwnerId::fromInt(42),
            CharacterId::generate()
        );
    }

    public function testMemberCanJoinDirectlyAsLeader(): void
    {
        $parties = new InMemoryPartyRepository();
        $characters = new InMemoryCharacterRepository();
        $party = $this->savedParty($parties, 42);
        $character = $this->character();
        $characters->save($character);

        $updated = (new AddPartyMemberAction(
            $parties,
            $characters,
            new PartyFinder($parties)
        ))->handle(
            $party->id(),
            PartyOwnerId::fromInt(42),
            $character->id(),
            PartyMembershipRole::leader()
        );

        self::assertTrue(
            $updated->membership($character->id())
                ?->role()
                ->isLeader()
        );
    }

    public function testMembershipCanBeRemovedWithoutDeletingCharacter(): void
    {
        $parties = new InMemoryPartyRepository();
        $characters = new InMemoryCharacterRepository();
        $party = $this->savedParty($parties, 42);
        $character = $this->character();
        $characters->save($character);
        $party->addMember($character->id());
        $parties->save($party);

        $updated = (new RemovePartyMemberAction(
            $parties,
            new PartyFinder($parties)
        ))->handle(
            $party->id(),
            PartyOwnerId::fromInt(42),
            $character->id()
        );

        self::assertFalse(
            $updated->hasMember($character->id())
        );
        self::assertInstanceOf(
            Character::class,
            $characters->find($character->id())
        );
    }

    public function testMembershipRoleCanBeChanged(): void
    {
        $parties = new InMemoryPartyRepository();
        $party = $this->savedParty($parties, 42);
        $characterId = CharacterId::generate();
        $party->addMember($characterId);
        $parties->save($party);

        $updated = (new ChangePartyMemberRoleAction(
            $parties,
            new PartyFinder($parties)
        ))->handle(
            $party->id(),
            PartyOwnerId::fromInt(42),
            $characterId,
            PartyMembershipRole::leader()
        );

        self::assertTrue(
            $updated->membership($characterId)
                ?->role()
                ->isLeader()
        );
    }

    public function testFellowshipCanBeRenamedThroughApplicationLayer(): void
    {
        $parties = new InMemoryPartyRepository();
        $party = $this->savedParty($parties, 42);

        $updated = (new RenamePartyAction(
            $parties,
            new PartyFinder($parties)
        ))->handle(
            $party->id(),
            PartyOwnerId::fromInt(42),
            PartyName::fromString('The Heroic Trolley')
        );

        self::assertSame(
            'The Heroic Trolley',
            $updated->name()->value()
        );
    }

    public function testDeleteRequiresOwnerScopedPartyResolution(): void
    {
        $parties = new InMemoryPartyRepository();
        $party = $this->savedParty($parties, 42);
        $action = new DeletePartyAction(
            $parties,
            new PartyFinder($parties)
        );

        try {
            $action->handle(
                $party->id(),
                PartyOwnerId::fromInt(99)
            );
            self::fail('Wrong owner must not delete Fellowship.');
        } catch (PartyNotFound) {
            self::assertNotNull(
                $parties->findForOwner(
                    $party->id(),
                    PartyOwnerId::fromInt(42)
                )
            );
        }

        $action->handle(
            $party->id(),
            PartyOwnerId::fromInt(42)
        );

        self::assertNull(
            $parties->findForOwner(
                $party->id(),
                PartyOwnerId::fromInt(42)
            )
        );
    }

    public function testApplicationLayerDoesNotOwnCharacterDeletion(): void
    {
        $root = dirname(__DIR__, 5);

        foreach (glob(
            $root . '/app/Modules/Parties/Actions/*.php'
        ) ?: [] as $file) {
            $source = file_get_contents($file);

            self::assertIsString($source);
            self::assertStringNotContainsString(
                '->delete($characterId',
                $source
            );
            self::assertStringNotContainsString(
                'DeleteCharacterAction',
                $source
            );
        }
    }

    private function savedParty(
        InMemoryPartyRepository $parties,
        int $ownerId
    ): Party {
        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString('The Pantry Fellowship'),
            PartyOwnerId::fromInt($ownerId)
        );

        $parties->save($party);

        return $party;
    }

    private function character(): Character
    {
        return Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Magic'),
            Race::fromString('frostreem'),
            CharacterClass::fromString('wizard'),
            HitPoints::full(8),
            AbilityScores::average()
        );
    }
}

final class InMemoryPartyRepository implements PartyRepositoryInterface
{
    /** @var array<string,Party> */
    private array $parties = [];

    public function allForOwner(PartyOwnerId $ownerId): array
    {
        return array_values(array_filter(
            $this->parties,
            static fn (Party $party): bool =>
                $party->ownerId()->equals($ownerId)
        ));
    }

    public function findForOwner(
        PartyId $id,
        PartyOwnerId $ownerId
    ): ?Party {
        $party = $this->parties[$id->value()] ?? null;

        return $party instanceof Party
            && $party->ownerId()->equals($ownerId)
                ? $party
                : null;
    }

    public function save(Party $party): void
    {
        $this->parties[
            $party->id()->value()
        ] = $party;
    }

    public function delete(
        PartyId $id,
        PartyOwnerId $ownerId
    ): void {
        $party = $this->findForOwner($id, $ownerId);

        if ($party instanceof Party) {
            unset($this->parties[$id->value()]);
        }
    }
}

final class InMemoryCharacterRepository implements CharacterRepositoryInterface
{
    /** @var array<string,Character> */
    private array $characters = [];

    public function all(): array
    {
        return array_values($this->characters);
    }

    public function find(CharacterId $id): ?Character
    {
        return $this->characters[$id->value()] ?? null;
    }

    public function save(Character $character): void
    {
        $this->characters[
            $character->id()->value()
        ] = $character;
    }

    public function delete(CharacterId $id): void
    {
        unset($this->characters[$id->value()]);
    }
}
