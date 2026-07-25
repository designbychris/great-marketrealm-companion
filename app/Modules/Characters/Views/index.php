<?php

defined('ABSPATH') || exit;

$characters = $characters ?? [];
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
    </header>

    <?php if ($characters === []) : ?>
        <section class="gmrc-empty-state">
            <div class="gmrc-empty-state__icon" aria-hidden="true">
                ♙
            </div>

            <div>
                <h2>No adventurers have arrived yet</h2>

                <p>
                    Your first character will appear here once the character
                    creator opens its gates.
                </p>

                <a class="gmrc-button" href="<?php echo esc_url(add_query_arg('gmrc_route', 'characters/create', home_url('/') ) ); ?>">Create your first character</a>
            </div>
        </section>
    <?php else : ?>
        <div class="gmrc-character-grid">
            <?php foreach ($characters as $character) : ?>
                <article class="gmrc-character-card">
                    <h2>
                        <?php echo esc_html($character->name()); ?>
                    </h2>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
