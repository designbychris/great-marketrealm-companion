<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

if (! isset($party) || ! $party instanceof Party) {
    return;
}

$characters = is_array($characters ?? null)
    ? $characters
    : [];

$id = $party->id()->value();
$companionUrl = home_url('/companion/');

$availableCharacters = array_values(
    array_filter(
        $characters,
        static fn (mixed $character): bool =>
            $character instanceof Character
            && ! $party->hasMember(
                $character->id()
            )
    )
);
?>

<section class="gmrc-parties-scaffold">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">Fellowship Register</p>
        <h1><?php echo esc_html($party->name()->value()); ?></h1>
        <p>
            <?php echo esc_html((string) $party->memberCount()); ?>
            registered members.
        </p>
    </header>

    <p>
        <a href="<?php echo esc_url(
            add_query_arg(
                'gmrc_route',
                'parties/' . rawurlencode($id) . '/edit',
                $companionUrl
            )
        ); ?>">
            Edit Fellowship
        </a>
    </p>

    <h2>Memberships</h2>

    <?php if ($party->memberships() === []) : ?>
        <p>No adventurers have joined this Fellowship yet.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($party->memberships() as $membership) : ?>
                <?php
                $characterId = $membership->characterId()->value();
                ?>
                <li>
                    <code><?php echo esc_html($characterId); ?></code>
                    —
                    <?php echo esc_html($membership->role()->value()); ?>

                    <form
                        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                        method="post"
                    >
                        <input type="hidden" name="action" value="gmrc_app_request">
                        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr(
                            'parties/' . $id
                            . '/members/' . $characterId
                            . '/role'
                        ); ?>">
                        <input type="hidden" name="_method" value="PUT">

                        <?php wp_nonce_field(
                            'gmrc_party_members_' . $id,
                            'gmrc_nonce'
                        ); ?>

                        <select name="role">
                            <option
                                value="member"
                                <?php selected(
                                    $membership->role()->value(),
                                    'member'
                                ); ?>
                            >Member</option>
                            <option
                                value="leader"
                                <?php selected(
                                    $membership->role()->value(),
                                    'leader'
                                ); ?>
                            >Leader</option>
                        </select>

                        <button type="submit">Change role</button>
                    </form>

                    <form
                        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                        method="post"
                    >
                        <input type="hidden" name="action" value="gmrc_app_request">
                        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr(
                            'parties/' . $id
                            . '/members/' . $characterId
                        ); ?>">
                        <input type="hidden" name="_method" value="DELETE">

                        <?php wp_nonce_field(
                            'gmrc_party_members_' . $id,
                            'gmrc_nonce'
                        ); ?>

                        <button type="submit">Remove member</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Add Adventurer</h2>

    <?php if ($availableCharacters === []) : ?>
        <p>No additional adventurers are available to add.</p>
    <?php else : ?>
        <form
            action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
            method="post"
        >
            <input type="hidden" name="action" value="gmrc_app_request">
            <input type="hidden" name="gmrc_route" value="<?php echo esc_attr(
                'parties/' . $id . '/members'
            ); ?>">

            <?php wp_nonce_field(
                'gmrc_party_members_' . $id,
                'gmrc_nonce'
            ); ?>

            <label>
                Adventurer
                <select name="character_id" required>
                    <?php foreach ($availableCharacters as $character) : ?>
                        <option value="<?php echo esc_attr(
                            $character->id()->value()
                        ); ?>">
                            <?php echo esc_html(
                                $character->name()->value()
                            ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Fellowship role
                <select name="role">
                    <option value="member">Member</option>
                    <option value="leader">Leader</option>
                </select>
            </label>

            <button type="submit">
                Add Adventurer
            </button>
        </form>
    <?php endif; ?>
</section>
