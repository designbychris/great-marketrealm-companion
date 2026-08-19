<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models;

defined('ABSPATH') || exit;

/**
 * Immutable active-play flags for temporary class conditions.
 *
 * Examples include a Barbarian currently Raging. Permanent capability remains
 * owned by certified progression; this object records only current play state.
 */
final class ActiveClassConditionState
{
    /**
     * @param array<string,bool> $conditions
     */
    private function __construct(
        private array $conditions
    ) {
    }

    public static function fresh(): self
    {
        return new self([]);
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(
        array $data
    ): self {
        $conditions = [];

        foreach ($data as $key => $value) {
            $key = sanitize_key(
                (string) $key
            );

            if ($key === '') {
                continue;
            }

            $conditions[$key] =
                (bool) $value;
        }

        return new self($conditions);
    }

    public function active(
        string $condition
    ): bool {
        return $this->conditions[
            sanitize_key($condition)
        ] ?? false;
    }

    public function activate(
        string $condition
    ): self {
        $condition = sanitize_key(
            $condition
        );

        if ($condition === '') {
            return $this;
        }

        $next = $this->conditions;
        $next[$condition] = true;

        return new self($next);
    }

    public function deactivate(
        string $condition
    ): self {
        $next = $this->conditions;

        unset(
            $next[
                sanitize_key($condition)
            ]
        );

        return new self($next);
    }

    public function clear(): self
    {
        return self::fresh();
    }

    /**
     * @return array<string,bool>
     */
    public function toArray(): array
    {
        return $this->conditions;
    }
}
