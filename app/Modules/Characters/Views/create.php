<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

$old = is_array($old ?? null)
    ? $old
    : [];

$errors = $errors ?? [];

$flash = is_array($flash ?? null)
    ? $flash
    : [];

$portrait = isset($portrait)
    && $portrait instanceof PortraitViewModel
        ? $portrait
        : null;
/**
 * Retrieve the first validation error for a field.
 */
$fieldError = static function (
    string $field
) use ($errors): ?string {
    if (is_object($errors)) {
        if (method_exists($errors, 'first')) {
            $message = $errors->first($field);

            return is_string($message) && $message !== ''
                ? $message
                : null;
        }

        if (method_exists($errors, 'get')) {
            $messages = $errors->get($field);

            if (is_string($messages)) {
                return $messages;
            }

            if (is_array($messages)) {
                $message = reset($messages);

                return is_string($message)
                    ? $message
                    : null;
            }
        }
    }

    if (! is_array($errors)) {
        return null;
    }

    $messages = $errors[$field] ?? null;

    if (is_string($messages)) {
        return $messages;
    }

    if (is_array($messages)) {
        $message = reset($messages);

        return is_string($message)
            ? $message
            : null;
    }

    return null;
};

/**
 * Create a display label from a canonical identifier.
 */
$identifierLabel = static function (
    string $identifier
): string {
    return ucwords(
        str_replace(
            '-',
            ' ',
            $identifier
        )
    );
};

$nameError = $fieldError('name');
$raceError = $fieldError('race');
$classError = $fieldError('class');
$backgroundError = $fieldError('background');
$abilitiesError = $fieldError('abilities');
$languageOneError = $fieldError('language_1');
$languageTwoError = $fieldError('language_2');
$artisanToolError = $fieldError('artisan_tool');
$gamingSetError = $fieldError('gaming_set');

$nameValue = isset($old['name'])
    && is_scalar($old['name'])
        ? (string) $old['name']
        : '';

$raceValue = isset($old['race'])
    && is_scalar($old['race'])
        ? (string) $old['race']
        : '';

$classValue = isset($old['class'])
    && is_scalar($old['class'])
        ? (string) $old['class']
        : '';

if (! Race::supports($raceValue)) {
    $raceValue = '';
}

if (! CharacterClass::supports($classValue)) {
    $classValue = '';
}

$raceOptions = Race::all();

$classOptions = CharacterClass::all();
$backgroundOptions = Background::all();
$languageOptions = Language::all();
$artisanToolOptions = ToolProficiency::artisansTools();
$gamingSetOptions = ToolProficiency::gamingSets();

$backgroundValue = isset($old['background'])
    && is_scalar($old['background'])
        ? (string) $old['background']
        : '';

if (! Background::supports($backgroundValue)) {
    $backgroundValue = '';
}

$abilityDefaults = [
    'strength' => 15,
    'dexterity' => 14,
    'constitution' => 13,
    'intelligence' => 12,
    'wisdom' => 10,
    'charisma' => 8,
];

$abilityValues = [];

foreach ($abilityDefaults as $ability => $default) {
    $submitted = $old[$ability] ?? $default;

    $abilityValues[$ability] =
        is_scalar($submitted)
            ? (int) $submitted
            : $default;
}

$languageOneValue = isset($old['language_1'])
    && is_scalar($old['language_1'])
        ? (string) $old['language_1']
        : '';

$languageTwoValue = isset($old['language_2'])
    && is_scalar($old['language_2'])
        ? (string) $old['language_2']
        : '';

$artisanToolValue = isset($old['artisan_tool'])
    && is_scalar($old['artisan_tool'])
        ? (string) $old['artisan_tool']
        : '';

$gamingSetValue = isset($old['gaming_set'])
    && is_scalar($old['gaming_set'])
        ? (string) $old['gaming_set']
        : '';


$aubyNotes = is_array($aubyNotes ?? null)
    ? $aubyNotes
    : [];

$aubyStartNotes = is_array(
    $aubyNotes['start'] ?? null
)
    ? $aubyNotes['start']
    : [];

