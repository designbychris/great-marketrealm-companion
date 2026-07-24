<?php

namespace GreatMarketrealmCompanion\Core\Http\Validation;

use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Validation Exception.
 *
 * Thrown when submitted input fails validation.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class ValidationException extends RuntimeException
{
    /**
     * Validation errors.
     *
     * @var array<string, array<int, string>>
     */
    protected array $errors;

    /**
     * Create a new validation exception.
     *
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(
        array $errors,
        string $message = 'The submitted data was invalid.'
    ) {
        parent::__construct($message);

        $this->errors = $errors;
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
     * Determine whether a field has an error.
     */
    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Retrieve the first error for a field.
     */
    public function first(
        string $field,
        string $default = ''
    ): string {
        return $this->errors[$field][0]
            ?? $default;
    }
}
