<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Actions;

use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;
use PHPUnit\Framework\TestCase;

final class DeleteCharacterPortraitCleanupTest extends TestCase
{
    public function testPortraitIsRemovedBeforeCharacterRecord(): void
    {
        $events = [];

        $characters = new class($events)
            implements CharacterRepositoryInterface {
            public function __construct(
                private array &$events
            ) {
            }

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
                $this->events[] = 'character';
            }
        };

        $portraits = new class($events)
            implements CharacterPortraitRepositoryInterface {
            public function __construct(
                private array &$events
            ) {
            }

            public function find(
                CharacterId $characterId
            ): ?CharacterPortrait {
                return null;
            }

            public function save(
                CharacterId $characterId,
                CharacterPortrait $portrait
            ): void {
            }

            public function delete(
                CharacterId $characterId
            ): void {
                $this->events[] = 'portrait';
            }
        };

        $action = new DeleteCharacterAction(
            $characters,
            $portraits
        );

        $action->handle(
            CharacterId::generate()
        );

        self::assertSame(
            [
                'portrait',
                'character',
            ],
            $events
        );
    }
}