$aubyNameNotes = is_array(
    $aubyNotes['name'] ?? null
)
    ? $aubyNotes['name']
    : [];

$aubyRaceNotes = is_array(
    $aubyNotes['race'] ?? null
)
    ? $aubyNotes['race']
    : [];

$aubyClassNotes = is_array(
    $aubyNotes['class'] ?? null
)
    ? $aubyNotes['class']
    : [];

$aubyReadyNotes = is_array(
    $aubyNotes['ready'] ?? null
)
    ? $aubyNotes['ready']
    : [];

$aubyStartNote = $aubyStartNotes[0] ?? null;

/**
 * Convert quote objects into primitive text arrays
 * for the reactive front end.
 *
 * @param array<int,mixed> $quotes
 *
 * @return array<int,string>
 */
$aubyQuoteTexts = static function (
    array $quotes
): array {
    return array_values(
        array_filter(
            array_map(
                static function (
                    mixed $quote
                ): ?string {
                    if (
                        ! is_object($quote)
                        || ! method_exists(
                            $quote,
                            'text'
                        )
                    ) {
                        return null;
                    }

                    $text = $quote->text();

                    return is_string($text)
                        && $text !== ''
                            ? $text
                            : null;
                },
                $quotes
            )
        )
    );
};

$charactersUrl = add_query_arg(
    'gmrc_route',
    'characters',
    home_url('/companion/')
);
?>

