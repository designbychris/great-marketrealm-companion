<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Models;

defined('ABSPATH') || exit;

/**
 * Immutable spell or class-feature definition used by the Arcane Pantry.
 */
final class ArcaneAbilityDefinition
{
    /**
     * @param string[] $classes
     */
    public function __construct(
        private string $id,
        private string $label,
        private string $kind,
        private array $classes,
        private string $description,
        private string $activation,
        private string $range,
        private string $duration,
        private string $uses,
        private ?string $rollKind = null,
        private ?string $formula = null,
        private ?string $damageType = null,
        private ?string $saveAbility = null,
        private bool $addCastingModifier = false,
        private bool $spellAttack = false
    ) {
    }

    public function id(): string { return $this->id; }
    public function label(): string { return $this->label; }
    public function kind(): string { return $this->kind; }
    public function classes(): array { return $this->classes; }
    public function description(): string { return $this->description; }
    public function activation(): string { return $this->activation; }
    public function range(): string { return $this->range; }
    public function duration(): string { return $this->duration; }
    public function uses(): string { return $this->uses; }
    public function rollKind(): ?string { return $this->rollKind; }
    public function formula(): ?string { return $this->formula; }
    public function damageType(): ?string { return $this->damageType; }
    public function saveAbility(): ?string { return $this->saveAbility; }
    public function addCastingModifier(): bool { return $this->addCastingModifier; }
    public function isSpellAttack(): bool { return $this->spellAttack; }

    public function supportsClass(string $class): bool
    {
        return in_array($class, $this->classes, true);
    }
}
