<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$note = trim(
    (string) (
        $note
        ?? __(
            'I left your Guild Journal open for you. I was absolutely not reading it.',
            'great-marketrealm-companion'
        )
    )
);
?>

<section
    class="gmrc-auby-desk"
    data-auby-desk
    data-guild-hall-daypart="day"
    aria-labelledby="gmrc-auby-desk-title"
>
    <div class="gmrc-auby-desk__heading">
        <p class="gmrc-eyebrow">
            <?php esc_html_e(
                "Auby's Desk",
                'great-marketrealm-companion'
            ); ?>
        </p>

        <h2 id="gmrc-auby-desk-title">
            <?php esc_html_e(
                'Someone has been busy.',
                'great-marketrealm-companion'
            ); ?>
        </h2>

        <p
            class="gmrc-auby-desk__status"
            data-auby-desk-status
        >
            <?php esc_html_e(
                'Auby is sorting the Guild records.',
                'great-marketrealm-companion'
            ); ?>
        </p>
    </div>

    <div class="gmrc-auby-desk__scene">
        <picture>
            <img
                class="gmrc-auby-desk__image"
                data-auby-desk-image
                src="<?php echo esc_url(
                    GMRC_URL
                    . 'assets/images/auby/desk/'
                    . 'auby-desk-day.svg'
                ); ?>"
                data-morning="<?php echo esc_url(
                    GMRC_URL
                    . 'assets/images/auby/desk/'
                    . 'auby-desk-morning.svg'
                ); ?>"
                data-day="<?php echo esc_url(
                    GMRC_URL
                    . 'assets/images/auby/desk/'
                    . 'auby-desk-day.svg'
                ); ?>"
                data-evening="<?php echo esc_url(
                    GMRC_URL
                    . 'assets/images/auby/desk/'
                    . 'auby-desk-evening.svg'
                ); ?>"
                data-night="<?php echo esc_url(
                    GMRC_URL
                    . 'assets/images/auby/desk/'
                    . 'auby-desk-night.svg'
                ); ?>"
                alt="<?php echo esc_attr__(
                    'Auby working at his desk in the Guild Hall',
                    'great-marketrealm-companion'
                ); ?>"
                width="640"
                height="420"
            >
        </picture>

        <span
            class="gmrc-auby-desk__lamp-glow"
            aria-hidden="true"
        ></span>

        <span
            class="gmrc-auby-desk__ambient"
            aria-hidden="true"
        ></span>
    </div>

    <div class="gmrc-auby-desk__note">
        <?php echo $this->component(
            'components.auby.sticky-note',
            [
                'title' => __(
                    'Auby left this here',
                    'great-marketrealm-companion'
                ),
                'message' => $note,
                'variant' => 'general',
                'rotation' => 1.2,
            ]
        ); ?>
    </div>
</section>
