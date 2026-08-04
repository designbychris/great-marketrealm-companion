<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;

defined('ABSPATH') || exit;

$old = is_array($old ?? null)
    ? $old
    : [];

$errors = $errors ?? [];

$flash = is_array($flash ?? null)
    ? $flash
    : [];

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

        <aside class="gmrc-form-notice">
            <h2>The first page of the adventure</h2>

            <p>
                New adventurers begin at Level 1 with no experience.
                Their starting hit points are calculated from their
                chosen class and Constitution.
            </p>
        </aside>

        <div class="gmrc-form-actions">
            <?php
            echo $this->component(
                'components.controls.wax-button',
                [
                    'label' => 'Record Adventurer',
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
