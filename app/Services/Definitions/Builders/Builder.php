<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions\Builders;

use GreatMarketrealmCompanion\Definitions\Definition;
use GreatMarketrealmCompanion\Services\Definitions\Scriptorium;

defined('ABSPATH') || exit;

/**
 * Base Definition Builder.
 *
 * Provides shared authoring methods for all definition builders.
 *
 * @since 0.3.0
 */
abstract class Builder
{
    /**
     * Create the builder.
     */
    public function __construct(
        protected Definition $definition,
        protected Scriptorium $scriptorium
    ) {
    }

    /**
     * Set the definition description.
     */
    public function description(string $description): static
    {
        $this->definition->description($description);

        return $this;
    }

    /**
     * Set the definition icon.
     */
    public function icon(string $icon): static
    {
        $this->definition->icon($icon);

        return $this;
    }

    /**
     * Set the definition portrait.
     */
    public function portrait(string $portrait): static
    {
        $this->definition->portrait($portrait);

        return $this;
    }

    /**
     * Set the definition colour.
     */
    public function colour(string $colour): static
    {
        $this->definition->colour($colour);

        return $this;
    }

    /**
     * Return the definition being authored.
     */
    public function definition(): Definition
    {
        return $this->definition;
    }

    /**
     * Finish authoring the current definition and return
     * control to the Scriptorium.
     */
    public function done(): Scriptorium
    {
        return $this->scriptorium;
    }
}
