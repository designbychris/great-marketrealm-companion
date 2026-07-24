<?php

namespace GreatMarketrealmCompanion\Core\Http;

use GreatMarketrealmCompanion\Core\Http\Validation\ValidatedInput;
use GreatMarketrealmCompanion\Core\Http\Validation\Validator;

defined('ABSPATH') || exit;

/**
 * Form Request.
 *
 * Extends the base HTTP request with authorisation
 * and validation behaviour.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
abstract class FormRequest extends Request
{
    /**
     * Cached validated input.
     */
    protected ?ValidatedInput $validatedInput = null;

    /**
     * Determine whether the current user may perform this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define the validation rules for the request.
     *
     * @return array<string, array<int, string>>
     */
    abstract public function rules(): array;

    /**
     * Validate and retrieve the request input.
     */
    public function validated(): ValidatedInput
    {
        if ($this->validatedInput instanceof ValidatedInput) {
            return $this->validatedInput;
        }

        $validator = new Validator(
            $this->all(),
            $this->rules()
        );

        $this->validatedInput = $validator->validate();

        return $this->validatedInput;
    }

    /**
     * Determine whether the request is authorised.
     */
    public function isAuthorized(): bool
    {
        return $this->authorize();
    }
}