<section class="gmrc-character-creator">
    <header class="gmrc-page-header">
        <div class="gmrc-page-header__content">
            <p class="gmrc-eyebrow">
                Characters Kingdom
            </p>

            <h1>Create an adventurer</h1>

            <p>
                Begin a new tale by registering an adventurer of the
                Great Marketrealm.
            </p>
        </div>
    </header>

    <?php if (! empty($flash['error'])) : ?>
        <div
            class="gmrc-alert gmrc-alert--error"
            role="alert"
        >
            <?php echo esc_html($flash['error']); ?>
        </div>
    <?php endif; ?>

    <form
        class="
            gmrc-form
            gmrc-character-form
            character-inscription-form
        "
        action="<?php echo esc_url(
            admin_url('admin-post.php')
        ); ?>"
        method="post"
        novalidate
    >
        <input
            type="hidden"
            name="action"
            value="gmrc_app_request"
        >

        <input
            type="hidden"
            name="gmrc_route"
            value="characters"
        >

        <input
            type="hidden"
            name="registration_confirmed"
            value="1"
        >

        <input
            type="hidden"
            name="portrait_seed"
            value=""
            data-portrait-field="seed"
        >
        
        <input
            type="hidden"
            name="portrait_background"
            value=""
            data-portrait-field="background"
        >
        
        <input
            type="hidden"
            name="portrait_body"
            value=""
            data-portrait-field="body"
        >
        
        <input
            type="hidden"
            name="portrait_head"
            value=""
            data-portrait-field="head"
        >
        
        <input
            type="hidden"
            name="portrait_eyes"
            value=""
            data-portrait-field="eyes"
        >
        
        <input
            type="hidden"
            name="portrait_mouth"
            value=""
            data-portrait-field="mouth"
        >
        
        <input
            type="hidden"
            name="portrait_palette"
            value=""
            data-portrait-field="palette"
        >
        
        <input
            type="hidden"
            name="portrait_heritage"
            value=""
            data-portrait-field="heritage"
        >
        
        <input
            type="hidden"
            name="portrait_outfit"
            value=""
            data-portrait-field="outfit"
        >
        
        <input
            type="hidden"
            name="portrait_equipment"
            value=""
            data-portrait-field="equipment"
        >
        
        <input
            type="hidden"
            name="portrait_accessory"
            value=""
            data-portrait-field="class_accessory"
        >
        
        <input
            type="hidden"
            name="portrait_frame"
            value=""
            data-portrait-field="frame"
        >
        
        <input
            type="hidden"
            name="portrait_effects"
            value=""
            data-portrait-field="effects"
        >

        <?php
        wp_nonce_field(
            'gmrc_create_character',
            'gmrc_nonce'
        );
        ?>

        <section class="gmrc-form-section">
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">
                    First Inscription
                </p>

                <h2>Name your adventurer</h2>

                <p>
                    Every legend begins with a name recorded in the
                    Adventurer’s Register.
                </p>
            </header>

            <div class="gmrc-form-field">
                <?php
                echo $this->component(
                    'components.controls.scribe-input',
                    [
                        'name' => 'name',
                        'label' => 'Character name',
                        'value' => $nameValue,
                        'required' => true,
                        'autocomplete' => 'off',
                        'placeholder' =>
                            'Record the adventurer\'s name',
                        'error' => $nameError,
                    ]
                );
                ?>
            </div>
        </section>

        <section class="gmrc-form-section">
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">
                    Marketrealm Heritage
                </p>

                <h2>Choose a race</h2>

                <p>
                    Select the people and heritage from which this
                    adventurer begins their story.
                </p>
            </header>

            <fieldset
                class="
                    gmrc-choice-selector
                    gmrc-race-selector
                "
                data-choice-selector="race"
                <?php if ($raceError !== null) : ?>
                    aria-describedby="character-race-error"
                <?php endif; ?>
            >
                <legend class="screen-reader-text">
                    Character race
                </legend>

                <div class="gmrc-choice-grid">
                    <?php foreach ($raceOptions as $race) : ?>
                        <?php
                        $identifier = $race->value();

                        $isSelected =
                            $identifier === $raceValue;

                        $monogram = function_exists(
                            'mb_substr'
                        )
                            ? mb_substr(
                                $race->label(),
                                0,
                                1
                            )
                            : substr(
                                $race->label(),
                                0,
                                1
                            );

                        $monogram = function_exists(
                            'mb_strtoupper'
                        )
                            ? mb_strtoupper($monogram)
                            : strtoupper($monogram);
                        ?>

                        <label
                            class="
                                gmrc-choice-card
                                gmrc-choice-card--race
                                <?php echo $isSelected
                                    ? 'gmrc-choice-card--selected'
                                    : ''; ?>
                            "
                            data-choice-card
                            aria-current="<?php echo $isSelected
                                ? 'true'
                                : 'false'; ?>"
                            for="<?php echo esc_attr(
                                'race-' . $identifier
                            ); ?>"
                        >
                            <input
                                id="<?php echo esc_attr(
                                    'race-' . $identifier
                                ); ?>"
                                class="gmrc-choice-card__input"
                                type="radio"
                                name="race"
                                value="<?php echo esc_attr(
                                    $identifier
                                ); ?>"
                                data-race-label="<?php echo esc_attr(
                                    $race->label()
                                ); ?>"
                                <?php checked($isSelected); ?>
                                required
                            >

                            <span
                                class="gmrc-choice-card__image"
                                aria-hidden="true"
                            >
                                <span
                                    class="gmrc-choice-card__monogram"
                                >
                                    <?php echo esc_html(
                                        $monogram
                                    ); ?>
                                </span>
                            </span>

                            <span class="gmrc-choice-card__body">
                                <span
                                    class="gmrc-choice-card__heading"
                                >
                                    <strong
                                        class="
                                            gmrc-choice-card__title
                                        "
                                    >
                                        <?php echo esc_html(
                                            $race->label()
                                        ); ?>
                                    </strong>

                                    <span
                                        class="
                                            gmrc-choice-card__seal
                                        "
                                        aria-hidden="true"
                                    ></span>
                                </span>

                                <span
                                    class="gmrc-choice-card__summary"
                                >
                                    A playable heritage of the
                                    Great Marketrealm.
                                </span>

                                <span
                                    class="gmrc-choice-card__details"
                                    data-choice-details
                                    <?php echo $isSelected
                                        ? ''
                                        : 'hidden'; ?>
                                >
                                    <span
                                        class="
                                            gmrc-choice-card__detail
                                        "
                                    >
                                        <strong>Heritage</strong>

                                        <span>
                                            <?php echo esc_html(
                                                $race->label()
                                            ); ?>
                                        </span>
                                    </span>

                                    <span
                                        class="
                                            gmrc-choice-card__detail
                                        "
                                    >
                                        <strong>
                                            Racial traits
                                        </strong>

                                        <span>
                                            Awaiting inscription in
                                            the Archive.
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <?php if ($raceError !== null) : ?>
                <p
                    id="character-race-error"
                    class="gmrc-form-error"
                    role="alert"
                >
                    <?php echo esc_html($raceError); ?>
                </p>
            <?php endif; ?>
        </section>

        <section class="gmrc-form-section">
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">
                    Guild Calling
                </p>

                <h2>Choose a class</h2>

                <p>
                    Choose the training, calling and adventuring path
                    this character will follow.
                </p>
            </header>

            <fieldset
                class="
                    gmrc-choice-selector
                    gmrc-class-selector
                "
                data-choice-selector="class"
                <?php if ($classError !== null) : ?>
                    aria-describedby="character-class-error"
                <?php endif; ?>
            >
                <legend class="screen-reader-text">
                    Character class
                </legend>

                <div class="gmrc-choice-grid">
                    <?php foreach ($classOptions as $class) : ?>
                        <?php
                        $identifier = $class->value();

                        $isSelected =
                            $identifier === $classValue;

                        $savingThrows = array_map(
                            $identifierLabel,
                            $class
                                ->savingThrowProficiencies()
                        );

                        $startingHitPoints = $class->startingHitPoints(
                            AbilityScores::average()
                                ->constitution()
                        );

                        $monogram = function_exists(
                            'mb_substr'
                        )
                            ? mb_substr(
                                $class->label(),
                                0,
                                1
                            )
                            : substr(
                                $class->label(),
                                0,
                                1
                            );

                        $monogram = function_exists(
                            'mb_strtoupper'
                        )
                            ? mb_strtoupper($monogram)
                            : strtoupper($monogram);
                        ?>

                        <label
                            class="
                                gmrc-choice-card
                                gmrc-choice-card--class
                                <?php echo $isSelected
                                    ? 'gmrc-choice-card--selected'
                                    : ''; ?>
                            "
                            data-choice-card
                            aria-current="<?php echo $isSelected
                                ? 'true'
                                : 'false'; ?>"
                            for="<?php echo esc_attr(
                                'class-' . $identifier
                            ); ?>"
                        >
                            <input
                                id="<?php echo esc_attr(
                                    'class-' . $identifier
                                ); ?>"
                                class="gmrc-choice-card__input"
                                type="radio"
                                name="class"
                                value="<?php echo esc_attr(
                                    $identifier
                                ); ?>"
                                data-class-label="<?php echo esc_attr(
                                    $class->label()
                                ); ?>"
                                data-hit-die="<?php echo esc_attr(
                                    (string) $class->hitDie()
                                ); ?>"
                                data-starting-hit-points="<?php echo esc_attr(
                                    (string) $startingHitPoints
                                ); ?>"
                                data-saving-throws="<?php echo esc_attr(
                                    implode(', ', $savingThrows)
                                ); ?>"
                                <?php checked($isSelected); ?>
                                required
                            >

                            <span
                                class="gmrc-choice-card__image"
                                aria-hidden="true"
                            >
                                <span
                                    class="gmrc-choice-card__monogram"
                                >
                                    <?php echo esc_html(
                                        $monogram
                                    ); ?>
                                </span>
                            </span>

                            <span class="gmrc-choice-card__body">
                                <span
                                    class="gmrc-choice-card__heading"
                                >
                                    <strong
                                        class="
                                            gmrc-choice-card__title
                                        "
                                    >
                                        <?php echo esc_html(
                                            $class->label()
                                        ); ?>
                                    </strong>

                                    <span
                                        class="
                                            gmrc-choice-card__seal
                                        "
                                        aria-hidden="true"
                                    ></span>
                                </span>

                                <span
                                    class="gmrc-choice-card__summary"
                                >
                                    Hit Die:
                                    d<?php echo esc_html(
                                        (string) $class->hitDie()
                                    ); ?>
                                </span>

                                <span
                                    class="gmrc-choice-card__details"
                                    data-choice-details
                                    <?php echo $isSelected
                                        ? ''
                                        : 'hidden'; ?>
                                >
                                    <span
                                        class="
                                            gmrc-choice-card__detail
                                        "
                                    >
                                        <strong>Hit Die</strong>

                                        <span>
                                            d<?php echo esc_html(
                                                (string)
                                                $class->hitDie()
                                            ); ?>
                                        </span>
                                    </span>

                                    <span
                                        class="
                                            gmrc-choice-card__detail
                                        "
                                    >
                                        <strong>
                                            Saving Throws
                                        </strong>

                                        <span>
                                            <?php echo esc_html(
                                                implode(
                                                    ', ',
                                                    $savingThrows
                                                )
                                            ); ?>
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <?php if ($classError !== null) : ?>
                <p
                    id="character-class-error"
                    class="gmrc-form-error"
                    role="alert"
                >
                    <?php echo esc_html($classError); ?>
                </p>
            <?php endif; ?>
        </section>

        <section class="gmrc-form-section gmrc-registration-stage" data-registration-stage="history">
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">Personal History</p>
                <h2>Choose a background</h2>
                <p>Record the skills, languages and practical training gained before adventuring.</p>
            </header>
            <fieldset class="gmrc-background-selector" data-registration-backgrounds>
                <legend class="screen-reader-text">Character background</legend>
                <div class="gmrc-background-grid">
                    <?php foreach ($backgroundOptions as $background) : ?>
                        <?php
                        $identifier = $background->value();
                        $isSelected = $identifier === $backgroundValue;
                        $skills = array_map(
                            $identifierLabel,
                            $background->skillProficiencies()->proficiencies()
                        );
                        $tools = $background->toolProficiencyIdentifiers();
                        $needsArtisanTools = in_array(
                            ToolProficiency::CATEGORY_ARTISANS_TOOLS,
                            $tools,
                            true
                        );
                        $needsGamingSet = in_array(
                            ToolProficiency::CATEGORY_GAMING_SET,
                            $tools,
                            true
                        );
                        ?>
                        <label class="gmrc-background-option <?php echo $isSelected ? 'gmrc-background-option--selected' : ''; ?>" data-background-option>
                            <input
                                class="gmrc-background-option__input"
                                type="radio"
                                name="background"
                                value="<?php echo esc_attr($identifier); ?>"
                                data-background-label="<?php echo esc_attr($background->label()); ?>"
                                data-language-choices="<?php echo esc_attr((string) $background->languageChoices()); ?>"
                                data-needs-artisan-tools="<?php echo $needsArtisanTools ? '1' : '0'; ?>"
                                data-needs-gaming-set="<?php echo $needsGamingSet ? '1' : '0'; ?>"
                                <?php checked($isSelected); ?>
                                required
                            >
                            <span class="gmrc-background-option__image" aria-hidden="true">
                                <span class="gmrc-background-option__monogram"><?php echo esc_html(strtoupper(substr($background->label(), 0, 1))); ?></span>
                            </span>
                            <span class="gmrc-background-option__heading">
                                <strong class="gmrc-background-option__title"><?php echo esc_html($background->label()); ?></strong>
                                <span class="gmrc-background-option__control" aria-hidden="true"></span>
                            </span>
                            <span class="gmrc-background-option__summary"><?php echo esc_html(implode(', ', $skills)); ?></span>
                            <span class="gmrc-background-option__details" data-background-details <?php echo $isSelected ? '' : 'hidden'; ?>>
                                <span class="gmrc-background-option__detail">
                                    <strong>Language choices</strong>
                                    <span><?php echo esc_html((string) $background->languageChoices()); ?></span>
                                </span>
                                <span class="gmrc-background-option__detail">
                                    <strong>Practical training</strong>
                                    <span>
                                        <?php
                                        $toolLabels = [];
                                        foreach ($tools as $toolId) {
                                            $toolLabels[] = ToolProficiency::supports($toolId)
                                                ? ToolProficiency::fromString($toolId)->label()
                                                : $identifierLabel($toolId);
                                        }
                                        echo esc_html($toolLabels !== [] ? implode(', ', $toolLabels) : 'None');
                                        ?>
                                    </span>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php if ($backgroundError !== null) : ?>
                <p class="gmrc-form-error" role="alert"><?php echo esc_html($backgroundError); ?></p>
            <?php endif; ?>
        </section>

        <section class="gmrc-form-section gmrc-registration-stage" data-registration-stage="abilities">
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">Adventuring Measures</p>
                <h2>Assign the Standard Guild Array</h2>
                <p>Assign 15, 14, 13, 12, 10 and 8 exactly once.</p>
            </header>
            <div class="gmrc-registration-abilities" data-registration-abilities>
                <?php foreach ([
                    'strength' => 'Strength',
                    'dexterity' => 'Dexterity',
                    'constitution' => 'Constitution',
                    'intelligence' => 'Intelligence',
                    'wisdom' => 'Wisdom',
                    'charisma' => 'Charisma',
                ] as $ability => $label) : ?>
                    <label class="gmrc-registration-ability">
                        <span><?php echo esc_html($label); ?></span>
                        <select name="<?php echo esc_attr($ability); ?>" data-registration-ability="<?php echo esc_attr($ability); ?>" required>
                            <?php foreach ([15, 14, 13, 12, 10, 8] as $score) : ?>
                                <option value="<?php echo esc_attr((string) $score); ?>" <?php selected($abilityValues[$ability], $score); ?>>
                                    <?php echo esc_html((string) $score); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small data-registration-modifier="<?php echo esc_attr($ability); ?>"></small>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if ($abilitiesError !== null) : ?>
                <p class="gmrc-form-error" role="alert"><?php echo esc_html($abilitiesError); ?></p>
            <?php endif; ?>
        </section>

        <section class="gmrc-form-section gmrc-registration-stage" data-registration-stage="proficiencies" data-registration-choices>
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">Registrar’s Choices</p>
                <h2>Complete your proficiencies</h2>
                <p>Only choices required by the selected background are revealed here.</p>
            </header>
            <div class="gmrc-registration-choice" data-language-slot="1" hidden>
                <label for="registration-language-1">First language</label>
                <select id="registration-language-1" name="language_1">
                    <option value="">Choose a language</option>
                    <?php foreach ($languageOptions as $language) : ?>
                        <option value="<?php echo esc_attr($language->value()); ?>" <?php selected($languageOneValue, $language->value()); ?>>
                            <?php echo esc_html($language->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($languageOneError !== null) : ?><p class="gmrc-form-error" role="alert"><?php echo esc_html($languageOneError); ?></p><?php endif; ?>
            </div>
            <div class="gmrc-registration-choice" data-language-slot="2" hidden>
                <label for="registration-language-2">Second language</label>
                <select id="registration-language-2" name="language_2">
                    <option value="">Choose a different language</option>
                    <?php foreach ($languageOptions as $language) : ?>
                        <option value="<?php echo esc_attr($language->value()); ?>" <?php selected($languageTwoValue, $language->value()); ?>>
                            <?php echo esc_html($language->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($languageTwoError !== null) : ?><p class="gmrc-form-error" role="alert"><?php echo esc_html($languageTwoError); ?></p><?php endif; ?>
            </div>
            <div class="gmrc-registration-choice" data-tool-choice="artisan" hidden>
                <label for="registration-artisan-tool">Artisan’s Tool</label>
                <select id="registration-artisan-tool" name="artisan_tool">
                    <option value="">Choose an artisan’s tool</option>
                    <?php foreach ($artisanToolOptions as $tool) : ?>
                        <option value="<?php echo esc_attr($tool->value()); ?>" <?php selected($artisanToolValue, $tool->value()); ?>>
                            <?php echo esc_html($tool->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($artisanToolError !== null) : ?><p class="gmrc-form-error" role="alert"><?php echo esc_html($artisanToolError); ?></p><?php endif; ?>
            </div>
            <div class="gmrc-registration-choice" data-tool-choice="gaming" hidden>
                <label for="registration-gaming-set">Gaming Set</label>
                <select id="registration-gaming-set" name="gaming_set">
                    <option value="">Choose a gaming set</option>
                    <?php foreach ($gamingSetOptions as $tool) : ?>
                        <option value="<?php echo esc_attr($tool->value()); ?>" <?php selected($gamingSetValue, $tool->value()); ?>>
                            <?php echo esc_html($tool->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($gamingSetError !== null) : ?><p class="gmrc-form-error" role="alert"><?php echo esc_html($gamingSetError); ?></p><?php endif; ?>
            </div>
            <p class="gmrc-registration-complete-note" data-registration-no-extra-choices hidden>
                ✦ This background has no unresolved Registrar choices.
            </p>
        </section>

        <section
    class="gmrc-living-desk"
    data-living-desk
    data-auby-start="<?php echo esc_attr(
        wp_json_encode(
            $aubyQuoteTexts(
                $aubyStartNotes
            )
        )
    ); ?>"
    data-auby-name="<?php echo esc_attr(
        wp_json_encode(
            $aubyQuoteTexts(
                $aubyNameNotes
            )
        )
    ); ?>"
    data-auby-race="<?php echo esc_attr(
        wp_json_encode(
            $aubyQuoteTexts(
                $aubyRaceNotes
            )
        )
    ); ?>"
    data-auby-class="<?php echo esc_attr(
        wp_json_encode(
            $aubyQuoteTexts(
                $aubyClassNotes
            )
        )
    ); ?>"
    data-auby-ready="<?php echo esc_attr(
        wp_json_encode(
            $aubyQuoteTexts(
                $aubyReadyNotes
            )
        )
    ); ?>"
