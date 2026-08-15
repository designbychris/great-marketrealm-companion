<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Services\PathGiftPresenter;

defined('ABSPATH') || exit;

/**
 * Presents the Character's permanent, currently certified progression state.
 *
 * Unlike the Rising Register, this presenter never describes pending
 * advancement paperwork. It only reads state already entered into the
 * Character aggregate and completed Guild certification archive.
 */
final class LivingRegisterPresenter
{
    public function __construct(
        private ?PathGiftPresenter $pathGifts = null
    ) {
        $this->pathGifts ??= new PathGiftPresenter();
    }

    /**
     * @param array<int,array<string,mixed>> $history
     * @return array<string,mixed>
     */
    public function present(Character $character, array $history = []): array
    {
        $spellbook = $character->spellbook();
        $pathState = $this->pathGifts->present($character);
        $latest = $history === [] ? null : $history[array_key_last($history)];

        return [
            'level' => $character->level()->value(),
            'calling' => $character->characterClass()->label(),
            'calling_key' => $character->characterClass()->value(),
            'path' => $character->callingPath()->value(),
            'path_label' => (string) ($pathState['path_label'] ?? ''),
            'has_path' => $character->callingPath()->isChosen(),
            'proficiency' => $character->proficiencyBonus()->signed(),
            'current_hp' => $character->hitPoints()->current(),
            'maximum_hp' => $character->hitPoints()->maximum(),
            'experience' => $character->experience()->value(),
            'spells_known' => count($spellbook->spells()),
            'cantrips_known' => count($spellbook->cantrips()),
            'arcana_known' => count($spellbook->spells()) + count($spellbook->cantrips()),
            'path_gifts' => $pathState['gifts'] ?? [],
            'path_gift_count' => (int) ($pathState['count'] ?? 0),
            'certification_count' => count($history),
            'latest_certification' => is_array($latest) ? $latest : null,
            'is_living_record' => true,
        ];
    }
}
