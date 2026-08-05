<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;

defined('ABSPATH') || exit;

/**
 * Store Character Request.
 *
 * Validates input used when creating a Character.
 *
 * This request returns validated primitive input only.
 * Domain entities and portrait recipes are created outside
 * the HTTP boundary.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.7.0
 */
final class StoreCharacterRequest extends FormRequest
{
    /**
     * Portrait fields submitted by the live Illuminator.
     *
     * @var array<int,string>
     */
    private const PORTRAIT_FIELDS = [
        'portrait_seed',
        'portrait_background',
        'portrait_body',
        'portrait_head',
        'portrait_eyes',
        'portrait_mouth',
        'portrait_palette',
        'portrait_heritage',
        'portrait_outfit',
        'portrait_equipment',
        'portrait_accessory',
        'portrait_frame',
        'portrait_effects',
    ];

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
     * Portrait fields are optional because Characters may also
     * be created through imports, tests or command-line tools.
     *
     * @return array<string,array<int,string>>
     */
    public function rules(): array
    {
        $rules = [
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
                'in:' . implode(
                    ',',
                    Race::identifiers()
                ),
            ],
            'class' => [
                'required',
                'string',
                'max:100',
                'in:' . implode(
                    ',',
                    CharacterClass::identifiers()
                ),
            ],
        ];

        foreach (
            self::PORTRAIT_FIELDS
            as $field
        ) {
            $rules[$field] = [
                'string',
                'max:150',
            ];
        }

        return $rules;
    }

    /**
     * Return validated Character creation input.
     *
     * @return array{
     *     name:string,
     *     race:string,
     *     class:string
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

    /**
     * Return the submitted provisional portrait values.
     *
     * The values remain untrusted until checked against the
     * PortraitLayerRegistry.
     *
     * @return array{
     *     seed:string,
     *     layers:array<string,string>
     * }
     */
    public function portraitData(): array
    {
        $input = $this->validated();

        return [
            'seed' => $input->string(
                'portrait_seed'
            ),
            'layers' => [
                'background' => $input->string(
                    'portrait_background'
                ),
                'body' => $input->string(
                    'portrait_body'
                ),
                'head' => $input->string(
                    'portrait_head'
                ),
                'eyes' => $input->string(
                    'portrait_eyes'
                ),
                'mouth' => $input->string(
                    'portrait_mouth'
                ),
                'palette' => $input->string(
                    'portrait_palette'
                ),
                'heritage' => $input->string(
                    'portrait_heritage'
                ),
                'outfit' => $input->string(
                    'portrait_outfit'
                ),
                'equipment' => $input->string(
                    'portrait_equipment'
                ),
                'class_accessory' => $input->string(
                    'portrait_accessory'
                ),
                'frame' => $input->string(
                    'portrait_frame'
                ),
                'effects' => $input->string(
                    'portrait_effects'
                ),
            ],
        ];
    }
}
