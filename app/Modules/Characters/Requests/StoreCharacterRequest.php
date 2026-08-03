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
     * New Characters always begin at Level 1 through
     * CharacterCreationRules, so level is not accepted
     * from the HTTP request.
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
                'in:' . implode(
                    ',',
                    Race::identifiers()
                ),
            ],
            'class' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    CharacterClass::identifiers()
                ),
            ],
        ];
    }

    /**
     * Return validated Character creation input.
     *
     * @return array{
     *     name: string,
     *     race: string,
     *     class: string
     * }
     */
    public function characterData(): array
    {
        $input = $this->validated();

        return [
            'name' => $input->string('name'),
            'race' => $input->string('race'),
            'class' => $input->string('class'),
        ];
    }
}
