<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;

defined('ABSPATH') || exit;

if (
    ! isset($character)
    || ! $character instanceof Character
) {
    return;
}

$old = is_array($old ?? null)
    ? $old
    : [];

$errors = $errors ?? [];

$flash = is_array($flash ?? null)
    ? $flash
    : [];

$characterId = $character
    ->id()
    ->value();

$currentName = $character
    ->name()
    ->value();

$currentRace = $character
    ->race();

$currentClass = $character
    ->characterClass();

$currentBackground = $character
    ->background();

$backgroundOptions = Background::all();
$languageOptions = Language::all();
$artisanToolOptions = ToolProficiency::artisansTools();
$gamingSetOptions = ToolProficiency::gamingSets();

$companionUrl = home_url(
    '/companion/'
);

$characterUrl = add_query_arg(
    'gmrc_route',
    'characters/' . rawurlencode($characterId),
    $companionUrl
);

$charactersUrl = add_query_arg(
    'gmrc_route',
    'characters',
    $companionUrl
);

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
 * Convert a canonical identifier into a fallback label.
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

$backgroundError = $fieldError(
    'background'
);

$nameValue = isset($old['name'])
    && is_scalar($old['name'])
        ? (string) $old['name']
        : $currentName;

$backgroundValue = isset($old['background'])
    && is_scalar($old['background'])
        ? sanitize_key(
            (string) $old['background']
        )
        : $currentBackground->value();

if (! Background::supports($backgroundValue)) {
    $backgroundValue = $currentBackground->value();
}

$selectedLanguages = $character
    ->selectedLanguages()
    ->values();

$selectedTools = $character
    ->selectedToolProficiencies()
    ->values();

$languageOneValue = isset($old['language_1'])
    && is_scalar($old['language_1'])
        ? (string) $old['language_1']
        : ($selectedLanguages[0] ?? '');

$languageTwoValue = isset($old['language_2'])
    && is_scalar($old['language_2'])
        ? (string) $old['language_2']
        : ($selectedLanguages[1] ?? '');

$artisanToolValue = isset($old['artisan_tool'])
    && is_scalar($old['artisan_tool'])
        ? (string) $old['artisan_tool']
        : '';

$gamingSetValue = isset($old['gaming_set'])
    && is_scalar($old['gaming_set'])
        ? (string) $old['gaming_set']
        : '';


$portraitLayer = static function (
    string $slot
) use ($portrait): string {
    if (
        ! isset($portrait)
        || ! is_object($portrait)
        || ! method_exists($portrait, 'layer')
    ) {
        return '';
    }

    $value = $portrait->layer($slot);

    return is_string($value)
        ? $value
        : '';
};

$portraitSeed = isset($portrait)
    && is_object($portrait)
    && method_exists($portrait, 'seed')
        ? (string) ($portrait->seed() ?? '')
        : '';

foreach ($selectedTools as $selectedTool) {
    if (! ToolProficiency::supports($selectedTool)) {
        continue;
    }

    $tool = ToolProficiency::fromString($selectedTool);

    if ($tool->isArtisansTool() && $artisanToolValue === '') {
        $artisanToolValue = $tool->value();
    }

    if ($tool->isGamingSet() && $gamingSetValue === '') {
        $gamingSetValue = $tool->value();
    }
}
?>

