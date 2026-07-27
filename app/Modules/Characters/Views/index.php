<?php

defined('ABSPATH') || exit;

$characters = $characters ?? [];

$companionUrl = home_url('/companion/');
?>
<div class="guild-ledger">
    <div class="guild-ledger__pages guild-ledger__pages--single">
        <section class="guild-page guild-page--single">


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

            $initial = strtoupper(
                substr(
                    $character->name(),
                    0,
                    1
                )
            );
            ?>

            <article class="gmrc-character-card">
                <div class="gmrc-character-card__visual">
                    <div
                        class="gmrc-character-card__portrait"
                        aria-hidden="true"
                    >
                        <?php echo esc_html($initial); ?>
                    </div>

                    <span class="gmrc-character-card__level">
                        Level
                        <?php echo esc_html(
                            (string) $character->level()
                        ); ?>
                    </span>
                </div>

                <div class="gmrc-character-card__body">
                    <p class="gmrc-character-card__kingdom">
                        Marketrealm adventurer
                    </p>

                    <h2 class="gmrc-character-card__name">
                        <?php echo esc_html(
                            $character->name()
                        ); ?>
                    </h2>

                    <p class="gmrc-character-card__summary">
                        Level
                        <?php echo esc_html(
                            (string) $character->level()
                        ); ?>

                        <?php echo esc_html(
                            $character->race()
                        ); ?>

                        <?php echo esc_html(
                            $character->class()
                        ); ?>
                    </p>

                    <div
                        class="gmrc-character-card__divider"
                        aria-hidden="true"
                    ></div>

                    <div class="gmrc-character-card__features">
                        <span>
                            <strong>Character sheet</strong>
                            Ready to explore
                        </span>

                        <span>
                            <strong>Inventory</strong>
                            Coming soon
                        </span>

                        <span>
                            <strong>Achievements</strong>
                            Coming soon
                        </span>
                    </div>
                </div>

                <footer class="gmrc-character-card__footer">
                    <a
                        class="gmrc-button gmrc-character-card__primary"
                        href="<?php echo esc_url($viewUrl); ?>"
                    >
                        View character
                    </a>

                    <a
                        class="gmrc-character-card__edit"
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

            <span class="gmrc-character-card--create__content">
                <strong>Create a character</strong>

                <small>
                    Bring another hero into the Great Marketrealm.
                </small>
            </span>
        </a>
    </div>
<?php endif; ?>
</section>
</section>
</div>
</div>
