<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

$fellowships = is_array($fellowships ?? null)
    ? $fellowships
    : [];

$companionUrl = home_url('/companion/');
$createUrl = add_query_arg(
    'gmrc_route',
    'parties/create',
    $companionUrl
);

$flash = is_array($flash ?? null)
    ? $flash
    : [];

ob_start();
?>

<section class="gmrc-fellowship-register">
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

    <?php
    $ledger = 'The Guild Ledger';
    $volume = 'Volume II';
    $title = 'The Fellowship Register';
    $description =
        'Where lone adventurers become companies, companions become '
        . 'legends, and somebody inevitably forgets the snacks.';
    $level = 1;
    $ornament = '❧';

    require GMRC_PATH
        . 'app/Views/components/furniture/chapter-heading.php';
    ?>

    <aside class="gmrc-fellowship-auby-note">
        <span class="gmrc-fellowship-auby-note__seal" aria-hidden="true">
            🍆
        </span>
        <div>
            <strong>Auby, Keeper of the Kingdoms</strong>
            <p>
                “One adventurer is a record. Several adventurers with snacks
                are a Fellowship.”
            </p>
        </div>
    </aside>

    <?php if ($fellowships === []) : ?>
        <section class="gmrc-fellowship-empty">
            <div class="gmrc-fellowship-empty__seal" aria-hidden="true">
                ✦
            </div>
            <p class="gmrc-eyebrow">An unwritten company</p>
            <h2>Your Fellowship Register awaits its first entry</h2>
            <p>
                Gather your registered adventurers beneath one Guild banner.
                Their existing Illuminator portraits will assemble here as
                the Fellowship grows.
            </p>
            <a
                class="gmrc-fellowship-button gmrc-fellowship-button--primary"
                href="<?php echo esc_url($createUrl); ?>"
            >
                Form a Fellowship
            </a>
        </section>
    <?php else : ?>
        <div class="gmrc-fellowship-grid">
            <?php foreach ($fellowships as $fellowship) : ?>
                <?php
                $party = $fellowship['party'] ?? null;
                $members = is_array($fellowship['members'] ?? null)
                    ? $fellowship['members']
                    : [];

                if (! $party instanceof Party) {
                    continue;
                }

                $partyUrl = add_query_arg(
                    'gmrc_route',
                    'parties/' . rawurlencode(
                        $party->id()->value()
                    ),
                    $companionUrl
                );

                $leaders = array_filter(
                    $members,
                    static fn (array $member): bool =>
                        isset($member['membership'])
                        && $member['membership']
                            ->role()
                            ->isLeader()
                );

                $leader = reset($leaders);
                $leaderCharacter = is_array($leader)
                    ? ($leader['character'] ?? null)
                    : null;
                ?>
                <article class="gmrc-fellowship-entry">
                    <div class="gmrc-fellowship-entry__portrait">
                        <?php echo $this->component(
                            'components.media.fellowship-portrait',
                            [
                                'members' => $members,
                                'label' =>
                                    'Portrait of '
                                    . $party->name()->value(),
                                'limit' => 5,
                            ]
                        ); ?>
                    </div>

                    <div class="gmrc-fellowship-entry__content">
                        <p class="gmrc-eyebrow">Registered Fellowship</p>
                        <h2>
                            <?php echo esc_html(
                                $party->name()->value()
                            ); ?>
                        </h2>

                        <dl class="gmrc-fellowship-entry__facts">
                            <div>
                                <dt>Adventurers</dt>
                                <dd>
                                    <?php echo esc_html(
                                        (string) $party->memberCount()
                                    ); ?>
                                </dd>
                            </div>

                            <div>
                                <dt>Fellowship Leader</dt>
                                <dd>
                                    <?php echo esc_html(
                                        $leaderCharacter !== null
                                            ? $leaderCharacter
                                                ->name()
                                                ->value()
                                            : 'Awaiting appointment'
                                    ); ?>
                                </dd>
                            </div>
                        </dl>

                        <a
                            class="gmrc-fellowship-button"
                            href="<?php echo esc_url($partyUrl); ?>"
                        >
                            Open Fellowship
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <a
                class="gmrc-fellowship-entry gmrc-fellowship-entry--new"
                href="<?php echo esc_url($createUrl); ?>"
            >
                <span class="gmrc-fellowship-entry--new__seal" aria-hidden="true">
                    +
                </span>
                <strong>Form another Fellowship</strong>
                <small>Begin a fresh company in the Guild Register.</small>
            </a>
        </div>
    <?php endif; ?>
</section>

<?php
$registerContent = ob_get_clean();

$partyContent = $this->component(
    'components.furniture.ledger-section',
    [
        'eyebrow' => 'Guild Fellowship Archive',
        'title' => 'Your Adventuring Companies',
        'description' =>
            'Open a registered Fellowship or gather a new company.',
        'ornament' => '❧',
        'content' => $registerContent,
    ]
);

ob_start();

$content = $partyContent;
$side = 'single';
$class = 'gmrc-fellowship-register-page';
$spine = true;

require GMRC_PATH
    . 'app/Views/components/furniture/guild-page.php';

$ledgerContent = ob_get_clean();

$content = $ledgerContent;
$layout = 'single';
$class = 'gmrc-fellowship-ledger';

require GMRC_PATH
    . 'app/Views/components/furniture/guild-ledger.php';
