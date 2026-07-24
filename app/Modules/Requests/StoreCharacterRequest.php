<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

/**
 * Store Character Request.
 *
 * Validates input used when creating a character.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class StoreCharacterRequest extends FormRequest
{
    /**
     * Determine whether the current user may create characters.
     */
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Character creation validation rules.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'race' => [
                'required',
                'string',
                'max:100',
            ],
            'class' => [
                'required',
                'string',
                'max:100',
            ],
            'level' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],
        ];
    }
}
