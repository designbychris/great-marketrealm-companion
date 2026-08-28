<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Characters\Tokens\Repositories\CharacterTokenRepository;
use GreatMarketrealmCompanion\Modules\Characters\Tokens\Services\CharacterTokenPresenter;

final class TabletopCharacterBridge
{
    public function __construct(
        private CharacterRepository $characters,
        private PortraitRenderer $portraits
    ) {}

    /** @return array<int,array<string,mixed>> */
    public function ownedCharacters(mixed $value, int $userId): array
    {
        if ($userId < 1) {
            return [];
        }

        return array_map(
            fn (Character $character): array => $this->project($character, $userId),
            $this->characters->allForOwner($userId)
        );
    }

    /** @return array<string,mixed>|null */
    public function ownedCharacter(mixed $value, int $userId, string $characterId): ?array
    {
        $characterId = trim($characterId);
        if ($userId < 1 || $characterId === '') {
            return null;
        }

        try {
            $character = $this->characters->findForOwner(
                CharacterId::fromString($characterId),
                $userId
            );
        } catch (\Throwable) {
            return null;
        }

        return $character instanceof Character
            ? $this->project($character, $userId)
            : null;
    }

    /** @return array<string,mixed> */
    private function project(Character $character, int $ownerId): array
    {
        $portrait = $this->portraits->forCharacterForOwner($character, $ownerId);
        $token = (new CharacterTokenPresenter())->present(
            (new CharacterTokenRepository())->findForOwner($character->id(), $ownerId),
            $portrait
        );

        $imageUrl = is_string($token['custom_url'] ?? null)
            ? (string) $token['custom_url']
            : (is_string($token['portrait_url'] ?? null)
                ? (string) $token['portrait_url']
                : '');

        if ($imageUrl === '' && is_string($token['portrait_svg'] ?? null) && $token['portrait_svg'] !== '') {
            $imageUrl = 'data:image/svg+xml;base64,' . base64_encode((string) $token['portrait_svg']);
        }

        return [
            'id' => $character->id()->value(),
            'name' => $character->name()->value(),
            'race' => $character->race()->label(),
            'class' => $character->characterClass()->label(),
            'level' => $character->level()->value(),
            'token' => [
                'image_url' => $imageUrl,
                'frame' => (string) ($token['frame'] ?? 'guild-brass'),
                'focus_x' => (int) ($token['focus_x'] ?? 50),
                'focus_y' => (int) ($token['focus_y'] ?? 50),
                'zoom' => (int) ($token['zoom'] ?? 100),
                'source' => (string) ($token['source'] ?? 'portrait'),
            ],
        ];
    }
}
