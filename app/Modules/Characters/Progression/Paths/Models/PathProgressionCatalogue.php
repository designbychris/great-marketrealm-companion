<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\WizardPathProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\FighterPathProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\BarbarianPathProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\RogueArchetypeProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\MonkWayProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\PaladinOathProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\WarlockPatronProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\SorcererOriginProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\RangerPathProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\DruidCircleProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\ClericDomainProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\BardCollegeProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\ArtificerSpecialisationProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\StewardCallingPathProgression;

defined('ABSPATH') || exit;

final class PathProgressionCatalogue
{
    /** @var array<int,PathProgressionDefinitionInterface> */
    private array $definitions;

    /**
     * @param array<int,PathProgressionDefinitionInterface>|null $definitions
     */
    public function __construct(
        ?array $definitions = null
    ) {
        $this->definitions = $definitions
            ?? [
                new WizardPathProgression(),
                new FighterPathProgression(),
                new BarbarianPathProgression(),
                new RogueArchetypeProgression(),
                new MonkWayProgression(),
                new PaladinOathProgression(),
                new WarlockPatronProgression(),
                new SorcererOriginProgression(),
                new RangerPathProgression(),
                new DruidCircleProgression(),
                new ClericDomainProgression(),
                new BardCollegeProgression(),
                new ArtificerSpecialisationProgression(),
                new StewardCallingPathProgression(),
            ];
    }

    /** @return array<string,mixed>|null */
    public function forClass(
        CharacterClass $class
    ): ?array {
        foreach ($this->definitions as $definition) {
            if ($definition->supports($class)) {
                return $definition->definition(
                    $class
                );
            }
        }

        return null;
    }
}
