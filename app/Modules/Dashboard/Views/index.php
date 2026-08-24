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

$marketPassUrl = add_query_arg(
    'gmrc_route',
    'market-pass',
    $companionUrl
);

$activeCampaignsUrl = add_query_arg(
    'gmrc_route',
    'active-campaigns',
    $companionUrl
);
?>

<section class="gmrc-guild-hall">
    <header
        class="gmrc-guild-welcome"
        aria-labelledby="gmrc-guild-welcome-title"
    >
        <div class="gmrc-guild-welcome__paper">

            <span
                class="gmrc-guild-welcome__tape
                    gmrc-guild-welcome__tape--left"
                aria-hidden="true"
            ></span>

            <span
                class="gmrc-guild-welcome__tape
                    gmrc-guild-welcome__tape--right"
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

            <div
                class="gmrc-guild-welcome__divider"
                aria-hidden="true"
            >
                <span></span>
                <b>◆</b>
                <span></span>
            </div>

            <p class="gmrc-guild-welcome__message">
                Your Journal is waiting, the Registrar has kept your
                records safe, and Auby appears to have been rearranging
                the desk again.

                <span
                    class="gmrc-guild-welcome__auby-mark"
                    aria-hidden="true"
                >♡</span>
            </p>

        </div>
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

        <?php if (\GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile::accountType(get_current_user_id()) === \GreatMarketrealmCompanion\Modules\GuildGate\AccountType::PLAYER) : ?>
        <article
            class="gmrc-guild-hall-room"
            data-room-symbol="🗺"
        >
            <span class="gmrc-guild-hall-room__eyebrow">Your adventuring tables</span>
            <h2>Active Campaigns</h2>
            <p>See the Campaigns you have joined and your place at each table.</p>
            <a class="gmrc-guild-hall-room__link" href="<?php echo esc_url($activeCampaignsUrl); ?>">Open Active Campaigns →</a>
        </article>

        <article
            class="gmrc-guild-hall-room"
            data-room-symbol="🎟"
        >
            <span class="gmrc-guild-hall-room__eyebrow">Campaign invitation</span>
            <h2>Market Pass</h2>
            <p>Have a code from your Dungeon Master? Redeem it to join their Campaign roster.</p>
            <a class="gmrc-guild-hall-room__link" href="<?php echo esc_url($marketPassUrl); ?>">Redeem a Market Pass →</a>
        </article>

        <?php endif; ?>

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
