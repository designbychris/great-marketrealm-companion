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

$officeHolders = array_values(
    array_filter(
        $members,
        static fn (array $member): bool =>
            isset($member['membership'])
            && $member['membership']->office()->isAssigned()
    )
);

$memberNamesById = [];

foreach ($members as $memberRecord) {
    $memberCharacter = $memberRecord['character'] ?? null;

    if ($memberCharacter instanceof Character) {
        $memberNamesById[
            $memberCharacter->id()->value()
        ] = $memberCharacter->name()->value();
    }
}

$quartermasterName = null;

foreach ($officeHolders as $holder) {
    $membership = $holder['membership'] ?? null;
    $character = $holder['character'] ?? null;

    if (
        $membership !== null
        && $membership->office()->value() === 'quartermaster'
        && $character instanceof Character
    ) {
        $quartermasterName = $character->name()->value();
        break;
    }
}
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

    <div
        class="gmrc-fellowship-tabs"
        data-fellowship-tabs
        data-fellowship-id="<?php echo esc_attr($id); ?>"
    >
        <nav
            class="gmrc-fellowship-tablist"
            aria-label="Fellowship Hall sections"
        >
            <div
                role="tablist"
                aria-label="Fellowship Hall"
                aria-orientation="horizontal"
            >
                <?php foreach ([
                    'overview' => ['▣', 'Overview'],
                    'chronicle' => ['✎', 'Chronicle'],
                    'treasury' => ['◈', 'Treasury'],
                    'company' => ['⚑', 'Company'],
                ] as $hallTab => [$hallIcon, $hallLabel]) : ?>
                    <button
                        id="<?php echo esc_attr(
                            'gmrc-fellowship-tab-' . $hallTab
                        ); ?>"
                        class="gmrc-fellowship-tab<?php echo $hallTab === 'overview'
                            ? ' is-active'
                            : ''; ?>"
                        type="button"
                        role="tab"
                        aria-selected="<?php echo $hallTab === 'overview'
                            ? 'true'
                            : 'false'; ?>"
                        aria-controls="<?php echo esc_attr(
                            'gmrc-fellowship-panel-' . $hallTab
                        ); ?>"
                        tabindex="<?php echo $hallTab === 'overview'
                            ? '0'
                            : '-1'; ?>"
                        data-fellowship-tab="<?php echo esc_attr($hallTab); ?>"
                    >
                        <span
                            class="gmrc-fellowship-tab__icon"
                            aria-hidden="true"
                        >
                            <?php echo esc_html($hallIcon); ?>
                        </span>
                        <span class="gmrc-fellowship-tab__label">
                            <?php echo esc_html($hallLabel); ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </nav>

        <div
            id="gmrc-fellowship-panel-overview"
            class="gmrc-fellowship-tabpanel is-active"
            role="tabpanel"
            aria-labelledby="gmrc-fellowship-tab-overview"
            data-fellowship-panel="overview"
        >
    <header class="gmrc-fellowship-hero">
        <div
            class="gmrc-fellowship-hero__portrait"
            data-standard-palette="<?php echo esc_attr(
                $party->standard()->palette()
            ); ?>"
        >
            <span
                class="gmrc-fellowship-standard-seal"
                aria-hidden="true"
            >
                <?php echo esc_html(
                    $party->standard()->emblemGlyph()
                ); ?>
            </span>
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

            <?php if ($party->charter()->motto() !== '') : ?>
                <p class="gmrc-fellowship-hero__motto">
                    “<?php echo esc_html(
                        $party->charter()->motto()
                    ); ?>”
                </p>
            <?php endif; ?>

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

    <?php if (! $party->charter()->isBlank()) : ?>
        <section
            class="gmrc-fellowship-charter"
            aria-labelledby="gmrc-fellowship-charter-title"
        >
            <header class="gmrc-fellowship-section-heading">
                <div>
                    <p class="gmrc-eyebrow">The company’s own words</p>
                    <h2 id="gmrc-fellowship-charter-title">
                        Company Charter
                    </h2>
                </div>
                <span
                    class="gmrc-fellowship-charter__emblem"
                    aria-hidden="true"
                >
                    <?php echo esc_html(
                        $party->standard()->emblemGlyph()
                    ); ?>
                </span>
            </header>

            <?php if ($party->charter()->description() !== '') : ?>
                <p class="gmrc-fellowship-charter__description">
                    <?php echo esc_html(
                        $party->charter()->description()
                    ); ?>
                </p>
            <?php endif; ?>

            <?php if ($party->charter()->statement() !== '') : ?>
                <div class="gmrc-fellowship-charter__statement">
                    <?php echo wp_kses_post(
                        wpautop(
                            esc_html(
                                $party->charter()->statement()
                            )
                        )
                    ); ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

        </div>

        <div
            id="gmrc-fellowship-panel-chronicle"
            class="gmrc-fellowship-tabpanel"
            role="tabpanel"
            aria-labelledby="gmrc-fellowship-tab-chronicle"
            data-fellowship-panel="chronicle"
        >
    <section
        class="gmrc-fellowship-chronicle"
        aria-labelledby="gmrc-fellowship-chronicle-title"
    >
        <header class="gmrc-fellowship-section-heading">
            <div>
                <p class="gmrc-eyebrow">The company remembers</p>
                <h2 id="gmrc-fellowship-chronicle-title">
                    Company Chronicle
                </h2>
            </div>
            <span class="gmrc-fellowship-count">
                <?php echo esc_html(
                    (string) $party->chronicle()->count()
                ); ?>
                recorded
            </span>
        </header>

        <aside class="gmrc-fellowship-auby-note">
            <span
                class="gmrc-fellowship-auby-note__seal"
                aria-hidden="true"
            >
                🍆
            </span>
            <div>
                <strong>Auby, Acting Guild Historian</strong>
                <p>
                    “If nobody writes down what happened, in six months
                    everyone will insist they defeated the dragon.”
                </p>
            </div>
        </aside>

        <form
            class="gmrc-fellowship-chronicle__form"
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
                    'parties/' . $id . '/chronicle/notes'
                ); ?>"
            >

            <?php wp_nonce_field(
                'gmrc_party_chronicle_' . $id,
                'gmrc_nonce'
            ); ?>

            <div>
                <p class="gmrc-eyebrow">Adventure Notes</p>
                <h3>Record what happened</h3>
                <p>
                    Keep session notes, discoveries, clues, unfinished
                    business and the moments nobody should be allowed to
                    conveniently forget.
                </p>
            </div>

            <label class="gmrc-fellowship-field">
                <span>Entry title</span>
                <input
                    type="text"
                    name="title"
                    maxlength="120"
                    placeholder="e.g. The Pantry Door Was Definitely Trapped"
                    required
                >
            </label>

            <label class="gmrc-fellowship-field">
                <span>Adventure note</span>
                <textarea
                    name="content"
                    rows="7"
                    maxlength="3000"
                    placeholder="Record the Fellowship’s notes from the adventure..."
                    required
                ></textarea>
                <small>
                    Player-written notes are Fellowship records, not
                    Dungeon Master-certified Deeds or Honours.
                </small>
            </label>

            <button
                class="
                    gmrc-fellowship-button
                    gmrc-fellowship-button--primary
                "
                type="submit"
            >
                Add to Chronicle
            </button>
        </form>

        <div class="gmrc-fellowship-chronicle__timeline">
            <?php if ($party->chronicle()->entries() === []) : ?>
                <div class="gmrc-fellowship-empty gmrc-fellowship-empty--compact">
                    <span aria-hidden="true">✎</span>
                    <h3>The first page is still blank</h3>
                    <p>
                        The Fellowship has not recorded an adventure note yet.
                        The Guild Archivist has sharpened a pencil in
                        anticipation.
                    </p>
                </div>
            <?php else : ?>
                <ol>
                    <?php foreach (
                        $party->chronicle()->newestFirst()
                        as $entry
                    ) : ?>
                        <li
                            class="
                                gmrc-fellowship-chronicle-entry
                                <?php echo $entry->isCertified()
                                    ? 'gmrc-fellowship-chronicle-entry--certified'
                                    : ''; ?>
                            "
                        >
                            <div
                                class="gmrc-fellowship-chronicle-entry__marker"
                                aria-hidden="true"
                            >
                                <?php echo esc_html(
                                    $entry->type()->glyph()
                                ); ?>
                            </div>

                            <article>
                                <header>
                                    <div>
                                        <p class="gmrc-eyebrow">
                                            <?php echo esc_html(
                                                $entry->type()->label()
                                            ); ?>
                                            <?php if ($entry->isCertified()) : ?>
                                                · DM Certified
                                            <?php endif; ?>
                                        </p>
                                        <h3>
                                            <?php echo esc_html(
                                                $entry->title()
                                            ); ?>
                                        </h3>
                                    </div>
                                    <time
                                        datetime="<?php echo esc_attr(
                                            $entry
                                                ->recordedAt()
                                                ->format(DATE_ATOM)
                                        ); ?>"
                                    >
                                        <?php echo esc_html(
                                            $entry
                                                ->recordedAt()
                                                ->format('j M Y')
                                        ); ?>
                                    </time>
                                </header>

                                <div class="gmrc-fellowship-chronicle-entry__content">
                                    <?php echo wp_kses_post(
                                        wpautop(
                                            esc_html(
                                                $entry->content()
                                            )
                                        )
                                    ); ?>
                                </div>

                                <footer>
                                    <?php echo esc_html(
                                        $entry
                                            ->provenance()
                                            ->label()
                                    ); ?>
                                </footer>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </section>

        </div>

        <div
            id="gmrc-fellowship-panel-treasury"
            class="gmrc-fellowship-tabpanel"
            role="tabpanel"
            aria-labelledby="gmrc-fellowship-tab-treasury"
            data-fellowship-panel="treasury"
        >
    <section
        class="gmrc-fellowship-treasury"
        aria-labelledby="gmrc-fellowship-treasury-title"
    >
        <header class="gmrc-fellowship-section-heading">
            <div>
                <p class="gmrc-eyebrow">Shared company funds</p>
                <h2 id="gmrc-fellowship-treasury-title">
                    Fellowship Treasury
                </h2>
            </div>
            <?php if ($quartermasterName !== null) : ?>
                <span class="gmrc-fellowship-count">
                    Quartermaster:
                    <?php echo esc_html($quartermasterName); ?>
                </span>
            <?php endif; ?>
        </header>

        <aside class="gmrc-fellowship-auby-note">
            <span
                class="gmrc-fellowship-auby-note__seal"
                aria-hidden="true"
            >
                🍆
            </span>
            <div>
                <strong>Auby’s Treasury Note</strong>
                <p>
                    “A shared purse requires trust, accurate records,
                    and at least one adventurer who can count past twelve.”
                </p>
            </div>
        </aside>

        <div class="gmrc-fellowship-treasury__balance">
            <span>Current company purse</span>
            <strong>
                <?php echo esc_html(
                    $party->treasury()->balance()->formatted()
                ); ?>
            </strong>
        </div>

        <section
            class="gmrc-fellowship-coin-transfer"
            aria-labelledby="gmrc-fellowship-coin-transfer-title"
        >
            <header>
                <div>
                    <p class="gmrc-eyebrow">
                        Coin Between Companions
                    </p>
                    <h3 id="gmrc-fellowship-coin-transfer-title">
                        Transfer with an Adventurer
                    </h3>
                </div>
                <span aria-hidden="true">⇄</span>
            </header>

            <?php
            $coinTransferId = wp_generate_uuid4();
            ?>

            <?php if ($members === []) : ?>
                <p class="gmrc-fellowship-offices__empty">
                    Add an adventurer to the Fellowship before transferring
                    personal coin to or from the shared coffers.
                </p>
            <?php else : ?>
                <form
                    class="gmrc-fellowship-coin-transfer__form"
                    action="<?php echo esc_url(
                        admin_url('admin-post.php')
                    ); ?>"
                    method="post"
                    data-coin-transfer-form
                    aria-describedby="gmrc-fellowship-coin-transfer-help"
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
                            'parties/' . $id
                            . '/treasury/transfer'
                        ); ?>"
                    >

                    <input
                        type="hidden"
                        name="transfer_id"
                        value="<?php echo esc_attr(
                            $coinTransferId
                        ); ?>"
                    >

                    <?php wp_nonce_field(
                        'gmrc_party_coin_transfer_' . $id,
                        'gmrc_nonce'
                    ); ?>

                    <p
                        id="gmrc-fellowship-coin-transfer-help"
                        class="gmrc-fellowship-coin-transfer__help"
                    >
                        Personal purse funds belong to the selected adventurer.
                        Treasury funds belong to the whole Fellowship. A
                        successful transfer moves the same amount from one
                        balance to the other, updates both purses, and records
                        the movement in the Treasury Ledger.
                    </p>

                    <label class="gmrc-fellowship-field">
                        <span>Adventurer</span>
                        <select
                            name="character_id"
                            required
                        >
                            <?php foreach ($members as $member) : ?>
                                <?php
                                $transferCharacter =
                                    $member['character'] ?? null;

                                if (
                                    ! $transferCharacter
                                        instanceof Character
                                ) {
                                    continue;
                                }
                                ?>
                                <option
                                    value="<?php echo esc_attr(
                                        $transferCharacter
                                            ->id()
                                            ->value()
                                    ); ?>"
                                >
                                    <?php echo esc_html(
                                        sprintf(
                                            '%s — purse %s',
                                            $transferCharacter
                                                ->name()
                                                ->value(),
                                            $transferCharacter
                                                ->purse()
                                                ->formatted()
                                        )
                                    ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="gmrc-fellowship-field">
                        <span>Transfer direction</span>
                        <select
                            name="direction"
                            required
                        >
                            <option value="to-treasury">
                                Adventurer → Fellowship Treasury
                            </option>
                            <option value="to-character">
                                Fellowship Treasury → Adventurer
                            </option>
                        </select>
                    </label>

                    <fieldset
                        class="gmrc-fellowship-treasury__coins"
                    >
                        <legend class="screen-reader-text">
                            Transfer amount
                        </legend>
                        <?php foreach ([
                            'gold' => 'GP',
                            'silver' => 'SP',
                            'copper' => 'CP',
                        ] as $coin => $coinLabel) : ?>
                            <label>
                                <span>
                                    <?php echo esc_html($coinLabel); ?>
                                </span>
                                <input
                                    type="number"
                                    name="<?php echo esc_attr($coin); ?>"
                                    value="0"
                                    min="0"
                                    <?php echo $coin === 'gold'
                                        ? 'max="999999"'
                                        : 'max="9"'; ?>
                                    required
                                >
                            </label>
                        <?php endforeach; ?>
                    </fieldset>

                    <label class="gmrc-fellowship-field">
                        <span>Transfer note</span>
                        <input
                            type="text"
                            name="note"
                            maxlength="120"
                            placeholder="e.g. Shares from the Rootlands reward"
                        >
                    </label>

                    <button
                        class="
                            gmrc-fellowship-button
                            gmrc-fellowship-button--primary
                        "
                        type="submit"
                    >
                        Move Coin Between Purses
                    </button>
                </form>
            <?php endif; ?>
        </section>

        <details class="gmrc-fellowship-treasury-adjustments">
            <summary>
                <span>
                    <strong>Company-only Treasury adjustments</strong>
                    <small>
                        Rewards, sales, supplies and other money that does
                        not come from an adventurer’s personal purse.
                    </small>
                </span>
                <span aria-hidden="true">▾</span>
            </summary>

            <div
                class="gmrc-fellowship-treasury-adjustments__notice"
                role="note"
            >
                <strong>These controls do not change a Character’s purse.</strong>
                <p>
                    To move personal money between an adventurer and the
                    Fellowship, use <em>Coin Between Companions</em> above.
                </p>
            </div>

            <div class="gmrc-fellowship-treasury__forms">
                <?php foreach ([
                    'deposit' => [
                        'title' => 'Record External Income',
                        'button' => 'Add Company Funds',
                    ],
                    'withdraw' => [
                        'title' => 'Record Company Expense',
                        'button' => 'Spend Company Funds',
                    ],
                ] as $treasuryAction => $treasuryCopy) : ?>
                    <form
                        class="gmrc-fellowship-treasury__form"
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
                                'parties/' . $id
                                . '/treasury/' . $treasuryAction
                            ); ?>"
                        >

                        <?php wp_nonce_field(
                            'gmrc_party_treasury_' . $id,
                            'gmrc_nonce'
                        ); ?>

                        <h3>
                            <?php echo esc_html(
                                $treasuryCopy['title']
                            ); ?>
                        </h3>

                        <div class="gmrc-fellowship-treasury__coins">
                            <?php foreach ([
                                'gold' => 'GP',
                                'silver' => 'SP',
                                'copper' => 'CP',
                            ] as $coin => $coinLabel) : ?>
                                <label>
                                    <span>
                                        <?php echo esc_html($coinLabel); ?>
                                    </span>
                                    <input
                                        type="number"
                                        name="<?php echo esc_attr($coin); ?>"
                                        value="0"
                                        min="0"
                                        <?php echo $coin === 'gold'
                                            ? 'max="999999"'
                                            : 'max="9"'; ?>
                                        required
                                    >
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <label class="gmrc-fellowship-field">
                            <span>Ledger note</span>
                            <input
                                type="text"
                                name="note"
                                maxlength="160"
                                placeholder="<?php echo esc_attr(
                                    $treasuryAction === 'deposit'
                                        ? 'e.g. Rootlands contract reward'
                                        : 'e.g. Supplies for the Savory Sea voyage'
                                ); ?>"
                            >
                        </label>

                        <button
                            class="
                                gmrc-fellowship-button
                                <?php echo $treasuryAction === 'deposit'
                                    ? 'gmrc-fellowship-button--primary'
                                    : 'gmrc-fellowship-button--quiet'; ?>
                            "
                            type="submit"
                        >
                            <?php echo esc_html(
                                $treasuryCopy['button']
                            ); ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </details>

        <div class="gmrc-fellowship-treasury__ledger">
            <h3>Recent Treasury Ledger</h3>

            <?php if ($party->treasury()->transactions() === []) : ?>
                <p class="gmrc-fellowship-offices__empty">
                    No Treasury transactions have been recorded yet.
                </p>
            <?php else : ?>
                <ol>
                    <?php foreach (
                        $party->treasury()->recent(6)
                        as $transaction
                    ) : ?>
                        <li>
                            <span
                                class="<?php echo esc_attr(
                                    $transaction->isDeposit()
                                        ? 'is-deposit'
                                        : 'is-withdrawal'
                                ); ?>"
                            >
                                <?php echo $transaction->isDeposit()
                                    ? '+'
                                    : '−'; ?>
                                <?php echo esc_html(
                                    $transaction
                                        ->amount()
                                        ->formatted()
                                ); ?>
                            </span>
                            <div>
                                <?php if ($transaction->note() !== '') : ?>
                                    <strong>
                                        <?php echo esc_html(
                                            $transaction->note()
                                        ); ?>
                                    </strong>
                                <?php endif; ?>
                                <?php if (
                                    $transaction
                                        ->isCharacterTransfer()
                                ) : ?>
                                    <small
                                        class="gmrc-fellowship-treasury__transfer-meta"
                                    >
                                        <?php
                                        $transferCharacterName =
                                            $memberNamesById[
                                                $transaction
                                                    ->characterId()
                                            ]
                                            ?? 'Former Fellowship member';
                                        ?>
                                        <?php echo esc_html(
                                            $transaction
                                                ->transferDirection()
                                                === 'to-treasury'
                                                ? 'Adventurer → Fellowship'
                                                : 'Fellowship → Adventurer'
                                        ); ?>
                                        ·
                                        <?php echo esc_html(
                                            $transferCharacterName
                                        ); ?>
                                    </small>
                                <?php endif; ?>
                                <small>
                                    <?php echo esc_html(
                                        $transaction
                                            ->occurredAt()
                                            ->format('j M Y · H:i')
                                    ); ?>
                                </small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </section>

        </div>

        <div
            id="gmrc-fellowship-panel-company"
            class="gmrc-fellowship-tabpanel"
            role="tabpanel"
            aria-labelledby="gmrc-fellowship-tab-company"
            data-fellowship-panel="company"
        >
    <section
        class="gmrc-fellowship-offices"
        aria-labelledby="gmrc-fellowship-offices-title"
    >
        <header class="gmrc-fellowship-section-heading">
            <div>
                <p class="gmrc-eyebrow">Duties beyond the adventuring class</p>
                <h2 id="gmrc-fellowship-offices-title">
                    Company Offices
                </h2>
            </div>
        </header>

        <?php if ($officeHolders === []) : ?>
            <p class="gmrc-fellowship-offices__empty">
                No Company Offices have been appointed yet. The Guild
                suspects everybody is currently hoping somebody else carries
                the inventory ledger.
            </p>
        <?php else : ?>
            <div class="gmrc-fellowship-offices__grid">
                <?php foreach ($officeHolders as $holder) : ?>
                    <?php
                    $officeMembership = $holder['membership'];
                    $officeCharacter = $holder['character'] ?? null;
                    $officeName = $officeCharacter instanceof Character
                        ? $officeCharacter->name()->value()
                        : 'Unrecorded Adventurer';
                    ?>
                    <article class="gmrc-fellowship-office-card">
                        <span
                            class="gmrc-fellowship-office-card__glyph"
                            aria-hidden="true"
                        >
                            <?php echo esc_html(
                                $officeMembership
                                    ->office()
                                    ->glyph()
                            ); ?>
                        </span>
                        <div>
                            <strong>
                                <?php echo esc_html(
                                    $officeMembership
                                        ->office()
                                        ->label()
                                ); ?>
                            </strong>
                            <span>
                                <?php echo esc_html($officeName); ?>
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

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
        </div>
    </div>
</section>
