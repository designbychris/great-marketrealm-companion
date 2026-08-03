<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

/**
 * Update Character Request.
 *
 * Validates input used when editing a Character.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.7.0
 */
final class UpdateCharacterRequest extends FormRequest
{
    /**
     * Determine whether the current user
     * may edit Characters.
     */
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Validation rules.
     *
     * @return array<string,array<int,string>>
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

            'background' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    Background::identifiers()
                ),
            ],
        ];
    }

    /**
     * Return validated Character data.
     *
     * @return array{
     *     name:string,
     *     background:string
     * }
     */
    public function characterData(): array
    {
        $input = $this->validated();

        return [
            'name' => $input->string('name'),
            'background' => $input->string(
                'background'
            ),
        ];
    }
}
