<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

/**
 * Store Character Request.
 *
 * Validates input used when creating a Character.
 *
 * This request is responsible only for validation and
 * returning validated primitive input. Domain entities
 * are created outside the HTTP layer.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.7.0
 */
final class StoreCharacterRequest extends FormRequest
{
    /**
     * Determine whether the current user may create Characters.
     */
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Character creation validation rules.
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

    /**
     * Return validated Character creation input.
     *
     * Race, class and level remain part of the validated
     * form data while their domain objects are developed.
     *
     * @return array{
     *     name: string,
     *     race: string,
     *     class: string,
     *     level: int
     * }
     */
    public function characterData(): array
    {
        $input = $this->validated();

        return [
            'name' => $input->string('name'),
            'race' => $input->string('race'),
            'class' => $input->string('class'),
            'level' => $input->integer(
                'level',
                1
            ),
        ];
    }
}
