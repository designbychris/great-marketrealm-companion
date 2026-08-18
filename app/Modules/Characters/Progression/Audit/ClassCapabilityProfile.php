<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Audit;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;

defined('ABSPATH') || exit;

/**
 * Read-only snapshot of the progression capabilities currently implemented
 * for one registered Calling.
 *
 * This profile describes GMRC implementation state. It does not invent or
 * infer tabletop rules that are not yet represented by the application.
 */
final class ClassCapabilityProfile
{
    public const SPECIALIST = 'specialist';
    public const FOUNDATION = 'foundation';

    public function __construct(
        private CharacterClass $class,
        private string $advancementStatus,
        private bool $spellcasting,
        private bool $callingPath
    ) {
    }

    public function class(): CharacterClass
    {
        return $this->class;
    }

    public function advancementStatus(): string
    {
        return $this->advancementStatus;
    }

    public function hasSpecialistAdvancement(): bool
    {
        return $this->advancementStatus !== 'registered';
    }

    public function hasSpellcastingProgression(): bool
    {
        return $this->spellcasting;
    }

    public function hasCallingPathProgression(): bool
    {
        return $this->callingPath;
    }

    public function implementationState(): string
    {
        return (
            $this->hasSpecialistAdvancement()
            || $this->spellcasting
            || $this->callingPath
        )
            ? self::SPECIALIST
            : self::FOUNDATION;
    }

    /**
     * @return array{
     *     class:string,
     *     label:string,
     *     advancement:string,
     *     spellcasting:bool,
     *     calling_path:bool,
     *     implementation_state:string
     * }
     */
    public function toArray(): array
    {
        return [
            'class' => $this->class->value(),
            'label' => $this->class->label(),
            'advancement' => $this->advancementStatus,
            'spellcasting' => $this->spellcasting,
            'calling_path' => $this->callingPath,
            'implementation_state' =>
                $this->implementationState(),
        ];
    }
}
