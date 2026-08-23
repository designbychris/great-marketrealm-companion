<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Requests\Concerns\ResolvesRegistrationInput;

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
    use ResolvesRegistrationInput;

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
        'portrait_class_effects',
        'portrait_guild_ornament',
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
            'heritage' => [
                'string',
                'max:100',
            ],
            'subclass' => [
                'string',
                'max:150',
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

    /** @return array{heritage:string,subclass:string} */
    public function catalogueData(): array
    {
        $input = $this->validated();
        return [
            'heritage' => $input->string('heritage'),
            'subclass' => $input->string('subclass'),
        ];
    }

    /**
     * Return Complete Registration data.
     *
     * Core characterData() intentionally remains the original three-string
     * contract for imports, API callers and existing tests.
     *
     * @return array{
     *     background:string,
     *     abilities:array{
     *         strength:int,
     *         dexterity:int,
     *         constitution:int,
     *         intelligence:int,
     *         wisdom:int,
     *         charisma:int
     *     },
     *     languages:array<int,string>,
     *     tools:array<int,string>,
     *     background_skills:array<int,string>,
     *     background_tools:array<int,string>,
     *     confirmed:bool
     * }
     */
    public function registrationData(): array
    {
        /*
         * Validate the established core fields first.
         */
        $this->validated();

        $background = $this
            ->registrationBackground();

        $choices = $this
            ->registrationChoices(
                $background
            );

        return [
            'background' => $background->value(),
            'abilities' =>
                $this->registrationAbilityScores(),
            'languages' => $choices['languages'],
            'tools' => $choices['tools'],
            'background_skills' => $background->skillProficiencies()->proficiencies(),
            'background_tools' => $background->toolProficiencyIdentifiers(),
            'confirmed' =>
                $this->registrationIsConfirmed(),
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
                'class_effects' => $input->string(
                    'portrait_class_effects'
                ),
                'guild_ornament' => $input->string(
                    'portrait_guild_ornament'
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
