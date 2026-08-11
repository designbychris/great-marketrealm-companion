<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Requests;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Modules\Characters\Requests\Concerns\ResolvesRegistrationInput;

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
    use ResolvesRegistrationInput;

    /** @var array<int,string> */
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
        $rules = [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'background' => [
                'required',
                'string',
                'max:100',
                'in:' . implode(
                    ',',
                    Background::identifiers()
                ),
            ],
        ];

        foreach (self::PORTRAIT_FIELDS as $field) {
            $rules[$field] = [
                'string',
                'max:150',
            ];
        }

        return $rules;
    }

    /**
     * Resolve background-dependent language/tool selections.
     *
     * @return array{
     *     languages:array<int,string>,
     *     tools:array<int,string>,
     *     confirmed:bool
     * }
     */
    public function registrationChoicesFor(
        Background $background
    ): array {
        $this->validated();

        $choices = $this
            ->registrationChoices(
                $background
            );

        return [
            'languages' => $choices['languages'],
            'tools' => $choices['tools'],
            'confirmed' =>
                $this->registrationIsConfirmed(),
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

    /** @return array{seed:string,layers:array<string,string>} */
    public function portraitData(): array
    {
        $input = $this->validated();

        return [
            'seed' => $input->string('portrait_seed'),
            'layers' => [
                'background' => $input->string('portrait_background'),
                'body' => $input->string('portrait_body'),
                'head' => $input->string('portrait_head'),
                'eyes' => $input->string('portrait_eyes'),
                'mouth' => $input->string('portrait_mouth'),
                'palette' => $input->string('portrait_palette'),
                'heritage' => $input->string('portrait_heritage'),
                'outfit' => $input->string('portrait_outfit'),
                'equipment' => $input->string('portrait_equipment'),
                'class_accessory' => $input->string('portrait_accessory'),
                'frame' => $input->string('portrait_frame'),
                'effects' => $input->string('portrait_effects'),
            ],
        ];
    }
}
