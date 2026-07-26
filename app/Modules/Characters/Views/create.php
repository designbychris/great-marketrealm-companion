<?php

defined('ABSPATH') || exit;

$old = is_array($old ?? null)
    ? $old
    : [];

$errors = $errors ?? null;

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
$levelError = $fieldError('level');
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
            <label for="character-name">
                Character name
                <span aria-hidden="true">*</span>
            </label>

            <input
                id="character-name"
                name="name"
                type="text"
                value="<?php echo esc_attr(
                    $old['name'] ?? ''
                ); ?>"
                maxlength="100"
                autocomplete="off"
                required
                <?php if ($nameError !== null) : ?>
                    aria-invalid="true"
                    aria-describedby="character-name-error"
                <?php endif; ?>
            >

            <?php if ($nameError !== null) : ?>
                <p
                    id="character-name-error"
                    class="gmrc-form-error"
                >
                    <?php echo esc_html($nameError); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="gmrc-form-field">
            <label for="character-race">
                Race
                <span aria-hidden="true">*</span>
            </label>

            <input
                id="character-race"
                name="race"
                type="text"
                value="<?php echo esc_attr(
                    $old['race'] ?? ''
                ); ?>"
                maxlength="100"
                autocomplete="off"
                required
                <?php if ($raceError !== null) : ?>
                    aria-invalid="true"
                    aria-describedby="character-race-error"
                <?php endif; ?>
            >

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

            <input
                id="character-class"
                name="class"
                type="text"
                value="<?php echo esc_attr(
                    $old['class'] ?? ''
                ); ?>"
                maxlength="100"
                autocomplete="off"
                required
                <?php if ($classError !== null) : ?>
                    aria-invalid="true"
                    aria-describedby="character-class-error"
                <?php endif; ?>
            >

            <?php if ($classError !== null) : ?>
                <p
                    id="character-class-error"
                    class="gmrc-form-error"
                >
                    <?php echo esc_html($classError); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="gmrc-form-field">
            <label for="character-level">
                Level
                <span aria-hidden="true">*</span>
            </label>

            <input
                id="character-level"
                name="level"
                type="number"
                value="<?php echo esc_attr(
                    $old['level'] ?? 1
                ); ?>"
                min="1"
                max="20"
                step="1"
                inputmode="numeric"
                required
                <?php if ($levelError !== null) : ?>
                    aria-invalid="true"
                    aria-describedby="character-level-help<?php
                        echo $levelError !== null
                            ? ' character-level-error'
                            : '';
                    ?>"
                <?php else : ?>
                    aria-describedby="character-level-help"
                <?php endif; ?>
            >

            <p
                id="character-level-help"
                class="gmrc-form-help"
            >
                Characters may begin between levels 1 and 20.
            </p>

            <?php if ($levelError !== null) : ?>
                <p
                    id="character-level-error"
                    class="gmrc-form-error"
                >
                    <?php echo esc_html($levelError); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="gmrc-form-actions">
            <button
                class="gmrc-button"
                type="submit"
            >
                Create character
            </button>

            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url(add_query_arg('gmrc_route', 'characters', home_url('/companion/') ) ); ?>" >Cancel</a>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.gmrc-character-form');
    
        if (!form) {
            console.log('GMRC form not found');
            return;
        }
    
        console.log('GMRC form found:', form);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
    
        form.addEventListener('submit', function (e) {
            console.log('Submitting to:', form.action);
            console.log('Method:', form.method);
        });
    });
    </script>
</section>