>
    <header class="gmrc-living-desk__header">
        <p class="gmrc-eyebrow">
            The Registrar’s Desk
        </p>

        <h2>The first page of the adventure</h2>

        <p>
            New adventurers begin at Level 1 with no experience.
            Their starting hit points are calculated from their
            chosen class and Constitution.
        </p>
    </header>

    <div class="gmrc-living-desk__surface">
        <div class="gmrc-living-desk__auby">
            <?php
            if ($aubyStartNote !== null) {
                echo $this->component(
                    'components.furniture.auby-note',
                    [
                        'quote' => $aubyStartNote,
                    ]
                );
            }
            ?>
        </div>

        <div class="gmrc-living-desk__illuminator">
            <?php
            if ($portrait instanceof PortraitViewModel) {
                echo $this->component(
                    'components.media.illuminated-portrait',
                    [
                        'portrait' => $portrait,
                    ]
                );

                echo $this->component(
                    'components.auby.seal-of-approval',
                    [
                        'context' => 'portrait',
                        'trigger' => 'manual',
                    ]
                );
            }
            ?>
        </div>

        <section
            class="gmrc-creation-preview"
            data-character-creation-preview
            aria-live="polite"
        >
            <div
                class="gmrc-creation-preview__particles"
                aria-hidden="true"
            >
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="gmrc-creation-preview__scroll">
                <div
                    class="
                        gmrc-creation-preview__roller
                        gmrc-creation-preview__roller--top
                    "
                >
                    <span></span>
                </div>

                <div class="gmrc-creation-preview__paper">
                    <div
                        class="gmrc-registrar"
                        aria-hidden="true"
                    >
                        <div class="gmrc-registrar__inkwell"></div>
                    
                        <div class="gmrc-registrar__quill">
                            🪶
                        </div>
                    </div>
                    <header class="gmrc-creation-preview__header">
                        <p class="gmrc-eyebrow">
                            Provisional Guild Record
                        </p>

                        <h2
                            class="gmrc-creation-preview__name"
                            data-preview-name
                            data-register-anchor="name"
                        >
                            Unnamed Adventurer
                        </h2>

                        <p class="gmrc-creation-preview__identity">
                            <span data-preview-race
                                data-register-anchor="race" 
                            >
                                Heritage awaiting selection
                            </span>

                            <span aria-hidden="true">·</span>

                            <span data-preview-class
                                data-register-anchor="class"
                            >
                                Class awaiting selection
                            </span>
                        </p>
                    </header>

                    <div
                        class="recipe-divider"
                        aria-hidden="true"
                    >
                        <span class="recipe-divider__ornament">
                            ✦
                        </span>
                    </div>

                    <dl class="gmrc-creation-preview__statistics">
                        <div>
                            <dt>Starting level</dt>

                            <dd>
                                <span class="gmrc-preview-ink">
                                    1
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt>Starting experience</dt>

                            <dd>
                                <span class="gmrc-preview-ink">
                                    0
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt>Hit Die</dt>

                            <dd>
                                <span
                                    class="gmrc-preview-ink"
                                    data-preview-hit-die
                                >
                                    —
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt>Starting Hit Points</dt>

                            <dd>
                                <span
                                    class="gmrc-preview-ink"
                                    data-preview-hit-points
                                    data-register-anchor="hp"
                                >
                                    —
                                </span>
                            </dd>
                        </div>
                    </dl>

                    <section class="gmrc-creation-preview__entry">
                        <h3>
                            Saving Throw Proficiencies
                        </h3>

                        <p
                            class="gmrc-preview-ink"
                            data-preview-saving-throws
                            data-register-anchor="saving"
                        >
                            Choose a class to reveal its defensive
                            training.
                        </p>
                    </section>

                    <section class="gmrc-creation-preview__entry">
                        <h3>Archive Note</h3>

                        <p
                            class="gmrc-preview-ink"
                            data-preview-note
                            data-register-anchor="note"
                        >
                            Choose a race and class to begin this
                            adventurer’s first inscription.
                        </p>
                    </section>

                    <footer class="gmrc-creation-preview__footer">
                        <span aria-hidden="true">✦</span>

                        <span data-preview-status>
                            Awaiting the Guild Registrar’s seal
                        </span>

                        <span aria-hidden="true">✦</span>
                    </footer>
                </div>

                <div
                    class="
                        gmrc-creation-preview__roller
                        gmrc-creation-preview__roller--bottom
                    "
                >
                    <span></span>
                </div>
            </div>
        </section>
    </div>
