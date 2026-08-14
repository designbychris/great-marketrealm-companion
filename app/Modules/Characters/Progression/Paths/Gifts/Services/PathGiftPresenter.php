<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;

defined('ABSPATH') || exit;

final class PathGiftPresenter
{
    public function __construct(private ?PathGiftCatalogue $catalogue = null)
    {
        $this->catalogue ??= new PathGiftCatalogue();
    }

    /** @return array<string,mixed> */
    public function present(Character $character): array
    {
        $pathKey = $character->callingPath()->value();

        if ($pathKey === '') {
            return [
                'path' => '',
                'path_label' => '',
                'gifts' => [],
                'count' => 0,
            ];
        }

        $known = $character->pathGifts()->values();

        $gifts = array_values(array_filter(
            $this->catalogue->all($pathKey),
            static fn (array $gift): bool =>
                in_array((string) ($gift['key'] ?? ''), $known, true)
        ));

        return [
            'path' => $pathKey,
            'path_label' => $this->catalogue->pathLabel($pathKey),
            'gifts' => $gifts,
            'count' => count($gifts),
        ];
    }
}
