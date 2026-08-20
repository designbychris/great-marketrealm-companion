<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\ShelfmancyGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\FighterMartialPathGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\BarbarianPrimalPathGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\RogueArchetypeGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\MonkWayGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\PaladinSacredOathGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\WarlockPatronGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\RangerPathGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\DruidCircleGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\ClericDomainGiftProgression;

defined('ABSPATH') || exit;

final class PathGiftCatalogue
{
    /** @var array<int,PathGiftProgressionDefinitionInterface> */
    private array $definitions;

    /** @param array<int,PathGiftProgressionDefinitionInterface>|null $definitions */
    public function __construct(?array $definitions = null)
    {
        $this->definitions = $definitions ?? array_merge(
            [
                new ShelfmancyGiftProgression(),
            ],
            FighterMartialPathGiftProgression::allDefinitions(),
            BarbarianPrimalPathGiftProgression::allDefinitions(),
            RogueArchetypeGiftProgression::allDefinitions(),
            MonkWayGiftProgression::allDefinitions(),
            PaladinSacredOathGiftProgression::allDefinitions(),
            WarlockPatronGiftProgression::allDefinitions(),
            RangerPathGiftProgression::allDefinitions(),
            DruidCircleGiftProgression::allDefinitions(),
            ClericDomainGiftProgression::allDefinitions()
        );
    }

    public function supports(string $pathKey): bool
    {
        return $this->definition($pathKey) !== null;
    }

    public function pathLabel(string $pathKey): string
    {
        $definition = $this->definition($pathKey);

        return $definition !== null
            ? $definition->pathLabel()
            : ucwords(str_replace('-', ' ', sanitize_key($pathKey)));
    }

    /** @return array<int,array<string,mixed>> */
    public function all(string $pathKey): array
    {
        $definition = $this->definition($pathKey);

        return $definition !== null
            ? $definition->gifts()
            : [];
    }

    /** @return array<int,array<string,mixed>> */
    public function unlocked(
        string $pathKey,
        int $targetLevel,
        PathGifts $known
    ): array {
        return array_values(array_filter(
            $this->all($pathKey),
            static fn (array $gift): bool =>
                (int) ($gift['level'] ?? 0) <= $targetLevel
                && ! $known->has((string) ($gift['key'] ?? ''))
        ));
    }

    private function definition(
        string $pathKey
    ): ?PathGiftProgressionDefinitionInterface {
        foreach ($this->definitions as $definition) {
            if ($definition->supports($pathKey)) {
                return $definition;
            }
        }

        return null;
    }
}
