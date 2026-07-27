<?php

defined('ABSPATH') || exit;

$characters = $characters ?? [];

$companionUrl = home_url('/companion/');
?>

<section class="gmrc-characters">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">
            Characters Kingdom
        </p>

        <h1>Your adventurers</h1>

        <p>
            Create and manage the heroes who journey through the Great
            Marketrealm.
        </p>

        <a
            class="gmrc-button"
            href="<?php echo esc_url(
                add_query_arg(
                    'gmrc_route',
                    'characters/create',
                    $companionUrl
                )
            ); ?>"
        >
            Create another character
        </a>
    </header>

    <?php if ($characters === []) : ?>
        <section class="gmrc-empty-state">
            <div
                class="gmrc-empty-state__icon"
                aria-hidden="true"
            >
                ♙
            </div>

            <div>
                <h2>No adventurers have arrived yet</h2>

                <p>
                    Create your first hero and begin their journey through
                    the Great Marketrealm.
                </p>

                <a
                    class="gmrc-button"
                    href="<?php echo esc_url(
                        add_query_arg(
                            'gmrc_route',
                            'characters/create',
                            $companionUrl
                        )
                    ); ?>"
                >
                    Create your first character
                </a>
            </div>
        </section>
    <?php else : ?>
        <div class="gmrc-character-grid">
            <?php foreach ($characters as $character) : ?>
                <?php
                $characterId = $character->id();

                $viewUrl = add_query_arg(
                    'gmrc_route',
                    sprintf(
                        'characters/%d',
                        $characterId
                    ),
                    $companionUrl
                );

                $editUrl = add_query_arg(
                    'gmrc_route',
                    sprintf(
                        'characters/%d/edit',
                        $characterId
                    ),
                    $companionUrl
                );
                ?>

                <article class="gmrc-character-card">
                    <div class="gmrc-character-card__portrait">
                        <div
                            class="gmrc-character-card__portrait-placeholder"
                            aria-hidden="true"
                        >
                            <?php
                            echo esc_html(
                                strtoupper(
                                    substr(
                                        $character->name(),
                                        0,
                                        1
                                    )
                                )
                            );
                            ?>
                        </div>

                        <span class="gmrc-character-card__level">
                            Level
                            <?php echo esc_html(
                                (string) $character->level()
                            ); ?>
                        </span>
                    </div>

                    <div class="gmrc-character-card__content">
                        <p class="gmrc-character-card__eyebrow">
                            Adventurer
                        </p>

                        <h2 class="gmrc-character-card__title">
                            <?php echo esc_html(
                                $character->name()
                            ); ?>
                        </h2>

                        <dl class="gmrc-character-card__details">
                            <div>
                                <dt>Race</dt>

                                <dd>
                                    <?php echo esc_html(
                                        $character->race()
                                    ); ?>
                                </dd>
                            </div>

                            <div>
                                <dt>Class</dt>

                                <dd>
                                    <?php echo esc_html(
                                        $character->class()
                                    ); ?>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <footer class="gmrc-character-card__actions">
                        <a
                            class="gmrc-button gmrc-button--small"
                            href="<?php echo esc_url($viewUrl); ?>"
                        >
                            View
                        </a>

                        <a
                            class="gmrc-button gmrc-button--secondary gmrc-button--small"
                            href="<?php echo esc_url($editUrl); ?>"
                        >
                            Edit
                        </a>
                    </footer>
                </article>
            <?php endforeach; ?>

            <a
                class="gmrc-character-card gmrc-character-card--create"
                href="<?php echo esc_url(
                    add_query_arg(
                        'gmrc_route',
                        'characters/create',
                        $companionUrl
                    )
                ); ?>"
            >
                <span
                    class="gmrc-character-card--create__icon"
                    aria-hidden="true"
                >
                    +
                </span>

                <span>
                    <strong>Create another character</strong>

                    <small>
                        Bring a new adventurer into the Marketrealm.
                    </small>
                </span>
            </a>
        </div>
    <?php endif; ?>
</section>