<section class="gmrc-character-editor">
    <header class="gmrc-page-header">
        <div class="gmrc-page-header__content">
            <p class="gmrc-eyebrow">
                Adventurer’s Register
            </p>

            <h1>
                Edit <?php echo esc_html($currentName); ?>
            </h1>

            <p>
                Amend this adventurer’s registered details within the
                Archive of the Aisles.
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

    <?php if (! empty($flash['success'])) : ?>
        <div
            class="gmrc-alert gmrc-alert--success"
            role="status"
        >
            <?php echo esc_html($flash['success']); ?>
        </div>
    <?php endif; ?>

    <form
        class="gmrc-form gmrc-character-form"
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
            value="<?php echo esc_attr(
                'characters/' . $characterId
            ); ?>"
        >

        <input
            type="hidden"
            name="_method"
            value="PUT"
        >

        <input
            type="hidden"
            name="registration_confirmed"
            value="1"
        >

        <input type="hidden" name="portrait_seed" value="<?php echo esc_attr($portraitSeed); ?>" data-portrait-field="seed">
        <input type="hidden" name="portrait_background" value="<?php echo esc_attr($portraitLayer('background')); ?>" data-portrait-field="background">
        <input type="hidden" name="portrait_body" value="<?php echo esc_attr($portraitLayer('body')); ?>" data-portrait-field="body">
        <input type="hidden" name="portrait_head" value="<?php echo esc_attr($portraitLayer('head')); ?>" data-portrait-field="head">
        <input type="hidden" name="portrait_eyes" value="<?php echo esc_attr($portraitLayer('eyes')); ?>" data-portrait-field="eyes">
        <input type="hidden" name="portrait_mouth" value="<?php echo esc_attr($portraitLayer('mouth')); ?>" data-portrait-field="mouth">
        <input type="hidden" name="portrait_palette" value="<?php echo esc_attr($portraitLayer('palette')); ?>" data-portrait-field="palette">
        <input type="hidden" name="portrait_heritage" value="<?php echo esc_attr($portraitLayer('heritage')); ?>" data-portrait-field="heritage">
        <input type="hidden" name="portrait_outfit" value="<?php echo esc_attr($portraitLayer('outfit')); ?>" data-portrait-field="outfit">
        <input type="hidden" name="portrait_equipment" value="<?php echo esc_attr($portraitLayer('equipment')); ?>" data-portrait-field="equipment">
        <input type="hidden" name="portrait_accessory" value="<?php echo esc_attr($portraitLayer('class_accessory')); ?>" data-portrait-field="class_accessory">
        <input type="hidden" name="portrait_class_effects" value="<?php echo esc_attr($portraitLayer('class_effects')); ?>" data-portrait-field="class_effects">
        <input type="hidden" name="portrait_guild_ornament" value="<?php echo esc_attr($portraitLayer('guild_ornament')); ?>" data-portrait-field="guild_ornament">
        <input type="hidden" name="portrait_frame" value="<?php echo esc_attr($portraitLayer('frame')); ?>" data-portrait-field="frame">
        <input type="hidden" name="portrait_effects" value="<?php echo esc_attr($portraitLayer('effects')); ?>" data-portrait-field="effects">

        <?php
        wp_nonce_field(
            'gmrc_update_character_' . $characterId,
            'gmrc_nonce'
        );
        ?>

        <section class="gmrc-form-section">
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">
                    Registered Identity
                </p>

                <h2>Character details</h2>
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

            <div class="gmrc-form-grid gmrc-form-grid--two">
                <div class="gmrc-form-field">
                    <label for="character-race-display">
                        Race
                    </label>

                    <input
                        id="character-race-display"
                        type="text"
                        value="<?php echo esc_attr(
                            $currentRace->label()
                        ); ?>"
                        readonly
                        aria-describedby="character-race-help"
                    >

                    <p
                        id="character-race-help"
                        class="gmrc-form-help"
                    >
                        Race changes are not yet supported after
                        character creation.
                    </p>
                </div>

                <div class="gmrc-form-field">
                    <label for="character-class-display">
                        Class
                    </label>

                    <input
                        id="character-class-display"
                        type="text"
                        value="<?php echo esc_attr(
                            $currentClass->label()
                        ); ?>"
                        readonly
                        aria-describedby="character-class-help"
                    >

                    <p
                        id="character-class-help"
                        class="gmrc-form-help"
                    >
                        Class changes are not yet supported after
                        character creation.
                    </p>
                </div>
            </div>
        </section>

        <section class="gmrc-form-section gmrc-private-studio" aria-labelledby="gmrc-private-studio-title">
            <header class="gmrc-private-studio__header">
                <p class="gmrc-eyebrow">Guild Illuminator</p>
                <h2 id="gmrc-private-studio-title">The Illuminator’s Private Studio</h2>
                <p>
                    Adjust the generated Guild portrait beneath this
                    adventurer’s record, then save the page when it is ready.
                </p>
            </header>

            <div class="gmrc-private-studio__workspace">
                <div class="gmrc-private-studio__portrait">
                    <?php
                    echo $this->component(
                        'components.media.illuminated-portrait',
                        [
                            'portrait' => $portrait,
                            'portraitPersisted' => true,
                            'controlsEnabled' => true,
                        ]
                    );
                    ?>
                </div>

                <div class="gmrc-private-studio__controls" data-private-studio-controls>
                    <p class="gmrc-private-studio__waiting">
                        ✦ The Illuminator is arranging the portrait tools…
                    </p>
                </div>
            </div>

            <aside class="gmrc-private-studio__note">
                <span aria-hidden="true">✦</span>
                <p>
                    <strong>Custom uploaded portraits stay untouched.</strong>
                    These controls amend the generated Guild illustration
                    underneath, ready for whenever it is restored.
                </p>
            </aside>
        </section>

        <section class="gmrc-form-section">
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">
                    Personal History
                </p>

                <h2>Choose a background</h2>

                <p>
                    A background determines skill proficiencies,
                    language opportunities and practical training.
                </p>
            </header>

            <fieldset
                class="gmrc-background-selector"
                <?php echo $backgroundError !== null
                    ? 'aria-describedby="background-error"'
                    : ''; ?>
            >
                <legend class="screen-reader-text">
                    Character background
                </legend>

                <div class="gmrc-background-grid">
                    <?php foreach ($backgroundOptions as $background) : ?>
                        <?php
                        $identifier = $background->value();

                        $isSelected =
                            $identifier === $backgroundValue;

                        $skills = array_map(
                            $identifierLabel,
                            $background
                                ->skillProficiencies()
                                ->proficiencies()
                        );

                        $tools = array_map(
                            static function (
                                string $tool
                            ) use ($identifierLabel): string {
                                if (
                                    ToolProficiency::supports(
                                        $tool
                                    )
                                ) {
                                    return ToolProficiency
                                        ::fromString($tool)
                                        ->label();
                                }

                                return $identifierLabel($tool);
                            },
                            $background
                                ->toolProficiencyIdentifiers()
                        );
                        ?>

                        <label
                            class="
                                gmrc-background-option
                                <?php echo $isSelected
                                    ? 'gmrc-background-option--selected'
                                    : ''; ?>
                            "
                            data-background-option
                            aria-current="<?php echo $isSelected
                                ? 'true'
                                : 'false'; ?>"
                            for="<?php echo esc_attr(
                                'background-' . $identifier
                            ); ?>"
                        >
                            <input
                                id="<?php echo esc_attr(
                                    'background-' . $identifier
                                ); ?>"
                                class="gmrc-background-option__input"
                                type="radio"
                                name="background"
                                value="<?php echo esc_attr(
                                    $identifier
                                ); ?>"
                                data-background-label="<?php echo esc_attr(
                                    $background->label()
                                ); ?>"
                                data-language-choices="<?php echo esc_attr(
                                    (string) $background->languageChoices()
                                ); ?>"
                                data-needs-artisan-tools="<?php echo in_array(
                                    ToolProficiency::CATEGORY_ARTISANS_TOOLS,
                                    $background->toolProficiencyIdentifiers(),
                                    true
                                ) ? '1' : '0'; ?>"
                                data-needs-gaming-set="<?php echo in_array(
                                    ToolProficiency::CATEGORY_GAMING_SET,
                                    $background->toolProficiencyIdentifiers(),
                                    true
                                ) ? '1' : '0'; ?>"
                                <?php checked(
                                    $isSelected
                                ); ?>
                                required
                            >

                            <span
                                class="gmrc-background-option__image"
                                aria-hidden="true"
                            >
                                <span
                                    class="
                                        gmrc-background-option__monogram
                                    "
                                >
                                    <?php echo esc_html(
                                        strtoupper(
                                            substr(
                                                $background->label(),
                                                0,
                                                1
                                            )
                                        )
                                    ); ?>
                                </span>
                            </span>

                            <span
                                class="gmrc-background-option__heading"
                            >
                                <strong
                                    class="
                                        gmrc-background-option__title
                                    "
                                >
                                    <?php echo esc_html(
                                        $background->label()
                                    ); ?>
                                </strong>

                                <span
                                    class="
                                        gmrc-background-option__control
                                    "
                                    aria-hidden="true"
                                ></span>
                            </span>

                            <span
                                class="gmrc-background-option__summary"
                            >
                                <?php echo esc_html(
                                    implode(', ', $skills)
                                ); ?>
                            </span>

                            <span
                                class="gmrc-background-option__details"
                                data-background-details
                                <?php echo $isSelected
                                    ? ''
                                    : 'hidden'; ?>
                            >
                                <span
                                    class="
                                        gmrc-background-option__detail
                                    "
                                >
                                    <strong>Skill proficiencies</strong>

                                    <span>
                                        <?php echo esc_html(
                                            $skills !== []
                                                ? implode(
                                                    ', ',
                                                    $skills
                                                )
                                                : 'None'
                                        ); ?>
                                    </span>
                                </span>

                                <span
                                    class="
                                        gmrc-background-option__detail
                                    "
                                >
                                    <strong>Language choices</strong>

                                    <span>
                                        <?php echo esc_html(
                                            (string) $background
                                                ->languageChoices()
                                        ); ?>
                                    </span>
                                </span>

                                <span
                                    class="
                                        gmrc-background-option__detail
                                    "
                                >
                                    <strong>Tool proficiencies</strong>

                                    <span>
                                        <?php echo esc_html(
                                            $tools !== []
                                                ? implode(
                                                    ', ',
                                                    $tools
                                                )
                                                : 'None'
                                        ); ?>
                                    </span>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <?php if ($backgroundError !== null) : ?>
                <p
                    id="background-error"
                    class="gmrc-form-error"
                    role="alert"
                >
                    <?php echo esc_html(
                        $backgroundError
                    ); ?>
                </p>
            <?php endif; ?>

            <p class="gmrc-form-help">
                Background artwork will be added to these selection
                cards later. The monogram currently reserves the same
                visual space.
            </p>
        </section>

        <aside
            class="gmrc-form-notice"
            data-background-preview
        >
            <h2>Selected background benefits</h2>

            <?php foreach ($backgroundOptions as $background) : ?>
                <?php
                $identifier = $background->value();

                $isSelected =
                    $identifier === $backgroundValue;

                $skills = array_map(
                    $identifierLabel,
                    $background
                        ->skillProficiencies()
                        ->proficiencies()
                );

                $tools = array_map(
                    static function (
                        string $tool
                    ) use ($identifierLabel): string {
                        return ToolProficiency::supports($tool)
                            ? ToolProficiency
                                ::fromString($tool)
                                ->label()
                            : $identifierLabel($tool);
                    },
                    $background
                        ->toolProficiencyIdentifiers()
                );
                ?>

                <div
                    data-background-preview-panel="<?php
                    echo esc_attr($identifier);
                    ?>"
                    <?php echo $isSelected
                        ? ''
                        : 'hidden'; ?>
                >
                    <h3>
                        <?php echo esc_html(
                            $background->label()
                        ); ?>
                    </h3>

                    <dl class="gmrc-definition-list">
                        <div>
                            <dt>Skill proficiencies</dt>

                            <dd>
                                <?php echo esc_html(
                                    $skills !== []
                                        ? implode(', ', $skills)
                                        : 'None'
                                ); ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Language choices</dt>

                            <dd>
                                <?php echo esc_html(
                                    (string) $background
                                        ->languageChoices()
                                ); ?>
                            </dd>
                        </div>

                        <div>
                            <dt>Tool proficiencies</dt>

                            <dd>
                                <?php echo esc_html(
                                    $tools !== []
                                        ? implode(', ', $tools)
                                        : 'None'
                                ); ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            <?php endforeach; ?>
        </aside>

        <section class="gmrc-form-section gmrc-registration-stage" data-registration-choices>
            <header class="gmrc-form-section__header">
                <p class="gmrc-eyebrow">Registrar’s Choices</p>
                <h2>Complete background choices</h2>
                <p>Resolve languages or generic tool categories granted by the amended background.</p>
            </header>

            <div class="gmrc-registration-choice" data-language-slot="1" hidden>
                <label for="edit-registration-language-1">First language</label>
                <select id="edit-registration-language-1" name="language_1">
                    <option value="">Choose a language</option>
                    <?php foreach ($languageOptions as $language) : ?>
                        <option value="<?php echo esc_attr($language->value()); ?>" <?php selected($languageOneValue, $language->value()); ?>>
                            <?php echo esc_html($language->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="gmrc-registration-choice" data-language-slot="2" hidden>
                <label for="edit-registration-language-2">Second language</label>
                <select id="edit-registration-language-2" name="language_2">
                    <option value="">Choose a different language</option>
                    <?php foreach ($languageOptions as $language) : ?>
                        <option value="<?php echo esc_attr($language->value()); ?>" <?php selected($languageTwoValue, $language->value()); ?>>
                            <?php echo esc_html($language->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="gmrc-registration-choice" data-tool-choice="artisan" hidden>
                <label for="edit-registration-artisan-tool">Artisan’s Tool</label>
                <select id="edit-registration-artisan-tool" name="artisan_tool">
                    <option value="">Choose an artisan’s tool</option>
                    <?php foreach ($artisanToolOptions as $tool) : ?>
                        <option value="<?php echo esc_attr($tool->value()); ?>" <?php selected($artisanToolValue, $tool->value()); ?>>
                            <?php echo esc_html($tool->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="gmrc-registration-choice" data-tool-choice="gaming" hidden>
                <label for="edit-registration-gaming-set">Gaming Set</label>
                <select id="edit-registration-gaming-set" name="gaming_set">
                    <option value="">Choose a gaming set</option>
                    <?php foreach ($gamingSetOptions as $tool) : ?>
                        <option value="<?php echo esc_attr($tool->value()); ?>" <?php selected($gamingSetValue, $tool->value()); ?>>
                            <?php echo esc_html($tool->label()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <p class="gmrc-registration-complete-note" data-registration-no-extra-choices hidden>
                ✦ This background has no unresolved Registrar choices.
            </p>
        </section>

        <aside class="gmrc-form-notice">
            <h2>Current adventuring record</h2>

            <dl class="gmrc-definition-list">
                <div>
                    <dt>Level</dt>

                    <dd>
                        <?php echo esc_html(
                            (string) $character
                                ->level()
                                ->value()
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Experience</dt>

                    <dd>
                        <?php echo esc_html(
                            (string) $character
                                ->experience()
                                ->value()
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Race</dt>

                    <dd>
                        <?php echo esc_html(
                            $currentRace->label()
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Class</dt>

                    <dd>
                        <?php echo esc_html(
                            $currentClass->label()
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Current background</dt>

                    <dd>
                        <?php echo esc_html(
                            $currentBackground->label()
                        ); ?>
                    </dd>
                </div>
            </dl>
        </aside>

        <div class="gmrc-form-actions">
            <?php
            echo $this->component(
                'components.controls.wax-button',
                [
                    'label' => 'Save Changes',
                    'type' => 'submit',
                    'symbol' => '✦',
                    'variant' => 'wax',
                    'size' => 'large',
                ]
            );

            echo $this->component(
                'components.controls.paper-button',
                [
                    'label' => 'Cancel',
                    'href' => $characterUrl,
                    'symbol' => '‹',
                    'variant' => 'parchment',
                    'size' => 'large',
                ]
            );

            echo $this->component(
                'components.controls.paper-button',
                [
                    'label' => 'Return to Register',
                    'href' => $charactersUrl,
                    'symbol' => '☰',
                    'variant' => 'parchment',
                    'size' => 'large',
                ]
            );
            ?>
        </div>
    </form>
</section>
