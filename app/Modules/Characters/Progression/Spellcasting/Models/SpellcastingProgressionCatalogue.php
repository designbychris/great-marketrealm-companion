<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts\SpellcastingProgressionDefinitionInterface;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\SorcererSpellcastingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\WizardSpellcastingProgression;

final class SpellcastingProgressionCatalogue
{
    /** @var array<int,SpellcastingProgressionDefinitionInterface> */
    private array $definitions;

    /**
     * @param array<int,SpellcastingProgressionDefinitionInterface>|null $definitions
     */
    public function __construct(
        ?array $definitions = null
    ) {
        $this->definitions =
            $definitions
            ?? [
                new WizardSpellcastingProgression(),
                new SorcererSpellcastingProgression(),
            ];
    }

    public function supports(
        CharacterClass $class
    ): bool {
        foreach ($this->definitions as $definition) {
            if ($definition->supports($class)) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string,mixed>|null */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): ?array {
        foreach ($this->definitions as $definition) {
            if ($definition->supports($class)) {
                return $definition->forLevel(
                    $class,
                    $level
                );
            }
        }

        return null;
    }
}
