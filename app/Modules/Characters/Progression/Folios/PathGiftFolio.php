<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;

defined('ABSPATH') || exit;

final class PathGiftFolio
{
    public function __construct(
        private ?PathGiftCatalogue $gifts = null,
        private ?PathProgressionCatalogue $paths = null
    ) {
        $this->gifts ??= new PathGiftCatalogue();
        $this->paths ??= new PathProgressionCatalogue();
    }

    /** @param array<string,array<int,string>> $choices */
    public function build(
        Character $character,
        int $targetLevel,
        array $choices = []
    ): ?AdvancementFolio {
        $pathKey = $character->callingPath()->value();

        if ($pathKey === '') {
            $path = $this->paths->forClass($character->characterClass());

            if (! is_array($path)) {
                return null;
            }

            $selectionLevel = (int) ($path['selection_level'] ?? 0);

            if ($targetLevel < $selectionLevel) {
                return null;
            }

            $choiceKey = (string) ($path['choice_key'] ?? '');

            $pathKey = sanitize_key(
                (string) ($choices[$choiceKey][0] ?? '')
            );

            if ($pathKey === '') {
                /*
                 * The Path Folio owns the actual subclass choice. Gift
                 * inspection begins after a supported Path has been selected,
                 * so this specialist folio never duplicates that decision.
                 */
                return null;
            }
        }

        if (! $this->gifts->supports($pathKey)) {
            return null;
        }

        $unlocked = $this->gifts->unlocked(
            $pathKey,
            $targetLevel,
            $character->pathGifts()
        );

        if ($unlocked === []) {
            return null;
        }

        $giftKeys = array_values(array_map(
            static fn (array $gift): string => (string) ($gift['key'] ?? ''),
            $unlocked
        ));

        $catchUp = false;

        foreach ($unlocked as $gift) {
            if ((int) ($gift['level'] ?? 0) <= $character->level()->value()) {
                $catchUp = true;
                break;
            }
        }

        return new AdvancementFolio(
            'path-gifts',
            'Gifts of the Path',
            sprintf(
                '%d %s %s ready to be entered into the Guild Record.',
                count($unlocked),
                count($unlocked) === 1 ? 'Path gift' : 'Path gifts',
                count($unlocked) === 1 ? 'is' : 'are'
            ),
            FolioStatus::READY,
            false,
            [
                'path' => $pathKey,
                'path_label' => $this->gifts->pathLabel($pathKey),
                'target_level' => $targetLevel,
                'gift_count' => count($unlocked),
                'gift_keys' => $giftKeys,
                'catch_up' => $catchUp,
                'automatic' => true,
                'gifts' => $unlocked,
            ]
        );
    }
}
