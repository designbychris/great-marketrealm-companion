<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Requests\Concerns;

use GreatMarketrealmCompanion\Core\Http\Validation\ValidationException;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\BackgroundMechanicsRegister;

defined('ABSPATH') || exit;

/**
 * Resolve Complete Registration input.
 *
 * Existing API/import callers remain backward compatible: strict completion
 * is enabled only when the browser Registrar submits registration_confirmed=1.
 */
trait ResolvesRegistrationInput
{
    /**
     * Return the Standard Guild ability array used during registration.
     *
     * Trait constants require PHP 8.2+, while the Companion supports PHP 8.1.
     *
     * @return array<int,int>
     */
    private function standardGuildArray(): array
    {
        return [
            8,
            10,
            12,
            13,
            14,
            15,
        ];
    }

    /**
     * Return whether this submission came through the Complete Registration UI.
     */
    private function registrationIsConfirmed(): bool
    {
        return $this->string(
            'registration_confirmed'
        ) === '1';
    }

    /**
     * Resolve a registration background.
     */
    private function registrationBackground(
        string $fallback = 'market-runner'
    ): Background {
        $value = $this->string(
            'background',
            $fallback
        );

        if (! Background::supports($value)) {
            $this->registrationFail(
                'background',
                'Choose a recognised background from the Guild Register.'
            );
        }

        return (new BackgroundMechanicsRegister())->background($value);
    }

    /**
     * Resolve the selected ability-generation method.
     *
     * Existing submissions predate this selector and therefore remain on
     * the Standard Guild Array unless they explicitly choose 3d6.
     */
    private function registrationAbilityMethod(): string
    {
        $method = $this->string(
            'ability_method',
            'standard'
        );

        return $method === 'rolled'
            ? 'rolled'
            : 'standard';
    }

    /**
     * Resolve the six ability scores.
     *
     * Legacy/programmatic submissions may omit these and retain average scores.
     *
     * @return array{
     *     strength:int,
     *     dexterity:int,
     *     constitution:int,
     *     intelligence:int,
     *     wisdom:int,
     *     charisma:int
     * }
     */
    private function registrationAbilityScores(): array
    {
        $fields = [
            'strength',
            'dexterity',
            'constitution',
            'intelligence',
            'wisdom',
            'charisma',
        ];

        $strict = $this->registrationIsConfirmed();
        $method = $this->registrationAbilityMethod();

        $values = [];

        foreach ($fields as $field) {
            if (! $this->has($field)) {
                if ($strict) {
                    $this->registrationFail(
                        $field,
                        'Assign one value from the Standard Guild Array.'
                    );
                }

                $values[$field] = 10;

                continue;
            }

            $raw = $this->input($field);

            if (
                ! is_scalar($raw)
                || filter_var(
                    $raw,
                    FILTER_VALIDATE_INT
                ) === false
            ) {
                $this->registrationFail(
                    $field,
                    'Ability scores must be whole numbers.'
                );
            }

            $score = (int) $raw;

            if (
                $strict
                && $method === 'standard'
                && ! in_array(
                    $score,
                    $this->standardGuildArray(),
                    true
                )
            ) {
                $this->registrationFail(
                    $field,
                    'Use only values from the Standard Guild Array: 15, 14, 13, 12, 10 and 8.'
                );
            }

            if (
                $strict
                && $method === 'rolled'
                && ($score < 3 || $score > 18)
            ) {
                $this->registrationFail(
                    $field,
                    'A 3d6 ability score must be between 3 and 18.'
                );
            }

            if ($score < 1 || $score > 30) {
                $this->registrationFail(
                    $field,
                    'Ability scores must be between 1 and 30.'
                );
            }

            $values[$field] = $score;
        }

        if (
            $strict
            && $method === 'standard'
        ) {
            $submitted = array_values(
                $values
            );

            sort($submitted);

            $expected = $this->standardGuildArray();
            sort($expected);

            if ($submitted !== $expected) {
                $this->registrationFail(
                    'abilities',
                    'Assign each Standard Guild Array value exactly once.'
                );
            }
        }

        return $values;
    }

    /**
     * Resolve language and tool choices granted by a Background.
     *
     * @return array{
     *     languages:array<int,string>,
     *     tools:array<int,string>
     * }
     */
    private function registrationChoices(
        Background $background
    ): array {
        $strict = $this->registrationIsConfirmed();

        $languageCount = $background
            ->languageChoices();

        $languages = [];

        for ($index = 1; $index <= 2; $index++) {
            $field = 'language_' . $index;
            $value = $this->string($field);

            if ($index <= $languageCount) {
                if ($value === '') {
                    if ($strict) {
                        $this->registrationFail(
                            $field,
                            'Choose a language granted by this background.'
                        );
                    }

                    continue;
                }

                if (! Language::supports($value)) {
                    $this->registrationFail(
                        $field,
                        'Choose a recognised language.'
                    );
                }

                $languages[] = Language::fromString(
                    $value
                )->value();
            }
        }

        if (
            $strict
            && count($languages) !== count(
                array_unique($languages)
            )
        ) {
            $this->registrationFail(
                'languages',
                'Choose different languages for each available language slot.'
            );
        }

        $tools = [];

        foreach (
            $background->toolProficiencyIdentifiers()
            as $toolIdentifier
        ) {
            if (
                ! ToolProficiency::supports(
                    $toolIdentifier
                )
            ) {
                continue;
            }

            $tool = ToolProficiency::fromString(
                $toolIdentifier
            );

            if (! $tool->isChoiceCategory()) {
                continue;
            }

            $field = $toolIdentifier
                === ToolProficiency::CATEGORY_ARTISANS_TOOLS
                    ? 'artisan_tool'
                    : 'gaming_set';

            $value = $this->string($field);

            if ($value === '') {
                if ($strict) {
                    $this->registrationFail(
                        $field,
                        sprintf(
                            'Choose one %s for this background.',
                            $tool->label()
                        )
                    );
                }

                continue;
            }

            if (! ToolProficiency::supports($value)) {
                $this->registrationFail(
                    $field,
                    'Choose a recognised tool proficiency.'
                );
            }

            $chosenTool = ToolProficiency::fromString(
                $value
            );

            if (
                $chosenTool->isChoiceCategory()
                || ! $chosenTool->belongsTo(
                    $toolIdentifier
                )
            ) {
                $this->registrationFail(
                    $field,
                    sprintf(
                        'Choose a concrete option from %s.',
                        $tool->label()
                    )
                );
            }

            $tools[] = $chosenTool->value();
        }

        return [
            'languages' => $languages,
            'tools' => $tools,
        ];
    }

    /**
     * Throw a normal form ValidationException so the existing exception
     * handler flashes errors and old input back to the Registrar.
     */
    private function registrationFail(
        string $field,
        string $message
    ): never {
        throw new ValidationException(
            [
                $field => [
                    $message,
                ],
            ],
            $this->all()
        );
    }
}
