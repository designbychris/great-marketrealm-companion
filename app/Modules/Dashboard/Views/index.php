<?php

defined('ABSPATH') || exit;

$companionUrl = remove_query_arg(
    'gmrc_route'
);

$charactersUrl = add_query_arg(
    'gmrc_route',
    'characters',
    $companionUrl
);

$createCharacterUrl = add_query_arg(
    'gmrc_route',
    'characters/create',
    $companionUrl
);
?>

<section class="gmrc-guild-hall">
    <header class="gmrc-guild-hall__welcome">
        <p class="gmrc-eyebrow">
            The Great Marketrealm Companion
        </p>

        <h1>
            Welcome back to the Guild Hall.
        </h1>

        <p>
            Your Journal is waiting, the Registrar has kept your records safe,
            and Auby appears to have been rearranging the desk again.
        </p>
    </header>

    <?php echo $this->component(
        'components.guild-hall.auby-desk',
        [
            'note' =>
                'I left your Guild Journal open for you. '
                . 'I was absolutely not reading it. '
                . 'Also, I found a copper coin under the desk. '
                . 'We should probably work out whose it is.',
        ]
    ); ?>

    <nav
        class="gmrc-guild-hall__rooms"
        aria-label="Guild Hall"
    >
        <article
            class="gmrc-guild-hall-room"
            data-room-symbol="✒"
        >
            <span class="gmrc-guild-hall-room__eyebrow">
                Open now
            </span>

            <h2>Adventurer Register</h2>

            <p>
                Visit your recorded adventurers or inscribe a new Guild member.
            </p>

            <a
                class="gmrc-guild-hall-room__link"
                href="<?php echo esc_url(
                    $charactersUrl
                ); ?>"
            >
                Open the Register →
            </a>

            <br>

            <a
                class="gmrc-guild-hall-room__link"
                href="<?php echo esc_url(
                    $createCharacterUrl
                ); ?>"
            >
                Inscribe an Adventurer →
            </a>
        </article>

        <article
            class="gmrc-guild-hall-room
                gmrc-guild-hall-room--future"
            data-room-symbol="📖"
        >
            <span class="gmrc-guild-hall-room__eyebrow">
                Guild Journal Initiative
            </span>

            <h2>Guild Journal</h2>

            <p>
                Your illuminated character record, portrait and personal archive.
            </p>
        </article>

        <article
            class="gmrc-guild-hall-room
                gmrc-guild-hall-room--future"
            data-room-symbol="🎒"
        >
            <span class="gmrc-guild-hall-room__eyebrow">
                Project Leather Satchel
            </span>

            <h2>Leather Satchel</h2>

            <p>
                Equipment, provisions, coins and suspicious things Auby found
                in the lining.
            </p>
        </article>

        <article
            class="gmrc-guild-hall-room
                gmrc-guild-hall-room--future"
            data-room-symbol="★"
        >
            <span class="gmrc-guild-hall-room__eyebrow">
                Book of Deeds
            </span>

            <h2>Guild Honours</h2>

            <p>
                Achievements, milestones and the occasional very enthusiastic
                Seal of Approval.
            </p>
        </article>
    </nav>
</section>
