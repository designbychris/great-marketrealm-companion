<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

defined('ABSPATH') || exit;

final class AdvancementFolio
{
    /**
     * @param array<string,mixed> $facts
     * @param array<int,array<string,mixed>> $choices
     * @param array<int,array<string,mixed>> $delegated
     */
    public function __construct(
        private string $key,
        private string $label,
        private string $summary,
        private string $status,
        private bool $requiresChoice,
        private array $facts = [],
        private array $choices = [],
        private array $delegated = []
    ) {
        $this->status = FolioStatus::validate(
            $status
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function requiresChoice(): bool
    {
        return $this->requiresChoice;
    }

    public function isReady(): bool
    {
        return ! $this->requiresChoice;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'summary' => $this->summary,
            'status' => $this->status,
            'requires_choice' =>
                $this->requiresChoice,
            'ready' => $this->isReady(),
            'facts' => $this->facts,
            'choices' => $this->choices,
            'delegated' => $this->delegated,
        ];
    }
}
