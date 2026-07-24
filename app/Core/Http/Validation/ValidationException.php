<?php

namespace GreatMarketrealmCompanion\Exceptions;

use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Validation Exception.
 *
 * Thrown when submitted request data fails validation.
 *
 * The exception carries both the validation errors and the
 * submitted input so the exception handler can flash them
 * before redirecting the user back to the form.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class ValidationException extends RuntimeException
{
    /**
     * Create a validation exception.
     *
     * @param array<string, array<int, string>> $errors
     * @param array<string, mixed>              $oldInput
     */
    public function __construct(
        protected array $errors,
        protected array $oldInput = [],
        string $message = 'The submitted data failed validation.'
    ) {
        parent::__construct($message);
    }

    /**
     * Retrieve all validation errors.
     *
     * @return array<string, array<int, string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Determine whether an error exists for a field.
     */
    public function has(string $field): bool
    {
        return isset($this->errors[$field])
            && $this->errors[$field] !== [];
    }

    /**
     * Retrieve the first validation error for a field.
     */
    public function first(
        string $field,
        ?string $default = null
    ): ?string {
        $errors = $this->errors[$field] ?? [];

        if (! is_array($errors)) {
            return is_string($errors)
                ? $errors
                : $default;
        }

        $first = $errors[0] ?? $default;

        return is_string($first)
            ? $first
            : $default;
    }

    /**
     * Retrieve the submitted form input.
     *
     * @return array<string, mixed>
     */
    public function oldInput(): array
    {
        return $this->oldInput;
    }

    /**
     * Determine whether submitted input contains a field.
     */
    public function hasOldInput(string $field): bool
    {
        return array_key_exists(
            $field,
            $this->oldInput
        );
    }

    /**
     * Retrieve a submitted input value.
     */
    public function old(
        string $field,
        mixed $default = null
    ): mixed {
        return $this->oldInput[$field]
            ?? $default;
    }
}
