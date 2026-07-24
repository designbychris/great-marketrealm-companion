<?php

namespace GreatMarketrealmCompanion\Core\Http\Validation;

defined('ABSPATH') || exit;

/**
 * Validated Input.
 *
 * Provides typed access to successfully validated input.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class ValidatedInput
{
    /**
     * Validated values.
     *
     * @var array<string, mixed>
     */
    protected array $input;

    /**
     * Constructor.
     *
     * @param array<string, mixed> $input
     */
    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * Retrieve a validated value.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->input[$key]
            ?? $default;
    }

    /**
     * Determine whether a validated value exists.
     */
    public function has(string $key): bool
    {
        return array_key_exists(
            $key,
            $this->input
        );
    }

    /**
     * Retrieve a validated string.
     */
    public function string(
        string $key,
        string $default = ''
    ): string {
        $value = $this->get(
            $key,
            $default
        );

        return is_scalar($value)
            ? (string) $value
            : $default;
    }

    /**
     * Retrieve a validated integer.
     */
    public function integer(
        string $key,
        int $default = 0
    ): int {
        $value = $this->get(
            $key,
            $default
        );

        return is_numeric($value)
            ? (int) $value
            : $default;
    }

    /**
     * Retrieve a validated boolean.
     */
    public function boolean(
        string $key,
        bool $default = false
    ): bool {
        $value = $this->get(
            $key,
            $default
        );

        if (is_bool($value)) {
            return $value;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        ) ?? $default;
    }

    /**
     * Retrieve a validated array.
     *
     * @return array<mixed>
     */
    public function array(
        string $key,
        array $default = []
    ): array {
        $value = $this->get(
            $key,
            $default
        );

        return is_array($value)
            ? $value
            : $default;
    }

    /**
     * Retrieve all validated values.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->input;
    }
}
