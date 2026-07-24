<?php

namespace GreatMarketrealmCompanion\Core\Http\Validation;

defined('ABSPATH') || exit;

/**
 * Validator.
 *
 * Validates request input against a collection of rules.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class Validator
{
    /**
     * Input being validated.
     *
     * @var array<string, mixed>
     */
    protected array $input;

    /**
     * Validation rules.
     *
     * @var array<string, array<int, string>>
     */
    protected array $rules;

    /**
     * Validation errors.
     *
     * @var array<string, array<int, string>>
     */
    protected array $errors = [];

    /**
     * Validated values.
     *
     * @var array<string, mixed>
     */
    protected array $validated = [];

    /**
     * Constructor.
     *
     * @param array<string, mixed>              $input
     * @param array<string, array<int, string>> $rules
     */
    public function __construct(
        array $input,
        array $rules
    ) {
        $this->input = $input;
        $this->rules = $rules;
    }

    /**
     * Validate the supplied input.
     *
     * @throws ValidationException
     */
    public function validate(): ValidatedInput
    {
        foreach ($this->rules as $field => $rules) {
            $this->validateField(
                $field,
                $rules
            );
        }

        if (! empty($this->errors)) {
            throw new ValidationException(
                $this->errors
            );
        }

        return new ValidatedInput(
            $this->validated
        );
    }

    /**
     * Validate a single field.
     *
     * @param array<int, string> $rules
     */
    protected function validateField(
        string $field,
        array $rules
    ): void {
        $exists = array_key_exists(
            $field,
            $this->input
        );

        $value = $this->input[$field]
            ?? null;

        foreach ($rules as $rule) {
            [$ruleName, $parameters] = $this->parseRule(
                $rule
            );

            if (
                ! $exists
                && $ruleName !== 'required'
            ) {
                continue;
            }

            if (
                $this->fails(
                    $ruleName,
                    $value,
                    $parameters
                )
            ) {
                $this->addError(
                    $field,
                    $ruleName,
                    $parameters
                );

                return;
            }
        }

        if ($exists) {
            $this->validated[$field] = $value;
        }
    }

    /**
     * Determine whether a rule fails.
     *
     * @param array<int, string> $parameters
     */
    protected function fails(
        string $rule,
        mixed $value,
        array $parameters
    ): bool {
        return match ($rule) {
            'required' => $this->failsRequired($value),
            'string' => ! is_string($value),
            'integer' => filter_var(
                $value,
                FILTER_VALIDATE_INT
            ) === false,
            'boolean' => filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) === null,
            'array' => ! is_array($value),
            'min' => $this->failsMin(
                $value,
                $parameters
            ),
            'max' => $this->failsMax(
                $value,
                $parameters
            ),
            'in' => ! in_array(
                (string) $value,
                $parameters,
                true
            ),
            default => false,
        };
    }

    /**
     * Determine whether required validation fails.
     */
    protected function failsRequired(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_array($value)) {
            return $value === [];
        }

        return false;
    }

    /**
     * Determine whether minimum validation fails.
     *
     * @param array<int, string> $parameters
     */
    protected function failsMin(
        mixed $value,
        array $parameters
    ): bool {
        $minimum = isset($parameters[0])
            ? (int) $parameters[0]
            : 0;

        if (is_numeric($value)) {
            return (float) $value < $minimum;
        }

        if (is_string($value)) {
            return mb_strlen($value) < $minimum;
        }

        if (is_array($value)) {
            return count($value) < $minimum;
        }

        return true;
    }

    /**
     * Determine whether maximum validation fails.
     *
     * @param array<int, string> $parameters
     */
    protected function failsMax(
        mixed $value,
        array $parameters
    ): bool {
        $maximum = isset($parameters[0])
            ? (int) $parameters[0]
            : 0;

        if (is_numeric($value)) {
            return (float) $value > $maximum;
        }

        if (is_string($value)) {
            return mb_strlen($value) > $maximum;
        }

        if (is_array($value)) {
            return count($value) > $maximum;
        }

        return true;
    }

    /**
     * Parse a rule and its parameters.
     *
     * @return array{0: string, 1: array<int, string>}
     */
    protected function parseRule(string $rule): array
    {
        [$name, $parameterString] = array_pad(
            explode(':', $rule, 2),
            2,
            ''
        );

        $parameters = $parameterString === ''
            ? []
            : array_map(
                'trim',
                explode(',', $parameterString)
            );

        return [
            trim($name),
            $parameters,
        ];
    }

    /**
     * Add an error for a field.
     *
     * @param array<int, string> $parameters
     */
    protected function addError(
        string $field,
        string $rule,
        array $parameters
    ): void {
        $this->errors[$field][] = $this->message(
            $field,
            $rule,
            $parameters
        );
    }

    /**
     * Build a validation error message.
     *
     * @param array<int, string> $parameters
     */
    protected function message(
        string $field,
        string $rule,
        array $parameters
    ): string {
        $label = ucfirst(
            str_replace('_', ' ', $field)
        );

        return match ($rule) {
            'required' => "{$label} is required.",
            'string' => "{$label} must be text.",
            'integer' => "{$label} must be a whole number.",
            'boolean' => "{$label} must be true or false.",
            'array' => "{$label} must be a list.",
            'min' => "{$label} must be at least {$parameters[0]}.",
            'max' => "{$label} may not be greater than {$parameters[0]}.",
            'in' => "{$label} contains an invalid value.",
            default => "{$label} is invalid.",
        };
    }
}
