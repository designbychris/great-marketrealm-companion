<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
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
