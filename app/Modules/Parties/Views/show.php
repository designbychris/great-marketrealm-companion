<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

if (! isset($party) || ! $party instanceof Party) {
    return;
}

$members = is_array($members ?? null)
    ? $members
    : [];

$available = is_array($available ?? null)
    ? $available
    : [];

$flash = is_array($flash ?? null)
    ? $flash
    : [];

$id = $party->id()->value();
$companionUrl = home_url('/companion/');
$registerUrl = add_query_arg(
    'gmrc_route',
    'parties',
    $companionUrl
);
$editUrl = add_query_arg(
    'gmrc_route',
    'parties/' . rawurlencode($id) . '/edit',
    $companionUrl
);
?>

<section class="gmrc-fellowship-ledger-page">
    <?php if (! empty($flash['success'])) : ?>
        <div
            class="gmrc-register-notice gmrc-register-notice--success"
            role="status"
        >
            <span aria-hidden="true">✦</span>
            <p><?php echo esc_html($flash['success']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (! empty($flash['error'])) : ?>
        <div
            class="gmrc-register-notice gmrc-register-notice--error"
            role="alert"
        >
            <span aria-hidden="true">!</span>
            <p><?php echo esc_html($flash['error']); ?></p>
        </div>
    <?php endif; ?>

    <header class="gmrc-fellowship-hero">
        <div class="gmrc-fellowship-hero__portrait">
            <?php echo $this->component(
                'components.media.fellowship-portrait',
                [
                    'members' => $members,
                    'label' =>
                        'Company portrait of '
                        . $party->name()->value(),
                    'limit' => 6,
                    'variant' => 'company',
                ]
            ); ?>
        </div>

        <div class="gmrc-fellowship-hero__copy">
            <p class="gmrc-eyebrow">The Fellowship Register</p>
            <h1>
                <?php echo esc_html(
                    $party->name()->value()
                ); ?>
            </h1>

            <p class="gmrc-fellowship-hero__lede">
                A Guild-recognised company of
                <?php echo esc_html(
                    (string) $party->memberCount()
                ); ?>
                adventurer<?php echo $party->memberCount() === 1
                    ? ''
                    : 's'; ?>.
            </p>

            <div class="gmrc-fellowship-hero__actions">
                <a
                    class="gmrc-fellowship-button"
                    href="<?php echo esc_url($registerUrl); ?>"
                >
                    Fellowship Register
                </a>
                <a
                    class="
                        gmrc-fellowship-button
                        gmrc-fellowship-button--quiet
                    "
                    href="<?php echo esc_url($editUrl); ?>"
                >
                    Edit Fellowship
                </a>
            </div>
        </div>
    </header>

    <aside class="gmrc-fellowship-auby-note">
        <span
            class="gmrc-fellowship-auby-note__seal"
            aria-hidden="true"
        >
            🍆
        </span>
        <div>
            <strong>Auby’s company note</strong>
            <p>
                “A Fellowship is strongest when everybody knows their role.
                It also helps if somebody remembers where the map went.”
            </p>
        </div>
    </aside>

    <section
        class="gmrc-fellowship-roster"
        aria-labelledby="gmrc-fellowship-roster-title"
    >
        <header class="gmrc-fellowship-section-heading">
            <div>
                <p class="gmrc-eyebrow">The company assembled</p>
                <h2 id="gmrc-fellowship-roster-title">
                    Fellowship Roster
                </h2>
            </div>
            <span class="gmrc-fellowship-count">
                <?php echo esc_html(
                    (string) $party->memberCount()
                ); ?>
                registered
            </span>
        </header>

        <?php if ($members === []) : ?>
            <div class="gmrc-fellowship-empty gmrc-fellowship-empty--compact">
                <span aria-hidden="true">✦</span>
                <h3>The company is still gathering</h3>
                <p>
                    Add one of your registered adventurers below and the
                    Illuminator will begin assembling the Fellowship portrait.
                </p>
            </div>
        <?php else : ?>
            <div class="gmrc-fellowship-roster__grid">
                <?php foreach ($members as $member) : ?>
                    <?php echo $this->component(
                        'components.entries.fellowship-member',
                        [
                            'membership' =>
                                $member['membership'] ?? null,
                            'character' =>
                                $member['character'] ?? null,
                            'portrait' =>
                                $member['portrait'] ?? null,
                            'partyId' => $id,
                        ]
                    ); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section
        class="gmrc-fellowship-recruit"
        aria-labelledby="gmrc-fellowship-recruit-title"
    >
        <header class="gmrc-fellowship-section-heading">
            <div>
                <p class="gmrc-eyebrow">Open the company ledger</p>
                <h2 id="gmrc-fellowship-recruit-title">
                    Add an Adventurer
                </h2>
            </div>
        </header>

        <?php if ($available === []) : ?>
            <p class="gmrc-fellowship-recruit__complete">
                Every adventurer currently available to this Guild account
                is already part of the Fellowship.
            </p>
        <?php else : ?>
            <form
                class="gmrc-fellowship-recruit__form"
                action="<?php echo esc_url(
                    admin_url('admin-post.php')
                ); ?>"
                method="post"
            >
                <input
                    type="hidden"
                    name="action"
                    value="gmrc_app_request"
                >
                <input
                    type="hidden"
                    name="gmrc_route"
                    value="<?php echo esc_attr(
                        'parties/' . $id . '/members'
                    ); ?>"
                >

                <?php wp_nonce_field(
                    'gmrc_party_members_' . $id,
                    'gmrc_nonce'
                ); ?>

                <label class="gmrc-fellowship-field">
                    <span>Adventurer</span>
                    <select name="character_id" required>
                        <?php foreach ($available as $character) : ?>
                            <?php if (! $character instanceof Character) {
                                continue;
                            } ?>
                            <option
                                value="<?php echo esc_attr(
                                    $character->id()->value()
                                ); ?>"
                            >
                                <?php echo esc_html(
                                    sprintf(
                                        '%s — %s %s, Level %d',
                                        $character->name()->value(),
                                        $character->race()->label(),
                                        $character
                                            ->characterClass()
                                            ->label(),
                                        $character->level()->value()
                                    )
                                ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="gmrc-fellowship-field">
                    <span>Fellowship role</span>
                    <select name="role">
                        <option value="member">Member</option>
                        <option value="leader">Leader</option>
                    </select>
                </label>

                <button
                    class="
                        gmrc-fellowship-button
                        gmrc-fellowship-button--primary
                    "
                    type="submit"
                >
                    Add to Fellowship
                </button>
            </form>
        <?php endif; ?>
    </section>
</section>