</section>

        <section class="gmrc-registration-review" aria-labelledby="gmrc-registration-review-title" data-registration-review>
            <span class="gmrc-registration-review__tape" aria-hidden="true"></span>
            <p class="gmrc-eyebrow">Final Registrar Review</p>
            <h2 id="gmrc-registration-review-title">Review the Guild Record</h2>
            <p>Auby has checked the ink. The Registrar is waiting for your seal.</p>
            <dl class="gmrc-registration-review__record">
                <div><dt>Adventurer</dt><dd data-registration-review-name>Awaiting inscription</dd></div>
                <div><dt>Heritage</dt><dd data-registration-review-race>Awaiting selection</dd></div>
                <div><dt>Calling</dt><dd data-registration-review-class>Awaiting selection</dd></div>
                <div><dt>Background</dt><dd data-registration-review-background>Awaiting selection</dd></div>
                <div><dt>Guild Array</dt><dd data-registration-review-abilities>Awaiting assignment</dd></div>
                <div><dt>Registrar choices</dt><dd data-registration-review-choices>Awaiting background</dd></div>
            </dl>
            <p class="gmrc-registration-review__signature">Ready for the Guild seal.<span>— Auby</span></p>
        </section>

        <div class="gmrc-form-actions">
            <?php
            echo $this->component(
                'components.controls.wax-button',
                [
                    'label' => 'Seal the Guild Record',
                    'type' => 'submit',
                    'symbol' => '✦',
                    'variant' => 'wax',
                    'size' => 'large',
                ]
            );

            echo $this->component(
                'components.controls.paper-button',
                [
                    'label' => 'Return to Register',
                    'href' => $charactersUrl,
                    'symbol' => '‹',
                    'variant' => 'parchment',
                    'size' => 'large',
                ]
            );
            ?>
        </div>
    </form>
</section>
