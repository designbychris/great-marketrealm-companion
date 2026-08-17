<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyMembership;

defined('ABSPATH') || exit;

$membership = $membership ?? null;
$character = $character ?? null;
$portrait = $portrait ?? null;
$partyId = isset($partyId) && is_scalar($partyId)
    ? (string) $partyId
    : '';

if (! $membership instanceof PartyMembership) {
    return;
}

$characterId = $membership
    ->characterId()
    ->value();

$isLeader = $membership
    ->role()
    ->isLeader();

$name = $character instanceof Character
    ? $character->name()->value()
    : 'Unrecorded Adventurer';

$race = $character instanceof Character
    ? $character->race()->label()
    : 'Character record unavailable';

$class = $character instanceof Character
    ? $character->characterClass()->label()
    : 'Unknown calling';

$level = $character instanceof Character
    ? $character->level()->value()
    : null;

$characterUrl = $character instanceof Character
    ? add_query_arg(
        'gmrc_route',
        'characters/' . rawurlencode($characterId),
        home_url('/companion/')
    )
    : null;
?>

<article
    class="
        gmrc-fellowship-member
        <?php echo $isLeader
            ? 'gmrc-fellowship-member--leader'
            : ''; ?>
    "
>
    <div class="gmrc-fellowship-member__portrait">
        <?php if (
            $portrait instanceof PortraitViewModel
            && $portrait->isCustom()
            && $portrait->attachmentUrl() !== null
        ) : ?>
            <img
                src="<?php echo esc_url(
                    $portrait->attachmentUrl()
                ); ?>"
                alt="<?php echo esc_attr(
                    'Guild portrait of ' . $name
                ); ?>"
                loading="lazy"
                decoding="async"
            >
        <?php elseif (
            $portrait instanceof PortraitViewModel
            && $portrait->svg() !== ''
        ) : ?>
            <div
                role="img"
                aria-label="<?php echo esc_attr(
                    'Guild portrait of ' . $name
                ); ?>"
            >
                <?php
                echo $portrait->svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
            </div>
        <?php else : ?>
            <div
                class="gmrc-fellowship-member__portrait-placeholder"
                aria-hidden="true"
            >
                <?php echo esc_html(
                    strtoupper(substr($name, 0, 1))
                ); ?>
            </div>
        <?php endif; ?>

        <?php if ($isLeader) : ?>
            <span
                class="gmrc-fellowship-member__leader-seal"
                title="Fellowship Leader"
            >
                ✦
            </span>
        <?php endif; ?>
    </div>

    <div class="gmrc-fellowship-member__body">
        <p class="gmrc-eyebrow">
            <?php echo $isLeader
                ? 'Fellowship Leader'
                : 'Registered Companion'; ?>
        </p>

        <h3><?php echo esc_html($name); ?></h3>

        <?php if ($membership->office()->isAssigned()) : ?>
            <p class="gmrc-fellowship-member__office">
                <span aria-hidden="true">
                    <?php echo esc_html(
                        $membership->office()->glyph()
                    ); ?>
                </span>
                <?php echo esc_html(
                    $membership->office()->label()
                ); ?>
            </p>
        <?php endif; ?>

        <p class="gmrc-fellowship-member__identity">
            <?php echo esc_html($race); ?>
            <span aria-hidden="true">·</span>
            <?php echo esc_html($class); ?>
            <?php if ($level !== null) : ?>
                <span aria-hidden="true">·</span>
                Level <?php echo esc_html((string) $level); ?>
            <?php endif; ?>
        </p>

        <div class="gmrc-fellowship-member__actions">
            <?php if ($characterUrl !== null) : ?>
                <a
                    class="gmrc-fellowship-button gmrc-fellowship-button--quiet"
                    href="<?php echo esc_url($characterUrl); ?>"
                >
                    Open Ledger
                </a>
            <?php endif; ?>

            <form
                action="<?php echo esc_url(
                    admin_url('admin-post.php')
                ); ?>"
                method="post"
                class="gmrc-fellowship-inline-form"
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
                        'parties/' . $partyId
                        . '/members/' . $characterId
                        . '/role'
                    ); ?>"
                >
                <input type="hidden" name="_method" value="PUT">

                <?php wp_nonce_field(
                    'gmrc_party_members_' . $partyId,
                    'gmrc_nonce'
                ); ?>

                <label>
                    <span class="screen-reader-text">
                        Role for <?php echo esc_html($name); ?>
                    </span>
                    <select
                        name="role"
                        aria-label="<?php echo esc_attr(
                            'Fellowship role for ' . $name
                        ); ?>"
                    >
                        <option
                            value="member"
                            <?php selected(
                                $membership->role()->value(),
                                'member'
                            ); ?>
                        >
                            Member
                        </option>
                        <option
                            value="leader"
                            <?php selected(
                                $membership->role()->value(),
                                'leader'
                            ); ?>
                        >
                            Leader
                        </option>
                    </select>
                </label>

                <button
                    class="gmrc-fellowship-button gmrc-fellowship-button--small"
                    type="submit"
                >
                    Save role
                </button>
            </form>

            <form
                action="<?php echo esc_url(
                    admin_url('admin-post.php')
                ); ?>"
                method="post"
                class="gmrc-fellowship-inline-form"
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
                        'parties/' . $partyId
                        . '/members/' . $characterId
                        . '/office'
                    ); ?>"
                >
                <input type="hidden" name="_method" value="PUT">

                <?php wp_nonce_field(
                    'gmrc_party_members_' . $partyId,
                    'gmrc_nonce'
                ); ?>

                <label>
                    <span class="screen-reader-text">
                        Company Office for <?php echo esc_html($name); ?>
                    </span>
                    <select
                        name="office"
                        aria-label="<?php echo esc_attr(
                            'Company Office for ' . $name
                        ); ?>"
                    >
                        <?php foreach ([
                            'none' => 'No office',
                            'quartermaster' => 'Quartermaster',
                            'chronicler' => 'Chronicler',
                            'pathfinder' => 'Pathfinder',
                            'standard-bearer' => 'Standard Bearer',
                        ] as $officeValue => $officeLabel) : ?>
                            <option
                                value="<?php echo esc_attr(
                                    $officeValue
                                ); ?>"
                                <?php selected(
                                    $membership->office()->value(),
                                    $officeValue
                                ); ?>
                            >
                                <?php echo esc_html($officeLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <button
                    class="
                        gmrc-fellowship-button
                        gmrc-fellowship-button--small
                    "
                    type="submit"
                >
                    Save office
                </button>
            </form>

            <form
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
                        'parties/' . $partyId
                        . '/members/' . $characterId
                    ); ?>"
                >
                <input type="hidden" name="_method" value="DELETE">

                <?php wp_nonce_field(
                    'gmrc_party_members_' . $partyId,
                    'gmrc_nonce'
                ); ?>

                <button
                    class="
                        gmrc-fellowship-button
                        gmrc-fellowship-button--small
                        gmrc-fellowship-button--danger-quiet
                    "
                    type="submit"
                >
                    Remove
                </button>
            </form>
        </div>
    </div>
</article>
