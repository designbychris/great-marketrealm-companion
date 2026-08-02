<?php

defined('ABSPATH') || exit;

$old = is_array($old ?? null)
    ? $old
    : [];

$errors = is_array($errors ?? null)
    ? $errors
    : [];

$flash = is_array($flash ?? null)
    ? $flash
    : [];

$raceOptions = is_array($raceOptions ?? null)
    ? $raceOptions
    : [];

$classOptions = is_array($classOptions ?? null)
    ? $classOptions
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

$nameError = $fieldError('name');
$raceError = $fieldError('race');
$classError = $fieldError('class');

$charactersUrl = add_query_arg(
    'gmrc_route',
    'characters',
    home_url('/companion/')
);
?>

<section class="gmrc-character-creator">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">
            Characters Kingdom
        </p>

        <h1>Create an adventurer</h1>

        <p>
            Begin a new tale by registering an adventurer of the
            Great Marketrealm.
        </p>
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
            value="characters"
        >

        <?php
        wp_nonce_field(
            'gmrc_create_character',
            'gmrc_nonce'
        );
        ?>

        <div class="gmrc-form-field">
            <?php
            echo $this->component(
                'components.controls.scribe-input',
                [
                    'name' => 'name',
                    'label' => 'Character name',
                    'value' => $old['name'] ?? '',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Record the adventurer\'s name',
                    'error' => $nameError,
                ]
            );
            ?>
        </div>

        <div class="gmrc-form-field">
            <label for="character-race">
                Race
                <span aria-hidden="true">*</span>
            </label>

            <select
                id="character-race"
                name="race"
                required
                <?php if ($raceError !== null) : ?>
                    aria-invalid="true"
                    aria-describedby="character-race-error"
                <?php endif; ?>
            >
                <option value="">
                    Choose a race
                </option>

                <?php foreach ($raceOptions as $value => $label) : ?>
                    <option
                        value="<?php echo esc_attr($value); ?>"
                        <?php selected(
                            $old['race'] ?? '',
                            $value
                        ); ?>
                    >
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($raceError !== null) : ?>
                <p
                    id="character-race-error"
                    class="gmrc-form-error"
                >
                    <?php echo esc_html($raceError); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="gmrc-form-field">
            <label for="character-class">
                Class
                <span aria-hidden="true">*</span>
            </label>

            <select
                id="character-class"
                name="class"
                required
                <?php if ($classError !== null) : ?>
                    aria-invalid="true"
                    aria-describedby="character-class-error"
                <?php endif; ?>
            >
                <option value="">
                    Choose a class
                </option>

                <?php foreach ($classOptions as $value => $label) : ?>
                    <option
                        value="<?php echo esc_attr($value); ?>"
                        <?php selected(
                            $old['class'] ?? '',
                            $value
                        ); ?>
                    >
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($classError !== null) : ?>
                <p
                    id="character-class-error"
                    class="gmrc-form-error"
                >
                    <?php echo esc_html($classError); ?>
                </p>
            <?php endif; ?>
        </div>

        <p class="gmrc-form-help">
            New adventurers begin at Level 1 with no experience.
        </p>

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
            ?>

            <?php
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
