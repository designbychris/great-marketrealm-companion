<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Presenters;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyMembership;

defined('ABSPATH') || exit;

/**
 * Presentation bridge for the Fellowship Register.
 *
 * Party membership stores Character references only. This presenter
 * resolves those references at display time and reuses the established
 * Character portrait pipeline.
 */
final class FellowshipPresenter
{
    public function __construct(
        private CharacterRepositoryInterface $characters,
        private PortraitRenderer $portraits
    ) {
    }

    /**
     * @return array{
     *     party:Party,
     *     members:array<int,array{
     *         membership:PartyMembership,
     *         character:?Character,
     *         portrait:?PortraitViewModel,
     *         missing:bool
     *     }>,
     *     available:Character[]
     * }
     */
    public function present(Party $party): array
    {
        $characters = $this->characters->all();
        $byId = [];

        foreach ($characters as $character) {
            if (! $character instanceof Character) {
                continue;
            }

            $byId[
                $character->id()->value()
            ] = $character;
        }

        $resolvedCharacters = [];
        $memberCharacters = [];

        foreach ($party->memberships() as $membership) {
            $character = $byId[
                $membership->characterId()->value()
            ] ?? null;

            if ($character instanceof Character) {
                $resolvedCharacters[] = $character;
                $memberCharacters[
                    $character->id()->value()
                ] = true;
            }
        }

        $portraitModels = $this->portraits
            ->forCharacters($resolvedCharacters);

        $members = [];

        foreach ($party->memberships() as $membership) {
            $characterId = $membership
                ->characterId()
                ->value();

            $character = $byId[$characterId] ?? null;

            $members[] = [
                'membership' => $membership,
                'character' =>
                    $character instanceof Character
                        ? $character
                        : null,
                'portrait' =>
                    $portraitModels[$characterId]
                        ?? null,
                'missing' =>
                    ! $character instanceof Character,
            ];
        }

        $available = array_values(
            array_filter(
                $characters,
                static fn (mixed $character): bool =>
                    $character instanceof Character
                    && ! isset(
                        $memberCharacters[
                            $character->id()->value()
                        ]
                    )
            )
        );

        return [
            'party' => $party,
            'members' => $members,
            'available' => $available,
        ];
    }

    /**
     * @param Party[] $parties
     *
     * @return array<int,array{
     *     party:Party,
     *     members:array<int,array{
     *         membership:PartyMembership,
     *         character:?Character,
     *         portrait:?PortraitViewModel,
     *         missing:bool
     *     }>,
     *     available:Character[]
     * }>
     */
    public function presentMany(array $parties): array
    {
        $presented = [];

        foreach ($parties as $party) {
            if (! $party instanceof Party) {
                continue;
            }

            $presented[] = $this->present($party);
        }

        return $presented;
    }
}
