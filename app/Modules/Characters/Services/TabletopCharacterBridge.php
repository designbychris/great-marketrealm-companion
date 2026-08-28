<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Characters\Combat\Services\AttackPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcanePantryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\CharacterInventoryRepository;
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

        $abilityScores = $character->abilityScores();
        $savingThrows = $character->savingThrows();
        $skills = $character->skills();

        $abilityProjection = [];
        $savingThrowProjection = [];
        foreach ([
            'strength' => 'strength',
            'dexterity' => 'dexterity',
            'constitution' => 'constitution',
            'intelligence' => 'intelligence',
            'wisdom' => 'wisdom',
            'charisma' => 'charisma',
        ] as $key => $method) {
            $score = $abilityScores->{$method}();
            $save = $savingThrows->{$method}();
            $abilityProjection[$key] = [
                'score' => $score->value(),
                'modifier' => $score->modifier(),
            ];
            $savingThrowProjection[$key] = [
                'modifier' => $save->modifier(),
                'proficient' => $save->isProficient(),
            ];
        }

        $skillProjection = [];
        foreach ($skills->all() as $key => $skill) {
            $skillProjection[(string) $key] = [
                'modifier' => $skill->modifier(),
                'proficient' => $skill->isProficient(),
                'expertise' => $skill->hasExpertise(),
            ];
        }

        $catalogue = new ItemCatalogue();
        $inventory = (new CharacterInventoryRepository())->findForOwner(
            $character->id(),
            $ownerId
        );
        $attackProjection = (new AttackPresenter($catalogue))->present(
            $character,
            $inventory
        );

        $arcana = (new ArcanePantryPresenter(
            new ArcaneAbilityCatalogue()
        ))->present($character);
        $spellProjection = array_values(array_filter(
            is_array($arcana['entries'] ?? null) ? $arcana['entries'] : [],
            static fn (mixed $entry): bool => is_array($entry)
                && in_array((string) ($entry['kind'] ?? ''), ['cantrip', 'spell'], true)
        ));

        return [
            'id' => $character->id()->value(),
            'name' => $character->name()->value(),
            'race' => $character->race()->label(),
            'class' => $character->characterClass()->label(),
            'level' => $character->level()->value(),
            'play' => [
                'armour_class' => $character->armourClass()->value(),
                'hit_points' => [
                    'current' => $character->hitPoints()->current(),
                    'maximum' => $character->hitPoints()->maximum(),
                    'temporary' => $character->hitPoints()->temporary(),
                ],
                'speed' => $character->speed()->feet(),
                'initiative' => $character->initiative()->modifier(),
                'proficiency_bonus' => $character->proficiencyBonus()->value(),
                'passive_perception' => $character->passivePerception()->value(),
                'abilities' => $abilityProjection,
                'saving_throws' => $savingThrowProjection,
                'skills' => $skillProjection,
                'attacks' => $attackProjection,
                'spellcasting' => [
                    'ability' => $arcana['casting_ability'] ?? null,
                    'modifier' => (int) ($arcana['casting_modifier'] ?? 0),
                    'spell_attack' => $arcana['spell_attack'] ?? null,
                    'save_dc' => $arcana['save_dc'] ?? null,
                    'slots' => is_array($arcana['slots'] ?? null) ? $arcana['slots'] : [],
                    'spells' => $spellProjection,
                ],
            ],
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
