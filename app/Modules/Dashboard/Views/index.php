<?php

defined('ABSPATH') || exit;

$companionUrl = remove_query_arg('gmrc_route');
$rooms = is_array($rooms ?? null) ? $rooms : [];
?>

<section class="gmrc-guild-hall">
    <header
        class="gmrc-guild-welcome"
        aria-labelledby="gmrc-guild-welcome-title"
    >
        <div class="gmrc-guild-welcome__paper">
            <span
                class="gmrc-guild-welcome__tape gmrc-guild-welcome__tape--left"
                aria-hidden="true"
            ></span>
            <span
                class="gmrc-guild-welcome__tape gmrc-guild-welcome__tape--right"
                aria-hidden="true"
            ></span>

            <p class="gmrc-guild-welcome__eyebrow">
                <span aria-hidden="true">✦</span>
                The Great Marketrealm Companion
                <span aria-hidden="true">✦</span>
            </p>

            <h1
                id="gmrc-guild-welcome-title"
                class="gmrc-guild-welcome__title"
            >
                Welcome back to the Guild Hall.
            </h1>

            <div class="gmrc-guild-welcome__divider" aria-hidden="true">
                <span></span>
                <b>◆</b>
                <span></span>
            </div>

            <p class="gmrc-guild-welcome__message">
                Your Journal is waiting, the Registrar has kept your
                records safe, and Auby appears to have been rearranging
                the desk again.
                <span class="gmrc-guild-welcome__auby-mark" aria-hidden="true">♡</span>
            </p>
        </div>
    </header>

    <?php echo $this->component(
        'components.guild-hall.auby-desk',
        [
            'note' =>
                'I checked the signposts this time. Every open room now '
                . 'goes somewhere useful. I only moved three of them.',
        ]
    ); ?>

    <section
        class="gmrc-guild-hall-directory"
        aria-labelledby="gmrc-guild-hall-directory-title"
    >
        <header class="gmrc-guild-hall-directory__heading">
            <p class="gmrc-guild-hall-room__eyebrow">Your Companion map</p>
            <h2 id="gmrc-guild-hall-directory-title">Choose a Guild Hall room</h2>
            <p>
                Open the records available to your Guild calling. Planned rooms
                are clearly marked and never masquerade as finished features.
            </p>
        </header>

        <nav class="gmrc-guild-hall__rooms" aria-label="Guild Hall directory">
            <?php foreach ($rooms as $room) : ?>
                <?php
                $planned = ! empty($room['planned']);
                $actions = is_array($room['actions'] ?? null)
                    ? $room['actions']
                    : [];
                ?>
                <article
                    class="gmrc-guild-hall-room<?php echo $planned
                        ? ' gmrc-guild-hall-room--planned'
                        : ''; ?>"
                    data-room-key="<?php echo esc_attr((string) ($room['key'] ?? '')); ?>"
                    data-room-symbol="<?php echo esc_attr((string) ($room['symbol'] ?? '✦')); ?>"
                >
                    <span class="gmrc-guild-hall-room__eyebrow">
                        <?php echo esc_html((string) ($room['eyebrow'] ?? 'Guild Hall')); ?>
                    </span>

                    <h2><?php echo esc_html((string) ($room['title'] ?? 'Guild Room')); ?></h2>

                    <p><?php echo esc_html((string) ($room['description'] ?? '')); ?></p>

                    <?php if ($planned) : ?>
                        <span class="gmrc-guild-hall-room__planned" aria-label="Planned feature">
                            Planned
                        </span>
                    <?php elseif ($actions !== []) : ?>
                        <div class="gmrc-guild-hall-room__actions">
                            <?php foreach ($actions as $action) : ?>
                                <?php
                                $route = trim((string) ($action['route'] ?? ''));
                                $label = trim((string) ($action['label'] ?? 'Open'));

                                if ($route === '') {
                                    continue;
                                }

                                $url = add_query_arg('gmrc_route', $route, $companionUrl);
                                ?>
                                <a class="gmrc-guild-hall-room__link" href="<?php echo esc_url($url); ?>">
                                    <?php echo esc_html($label); ?> <span aria-hidden="true">→</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </nav>
    </section>
</section>
