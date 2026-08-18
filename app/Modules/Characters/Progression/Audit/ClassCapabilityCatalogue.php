<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Audit;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;

defined('ABSPATH') || exit;

/**
 * Audits the progression machinery GMRC currently provides for each Calling.
 *
 * The catalogue deliberately asks the existing progression catalogues rather
 * than duplicating a hard-coded Wizard list. As future class definitions are
 * registered, the audit changes with the application.
 */
final class ClassCapabilityCatalogue
{
    public function __construct(
        private ?ClassProgressionCatalogue $advancement = null,
        private ?SpellcastingProgressionCatalogue $spellcasting = null,
        private ?PathProgressionCatalogue $paths = null
    ) {
        $this->advancement ??=
            new ClassProgressionCatalogue();

        $this->spellcasting ??=
            new SpellcastingProgressionCatalogue();

        $this->paths ??=
            new PathProgressionCatalogue();
    }

    public function forClass(
        CharacterClass $class
    ): ClassCapabilityProfile {
        $advancement = $this->advancement->forLevel(
            $class,
            2
        );

        return new ClassCapabilityProfile(
            class: $class,
            advancementStatus:
                (string) (
                    $advancement['catalogue_status']
                    ?? 'registered'
                ),
            spellcasting:
                $this->spellcasting->supports($class),
            callingPath:
                $this->paths->forClass($class) !== null
        );
    }

    /**
     * @return array<int,ClassCapabilityProfile>
     */
    public function all(): array
    {
        return array_map(
            fn (
                CharacterClass $class
            ): ClassCapabilityProfile =>
                $this->forClass($class),
            CharacterClass::all()
        );
    }

    /**
     * @return array<int,ClassCapabilityProfile>
     */
    public function specialist(): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (
                    ClassCapabilityProfile $profile
                ): bool =>
                    $profile->implementationState()
                    === ClassCapabilityProfile::SPECIALIST
            )
        );
    }

    /**
     * @return array<int,ClassCapabilityProfile>
     */
    public function foundation(): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (
                    ClassCapabilityProfile $profile
                ): bool =>
                    $profile->implementationState()
                    === ClassCapabilityProfile::FOUNDATION
            )
        );
    }
}
