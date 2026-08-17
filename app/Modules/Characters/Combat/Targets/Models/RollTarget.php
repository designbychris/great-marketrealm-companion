<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Models;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Immutable reference to the intended recipient of a Diceworks result.
 *
 * A target may be descriptive but unresolved. Mutation-capable services must
 * require a resolved target before changing vitality or encounter state.
 */
final class RollTarget
{
    private function __construct(
        private string $kind,
        private ?string $id,
        private string $label,
        private bool $resolved
    ) {
    }

    public static function resolved(
        string $kind,
        string $id,
        string $label
    ): self {
        self::guardKind($kind);

        $id = trim($id);
        $label = trim($label);

        if ($id === '' || $label === '') {
            throw new InvalidArgumentException(
                'A resolved roll target requires an id and label.'
            );
        }

        return new self(
            $kind,
            $id,
            $label,
            true
        );
    }

    public static function reference(
        string $kind,
        string $label = ''
    ): self {
        self::guardKind($kind);

        $label = trim($label);

        return new self(
            $kind,
            null,
            $label !== ''
                ? $label
                : RollTargetKind::label($kind),
            false
        );
    }

    public function kind(): string
    {
        return $this->kind;
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    /** @return array{kind:string,id:?string,label:string,resolved:bool} */
    public function toArray(): array
    {
        return [
            'kind' => $this->kind,
            'id' => $this->id,
            'label' => $this->label,
            'resolved' => $this->resolved,
        ];
    }

    private static function guardKind(string $kind): void
    {
        if (! RollTargetKind::valid($kind)) {
            throw new InvalidArgumentException(
                'Unknown Diceworks roll target kind.'
            );
        }
    }
}
