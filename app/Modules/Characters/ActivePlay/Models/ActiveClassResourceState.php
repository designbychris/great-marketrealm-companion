<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Immutable active-play expenditure ledger for class resources.
 *
 * Permanent capability and maximum uses remain owned by class progression.
 * This state stores only how many uses have been expended since refresh.
 */
final class ActiveClassResourceState
{
    /**
     * @param array<string,int> $expended
     */
    private function __construct(
        private array $expended
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
        $expended = [];

        foreach ($data as $key => $value) {
            $key = sanitize_key((string) $key);

            if (
                $key === ''
                || ! is_numeric($value)
            ) {
                continue;
            }

            $expended[$key] = max(
                0,
                (int) $value
            );
        }

        return new self($expended);
    }

    public function expended(
        string $resource
    ): int {
        return $this->expended[
            sanitize_key($resource)
        ] ?? 0;
    }

    public function remaining(
        string $resource,
        int $maximum
    ): int {
        return max(
            0,
            $maximum
            - $this->expended($resource)
        );
    }

    public function spend(
        string $resource,
        int $maximum
    ): self {
        $resource = sanitize_key($resource);

        if (
            $resource === ''
            || $maximum < 1
        ) {
            throw new InvalidArgumentException(
                'The active class resource cannot be spent.'
            );
        }

        if (
            $this->remaining(
                $resource,
                $maximum
            ) < 1
        ) {
            throw new InvalidArgumentException(
                'No uses of this class resource remain.'
            );
        }

        $next = $this->expended;
        $next[$resource] =
            $this->expended($resource) + 1;

        return new self($next);
    }

    public function recover(
        string $resource,
        int $amount = 1
    ): self {
        $resource = sanitize_key($resource);

        if (
            $resource === ''
            || $amount < 1
        ) {
            throw new InvalidArgumentException(
                'The active class resource cannot be recovered.'
            );
        }

        $expended = $this->expended(
            $resource
        );

        if ($expended < $amount) {
            throw new InvalidArgumentException(
                'That active class resource cannot recover beyond its maximum.'
            );
        }

        $next = $this->expended;
        $remaining = $expended - $amount;

        if ($remaining === 0) {
            unset($next[$resource]);
        } else {
            $next[$resource] = $remaining;
        }

        return new self($next);
    }

    /**
     * @param array<int,string> $resources
     */
    public function restore(
        array $resources
    ): self {
        $next = $this->expended;

        foreach ($resources as $resource) {
            unset(
                $next[
                    sanitize_key($resource)
                ]
            );
        }

        return new self($next);
    }

    public function restoreAll(): self
    {
        return self::fresh();
    }

    /**
     * @return array<string,int>
     */
    public function toArray(): array
    {
        return $this->expended;
    }
}
