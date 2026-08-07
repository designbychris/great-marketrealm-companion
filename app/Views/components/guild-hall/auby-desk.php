<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$note = trim((string) ($note ?? __('I left your Guild Journal open for you. I was absolutely not reading it.', 'great-marketrealm-companion')));
$sceneBase = GMRC_URL . 'assets/images/auby/desk/scenes/';
?>
<section class="gmrc-auby-desk" data-auby-desk data-guild-hall-daypart="afternoon" data-auby-scene-base="<?php echo esc_url($sceneBase); ?>" style="<?php echo esc_attr('--gmrc-auby-desk-scene: url(' . $sceneBase . 'high-resolution/auby-desk-afternoon-hires.webp);'); ?>" aria-labelledby="gmrc-auby-desk-title">
    <span class="gmrc-auby-desk__window-glow" data-auby-ambient="window-glow" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__lamp-glow" data-auby-ambient="lamp-glow" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__steam gmrc-auby-desk__steam--one" data-auby-ambient="steam" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__steam gmrc-auby-desk__steam--two" data-auby-ambient="steam" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__dust" data-auby-ambient="dust" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span>
    <span class="gmrc-auby-desk__stars" data-auby-ambient="stars" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
    <span class="gmrc-auby-desk__sleep" data-auby-ambient="sleep" aria-hidden="true">Z<small>z</small><em>z</em></span>
    <div class="gmrc-auby-desk__content">
        <header class="gmrc-auby-desk__heading">
            <p class="gmrc-eyebrow"><?php esc_html_e("Auby's Desk", 'great-marketrealm-companion'); ?></p>
            <h2 id="gmrc-auby-desk-title" data-auby-desk-title><?php esc_html_e('Someone has been busy.', 'great-marketrealm-companion'); ?></h2>
            <p class="gmrc-auby-desk__status" data-auby-desk-status><?php esc_html_e('Auby appears to be thinking very hard about something. Possibly cake.', 'great-marketrealm-companion'); ?></p>
        </header>
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

            <div
                class="gmrc-auby-desk__tea-card"
                aria-live="polite"
            >
                <span
                    class="gmrc-auby-desk__tea-icon"
                    aria-hidden="true"
                >☕</span>

                <p
                    class="gmrc-auby-desk__tea-message"
                    data-auby-tea-message
                >
                    <?php esc_html_e(
                        'Tea is still warm. Probably.',
                        'great-marketrealm-companion'
                    ); ?>
                </p>
            </div>
        </div>
    </div>
</section>
