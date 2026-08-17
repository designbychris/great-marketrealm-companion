<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyMembership;

defined('ABSPATH') || exit;

$members = is_array($members ?? null)
    ? $members
    : [];

$label = isset($label) && is_scalar($label)
    ? trim((string) $label)
    : 'Fellowship portrait';

$limit = isset($limit)
    ? max(1, min(6, (int) $limit))
    : 5;

$variant = isset($variant) && is_scalar($variant)
    ? sanitize_key((string) $variant)
    : 'compact';

if (! in_array($variant, ['compact', 'company'], true)) {
    $variant = 'compact';
}

$visibleMembers = array_slice(
    $members,
    0,
    $limit
);

$overflow = max(
    0,
    count($members) - count($visibleMembers)
);
?>

<div
    class="gmrc-fellowship-portrait"
    role="img"
    aria-label="<?php echo esc_attr($label); ?>"
    data-fellowship-portrait
    data-fellowship-variant="<?php echo esc_attr($variant); ?>"
    data-fellowship-size="<?php echo esc_attr(
        (string) count($visibleMembers)
    ); ?>"
>
    <?php if ($visibleMembers === []) : ?>
        <div class="gmrc-fellowship-portrait__empty">
            <span aria-hidden="true">✦</span>
            <strong>Awaiting adventurers</strong>
            <small>The Guild Illuminator has prepared the frame.</small>
        </div>
    <?php else : ?>
        <div
            class="gmrc-fellowship-portrait__company"
            aria-hidden="true"
        >
            <?php foreach ($visibleMembers as $index => $member) : ?>
                <?php
                $membership = $member['membership'] ?? null;
                $character = $member['character'] ?? null;
                $portrait = $member['portrait'] ?? null;
                $missing = (bool) ($member['missing'] ?? false);

                $isLeader =
                    $membership instanceof PartyMembership
                    && $membership->role()->isLeader();

                $name = $character instanceof Character
                    ? $character->name()->value()
                    : 'Unrecorded Adventurer';

                $classes = [
                    'gmrc-fellowship-portrait__member',
                    'gmrc-fellowship-portrait__member--'
                        . ($index + 1),
                ];

                if ($isLeader) {
                    $classes[] =
                        'gmrc-fellowship-portrait__member--leader';
                }

                if ($missing) {
                    $classes[] =
                        'gmrc-fellowship-portrait__member--missing';
                }
                ?>
                <div class="<?php echo esc_attr(
                    implode(' ', $classes)
                ); ?>">
                    <div
                        class="gmrc-fellowship-portrait__canvas"
                        title="<?php echo esc_attr($name); ?>"
                    >
                        <?php if (
                            $portrait instanceof PortraitViewModel
                            && $portrait->isCustom()
                            && $portrait->attachmentUrl() !== null
                        ) : ?>
                            <img
                                src="<?php echo esc_url(
                                    $portrait->attachmentUrl()
                                ); ?>"
                                alt=""
                                loading="lazy"
                                decoding="async"
                            >
                        <?php elseif (
                            $portrait instanceof PortraitViewModel
                            && $portrait->svg() !== ''
                        ) : ?>
                            <?php
                            echo $portrait->svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        <?php else : ?>
                            <span
                                class="gmrc-fellowship-portrait__placeholder"
                            >
                                <?php echo esc_html(
                                    strtoupper(
                                        substr($name, 0, 1)
                                    )
                                ); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($isLeader) : ?>
                        <span
                            class="gmrc-fellowship-portrait__leader-seal"
                            title="Fellowship Leader"
                        >
                            ✦
                        </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($overflow > 0) : ?>
            <span
                class="gmrc-fellowship-portrait__overflow"
                aria-hidden="true"
            >
                +<?php echo esc_html((string) $overflow); ?>
            </span>
        <?php endif; ?>
    <?php endif; ?>

    <span
        class="gmrc-fellowship-portrait__ornament"
        aria-hidden="true"
    >
        ❧
    </span>
</div>
