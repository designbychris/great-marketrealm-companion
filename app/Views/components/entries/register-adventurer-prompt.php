<?php

defined('ABSPATH') || exit;

$companionUrl = isset($companionUrl)
    && is_string($companionUrl)
        ? $companionUrl
        : home_url('/companion/');

$empty = (bool) ($empty ?? false);

$url = add_query_arg(
    'gmrc_route',
    'characters/create',
    $companionUrl
);

$title = $empty
    ? 'No adventurers have signed the Ledger… yet.'
    : 'Inscribe a New Adventurer';

$description = $empty
    ? 'The Registrar has prepared a fresh page. All that is missing is '
        . 'an adventurer brave — or foolish — enough to put their name on it.'
    : 'Prepare a fresh page for another hero of the Great Marketrealm.';

$button = $empty
    ? 'Register Your First Adventurer'
    : 'Register Another Adventurer';
?>

<section
    class="
        gmrc-register-adventurer-prompt
        <?php echo $empty
            ? 'gmrc-register-adventurer-prompt--empty'
            : 'gmrc-register-adventurer-prompt--compact'; ?>
    "
    aria-labelledby="<?php echo esc_attr(
        $empty
            ? 'gmrc-register-empty-title'
            : 'gmrc-register-another-title'
    ); ?>"
>
    <?php if ($empty) : ?>
        <p class="gmrc-register-adventurer-prompt__eyebrow">
            The Guild Register Awaits
        </p>
    <?php endif; ?>

    <div
        class="gmrc-register-adventurer-prompt__quill"
        aria-hidden="true"
    >
        ✒
    </div>

    <div class="gmrc-register-adventurer-prompt__copy">
        <h2
            id="<?php echo esc_attr(
                $empty
                    ? 'gmrc-register-empty-title'
                    : 'gmrc-register-another-title'
            ); ?>"
        >
            <?php echo esc_html($title); ?>
        </h2>

        <p>
            <?php echo esc_html($description); ?>
        </p>

        <a
            class="
                gmrc-register-adventurer-prompt__action
                wax-button
                wax-button--medium
            "
            href="<?php echo esc_url($url); ?>"
        >
            <span aria-hidden="true">✦</span>
            <?php echo esc_html($button); ?>
        </a>
    </div>

    <?php if ($empty) : ?>
        <blockquote class="gmrc-register-adventurer-prompt__auby">
            <p>
                “I sharpened the quill for you. Well… I tried.”
            </p>

            <footer>— Auby</footer>
        </blockquote>
    <?php endif; ?>
</section>
