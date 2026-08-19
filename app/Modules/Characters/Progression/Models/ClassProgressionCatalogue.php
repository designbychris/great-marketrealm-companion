<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\RegisteredCallingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\FighterProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\BarbarianProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\RogueProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\MonkProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\PaladinProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\WarlockProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\SorcererProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\RangerProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\WizardProgression;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class ClassProgressionCatalogue
{
    /**
     * @var array<int,ClassProgressionDefinitionInterface>
     */
    private array $definitions;

    /**
     * @param array<int,ClassProgressionDefinitionInterface>|null $definitions
     */
    public function __construct(
        ?array $definitions = null
    ) {
        $this->definitions = $definitions
            ?? [
                new WizardProgression(),
                new FighterProgression(),
                new BarbarianProgression(),
                new RogueProgression(),
                new MonkProgression(),
                new PaladinProgression(),
                new WarlockProgression(),
                new SorcererProgression(),
                new RangerProgression(),
                new RegisteredCallingProgression(),
            ];
    }

    /** @return array<int,string> */
    public function classes(): array
    {
        return CharacterClass::identifiers();
    }

    public function supports(
        CharacterClass $class
    ): bool {
        return in_array(
            $class->value(),
            $this->classes(),
            true
        );
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'The class is not registered for advancement.'
            );
        }

        foreach ($this->definitions as $definition) {
            if ($definition->supports($class)) {
                return $definition->forLevel(
                    $class,
                    $level
                );
            }
        }

        throw new InvalidArgumentException(
            'No Calling progression definition is registered.'
        );
    }
}
