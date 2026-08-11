<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$note = trim(
    (string) (
        $note
        ?? __(
            'I left your Guild Journal open for you. '
            . 'I was absolutely not reading it.',
            'great-marketrealm-companion'
        )
    )
);

$sceneBase =
    GMRC_URL
    . 'assets/images/auby/desk/scenes/';

$scenePath =
    GMRC_PATH
    . 'assets/images/auby/desk/scenes/';

$initialScene = 'afternoon';

$initialSceneRelative =
    'high-resolution/auby-desk-'
    . $initialScene
    . '-hires.webp';

$initialSceneFile =
    $scenePath
    . $initialSceneRelative;

$initialSceneUrl =
    $sceneBase
    . $initialSceneRelative;

if (is_file($initialSceneFile)) {
    $initialSceneUrl .= '?ver='
        . (string) filemtime($initialSceneFile);
}
?>
<section class="gmrc-auby-desk" data-auby-desk data-guild-hall-daypart="<?php echo esc_attr($initialScene); ?>" data-auby-scene-base="<?php echo esc_url($sceneBase); ?>" style="<?php echo esc_attr( '--gmrc-auby-desk-scene: url("'. $initialSceneUrl . '");' ); ?>" aria-labelledby="gmrc-auby-desk-title" >
    <span class="gmrc-auby-desk__window-glow" data-auby-ambient="window-glow" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__lamp-glow" data-auby-ambient="lamp-glow" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__steam gmrc-auby-desk__steam--one" data-auby-ambient="steam" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__steam gmrc-auby-desk__steam--two" data-auby-ambient="steam" aria-hidden="true"></span>
    <span class="gmrc-auby-desk__dust" data-auby-ambient="dust" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></span>
    <span class="gmrc-auby-desk__stars" data-auby-ambient="stars" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
    <span class="gmrc-auby-desk__sleep" data-auby-ambient="sleep" aria-hidden="true">Z<small>z</small><em>z</em></span>

    <div
        class="gmrc-living-guild"
        data-living-guild
        data-living-guild-manifest="<?php echo esc_url(
            GMRC_URL
            . 'assets/data/guild-hall/'
            . 'living-guild.json'
        ); ?>"
        aria-hidden="true"
    >
        <span
            class="gmrc-living-guild__page"
            data-living-guild-beat="page-flutter"
        ></span>

        <span
            class="gmrc-living-guild__quill"
            data-living-guild-beat="quill-nudge"
        ></span>

        <span
            class="gmrc-living-guild__mouse"
            data-living-guild-beat="market-mouse"
        >
            <span class="gmrc-living-guild__mouse-ear"></span>
            <span class="gmrc-living-guild__mouse-eye"></span>
        </span>

        <span
            class="gmrc-living-guild__thumbprint"
            data-living-guild-beat="purple-thumbprint"
        ></span>

        <button
            class="gmrc-living-guild__coin"
            type="button"
            data-living-guild-beat="copper-coin"
            data-living-guild-coin
            tabindex="-1"
            aria-label="<?php echo esc_attr__(
                'Pick up the copper coin',
                'great-marketrealm-companion'
            ); ?>"
        >
            <span aria-hidden="true">¢</span>
        </button>
    </div>

    <p
        class="gmrc-living-guild__status"
        data-living-guild-status
        aria-live="polite"
    ></p>

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
