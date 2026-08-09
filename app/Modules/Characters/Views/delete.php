<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

if (
    ! isset($character)
    || ! $character instanceof Character
) {
    return;
}

$characterId = $character
    ->id()
    ->value();

$name = $character
    ->name()
    ->value();

$race = $character
    ->race()
    ->label();

$characterClass = $character
    ->characterClass()
    ->label();

$level = $character
    ->level()
    ->value();

$companionUrl = home_url(
    '/companion/'
);

$characterUrl = add_query_arg(
    'gmrc_route',
    'characters/' . rawurlencode(
        $characterId
    ),
    $companionUrl
);

$charactersUrl = add_query_arg(
    'gmrc_route',
    'characters',
    $companionUrl
);
?>

<section
    class="gmrc-final-farewell"
    aria-labelledby="gmrc-final-farewell-title"
>
    <header class="gmrc-final-farewell__heading">
        <p class="gmrc-eyebrow">
            Character Lifecycle Initiative · Phase I
        </p>

        <h1 id="gmrc-final-farewell-title">
            The Final Farewell
        </h1>

        <p>
            Some Guild records close quietly. This action permanently removes
            the adventurer from your Register and cannot be undone.
        </p>
    </header>

    <div class="gmrc-final-farewell__ledger">
        <div
            class="gmrc-final-farewell__tape
                gmrc-final-farewell__tape--left"
            aria-hidden="true"
        ></div>

        <div
            class="gmrc-final-farewell__tape
                gmrc-final-farewell__tape--right"
            aria-hidden="true"
        ></div>

        <div class="gmrc-final-farewell__identity">
            <?php if (
                isset($portrait)
                && $portrait instanceof PortraitViewModel
            ) : ?>
                <div class="gmrc-final-farewell__portrait">
                    <?php
                    echo $this->component(
                        'components.media.illuminated-portrait',
                        [
                            'portrait' => $portrait,
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>

            <div class="gmrc-final-farewell__record">
                <p class="gmrc-final-farewell__eyebrow">
                    Adventurer’s Register
                </p>

                <h2>
                    <?php echo esc_html($name); ?>
                </h2>

                <p class="gmrc-final-farewell__summary">
                    Level <?php echo esc_html(
                        (string) $level
                    ); ?>
                    <?php echo esc_html($race); ?>
                    <?php echo esc_html(
                        $characterClass
                    ); ?>
                </p>

                <div
                    class="gmrc-final-farewell__warning"
                    role="note"
                >
                    <strong>
                        This is permanent.
                    </strong>

                    <span>
                        The character record and its stored portrait data
                        will be removed from your Companion.
                    </span>
                </div>
            </div>
        </div>

        <p class="gmrc-final-farewell__auby-note">
            “I’ll keep the empty page tidy. Some stories end, and some simply
            make room for the next adventure.”
            <span>— Auby</span>
        </p>

        <form
            class="gmrc-final-farewell__form"
            action="<?php echo esc_url(
                admin_url('admin-post.php')
            ); ?>"
            method="post"
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
                value="DELETE"
            >

            <?php
            wp_nonce_field(
                'gmrc_delete_character_'
                    . $characterId,
                'gmrc_nonce'
            );
            ?>

            <div class="gmrc-final-farewell__actions">
                <?php
                echo $this->component(
                    'components.controls.paper-button',
                    [
                        'label' => 'Keep this Adventurer',
                        'href' => $characterUrl,
                        'symbol' => '‹',
                        'variant' => 'parchment',
                        'size' => 'large',
                    ]
                );

                echo $this->component(
                    'components.controls.paper-button',
                    [
                        'label' =>
                            'Delete ' . $name,
                        'type' => 'submit',
                        'symbol' => '×',
                        'variant' => 'danger',
                        'size' => 'large',
                        'ariaLabel' =>
                            'Permanently delete '
                            . $name,
                    ]
                );
                ?>
            </div>
        </form>

        <a
            class="gmrc-final-farewell__register-link"
            href="<?php echo esc_url(
                $charactersUrl
            ); ?>"
        >
            Return to the Adventurer Register without making changes
        </a>
    </div>
</section>
