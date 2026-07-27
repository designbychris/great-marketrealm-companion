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

            <div class="adventurer-register">
            <?php foreach ($characters as $character) : ?>
                <?php
                require GMRC_PATH
                    . 'app/Views/components/adventurer-card.php';
                ?>
            <?php endforeach; ?>
        
            <a
                class="adventurer-create-entry"
                href="<?php echo esc_url(
                    add_query_arg(
                        'gmrc_route',
                        'characters/create',
                        $companionUrl
                    )
                ); ?>"
            >
                <span
                    class="adventurer-create-entry__icon"
                    aria-hidden="true"
                >
                    ✒
                </span>
        
                <span class="adventurer-create-entry__content">
                    <strong>Inscribe a New Adventurer</strong>
        
                    <small>
                        Prepare a fresh page for another hero of the
                        Great Marketrealm.
                    </small>
                </span>
            </a>
        </div>
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
