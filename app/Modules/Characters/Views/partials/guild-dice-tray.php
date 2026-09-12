<?php

defined('ABSPATH') || exit;
?>
<aside
        class="gmrc-guild-dice-tray"
        data-guild-dice-tray
        role="region"
        aria-labelledby="gmrc-guild-dice-title"
        aria-describedby="gmrc-guild-dice-accessibility-note"
        hidden
    >
        <div class="gmrc-guild-dice-tray__pin" aria-hidden="true"></div>

        <header class="gmrc-guild-dice-tray__header">
            <div>
                <p class="gmrc-eyebrow">The Guild Dice</p>
                <h2 id="gmrc-guild-dice-title" data-guild-dice-label>D20 Roll</h2>
            </div>

            <button
                class="gmrc-guild-dice-tray__close"
                type="button"
                data-guild-dice-close
                aria-label="Close Guild Dice"
            >×</button>
        </header>

        <p class="gmrc-guild-dice-tray__modifier">
            Modifier
            <strong data-guild-dice-modifier>+0</strong>
        </p>
        <p
            id="gmrc-guild-dice-accessibility-note"
            class="screen-reader-text"
        >
            Dice results are announced after each roll. Visual dice and confetti
            are decorative; critical and failure results are also announced in
            text.
        </p>
        <dl class="gmrc-guild-roll-context" data-guild-roll-context hidden>
            <div><dt>Roll</dt><dd data-guild-context-kind></dd></div>
            <div><dt>Source</dt><dd data-guild-context-source></dd></div>
            <div><dt>Ability</dt><dd data-guild-context-ability></dd></div>
            <div><dt>Training</dt><dd data-guild-context-proficiency></dd></div>
        </dl>

        <section
            class="gmrc-guild-targeting"
            data-guild-targeting
            aria-labelledby="gmrc-guild-targeting-title"
            hidden
        >
            <header>
                <div>
                    <p class="gmrc-eyebrow">Roll Recipient</p>
                    <h3 id="gmrc-guild-targeting-title">Target</h3>
                </div>
                <span data-guild-target-status>No target selected</span>
            </header>

            <label>
                <span>Target kind</span>
                <select data-guild-target-kind>
                    <option value="">No target selected</option>
                    <?php foreach (($rollTargets ?? []) as $target) : ?>
                        <option
                            value="<?php echo esc_attr((string) $target['kind']); ?>"
                            data-target-id="<?php echo esc_attr((string) ($target['id'] ?? '')); ?>"
                            data-target-label="<?php echo esc_attr((string) $target['target_label']); ?>"
                            data-target-resolved="<?php echo ! empty($target['resolved']) ? 'true' : 'false'; ?>"
                        >
                            <?php echo esc_html((string) $target['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label data-guild-target-name-row hidden>
                <span>Target name / label</span>
                <input
                    type="text"
                    maxlength="80"
                    autocomplete="off"
                    placeholder="e.g. Gravy Golem"
                    data-guild-target-name
                >
            </label>

            <p data-guild-target-note>
                Resolved targets may support Vital Application after the roll; reference-only targets remain non-mutating.
            </p>
        </section>

        <button
            type="button"
            class="gmrc-guild-favourite-toggle"
            data-guild-favourite-toggle
            hidden
        >
            <span aria-hidden="true" data-guild-favourite-symbol>☆</span>
            <span data-guild-favourite-label>Add to Quick Rolls</span>
        </button>

        <details
            class="gmrc-guild-situational"
            data-guild-situational-panel
        >
            <summary>
                <span>Situational Adjustment</span>
                <small data-guild-situational-summary>Next roll only</small>
            </summary>

            <div class="gmrc-guild-situational__controls">
                <label>
                    <span>Flat adjustment</span>
                    <input
                        type="number"
                        min="-20"
                        max="20"
                        step="1"
                        value="0"
                        inputmode="numeric"
                        data-guild-situational-flat
                    >
                </label>

                <label>
                    <span>Bonus die</span>
                    <select data-guild-situational-die>
                        <option value="0" selected>None</option>
                        <option value="4">d4</option>
                        <option value="6">d6</option>
                        <option value="8">d8</option>
                        <option value="10">d10</option>
                        <option value="12">d12</option>
                    </select>
                </label>

                <div
                    class="gmrc-guild-situational__shortcuts"
                    aria-label="Common situational adjustments"
                >
                    <button type="button" data-guild-situational-shortcut="-2">−2</button>
                    <button type="button" data-guild-situational-shortcut="-1">−1</button>
                    <button type="button" data-guild-situational-shortcut="1">+1</button>
                    <button type="button" data-guild-situational-shortcut="2">+2</button>
                </div>

                <p>
                    Applied to the next roll only, then cleared automatically.
                    This never changes the adventurer’s certified modifier.
                </p>
            </div>
        </details>

        <section
            class="gmrc-guild-quick-rolls"
            data-guild-quick-rolls
            aria-labelledby="gmrc-guild-quick-rolls-title"
            hidden
        >
            <header class="gmrc-guild-quick-rolls__heading">
                <div>
                    <p class="gmrc-eyebrow">Pinned Favourites</p>
                    <h3 id="gmrc-guild-quick-rolls-title">Quick Rolls</h3>
                </div>
                <span data-guild-quick-roll-count></span>
            </header>
            <div
                class="gmrc-guild-quick-rolls__list"
                data-guild-quick-roll-list
            ></div>
        </section>

        <details class="gmrc-guild-free-roll" data-guild-free-roll-panel>
            <summary>Guild Free Roll</summary>
            <div class="gmrc-guild-free-roll__controls">
                <label>
                    <span>Quantity</span>
                    <input
                        type="number"
                        min="1"
                        max="20"
                        step="1"
                        value="1"
                        inputmode="numeric"
                        data-guild-free-quantity
                    >
                </label>

                <label>
                    <span>Die</span>
                    <select data-guild-free-die>
                        <option value="4">d4</option>
                        <option value="6" selected>d6</option>
                        <option value="8">d8</option>
                        <option value="10">d10</option>
                        <option value="12">d12</option>
                        <option value="20">d20</option>
                        <option value="100">d100</option>
                    </select>
                </label>

                <label>
                    <span>Modifier</span>
                    <input
                        type="number"
                        min="-99"
                        max="99"
                        step="1"
                        value="0"
                        inputmode="numeric"
                        data-guild-free-modifier
                    >
                </label>

                <button
                    type="button"
                    class="gmrc-guild-free-roll__button"
                    data-guild-free-roll
                >
                    Roll Dice
                </button>

                <button
                    type="button"
                    class="gmrc-guild-free-roll__pin"
                    data-guild-free-roll-pin
                >
                    <span aria-hidden="true">☆</span>
                    Save as Quick Roll
                </button>
            </div>
        </details>

        <div
            class="gmrc-guild-dice-modes"
            aria-label="Choose how to roll"
        >
            <button type="button" data-guild-roll-mode="normal">Normal</button>
            <button type="button" data-guild-roll-mode="advantage">Advantage</button>
            <button type="button" data-guild-roll-mode="disadvantage">Disadvantage</button>
        </div>

        <div class="gmrc-guild-dice-result" data-guild-dice-result hidden>
            <div
                class="gmrc-guild-dice-reaction"
                data-guild-dice-reaction
                data-reaction="none"
                aria-hidden="true"
            >
                <span
                    class="gmrc-guild-dice-reaction__banner"
                    data-guild-dice-reaction-banner
                ></span>
                <span
                    class="gmrc-guild-dice-reaction__confetti"
                    data-guild-dice-confetti
                ></span>
            </div>

            <div
                class="gmrc-guild-dice-stage"
                data-guild-dice-stage
                aria-hidden="true"
            ></div>

            <div
                class="gmrc-guild-dice-result__copy"
                data-guild-dice-result-focus
                tabindex="-1"
                aria-label="Guild Dice result"
            >
                <p class="gmrc-guild-dice-result__mode" data-guild-dice-mode></p>
                <p class="gmrc-guild-dice-result__math" data-guild-dice-math></p>
                <strong class="gmrc-guild-dice-result__total" data-guild-dice-total></strong>
                <p
                    class="gmrc-guild-dice-result__target"
                    data-guild-dice-target-result
                    hidden
                ></p>

                <div
                    class="gmrc-guild-vital-application"
                    data-guild-vital-application
                    hidden
                >
                    <p data-guild-vital-application-note></p>
                    <button
                        type="button"
                        data-guild-vital-apply
                        hidden
                    ></button>
                </div>

                <p class="gmrc-guild-dice-result__auby" data-guild-dice-auby hidden></p>

                <div
                    class="gmrc-guild-critical-follow-up"
                    data-guild-critical-follow-up
                    hidden
                >
                    <p>
                        <strong>Critical damage is ready.</strong>
                        Double the weapon dice; keep the flat modifier once.
                    </p>
                    <button
                        type="button"
                        data-guild-critical-damage
                    ></button>
                </div>

            </div>
        </div>

        <div class="gmrc-guild-dice-history" data-guild-dice-history hidden>
            <div class="gmrc-guild-dice-history__heading">
                <div>
                    <p class="gmrc-guild-dice-history__eyebrow">
                        The Dice Ledger
                    </p>
                    <h3>Recent Rolls</h3>
                </div>
                <button
                    type="button"
                    class="gmrc-guild-dice-history__clear"
                    data-guild-dice-history-clear
                >
                    Clear Ledger
                </button>
            </div>
            <p class="gmrc-guild-dice-history__note">
                Kept for this adventurer during this browser session.
            </p>
            <ol data-guild-dice-history-list></ol>
        </div>

        <p
            class="screen-reader-text"
            data-guild-dice-live
            role="status"
            aria-live="polite"
            aria-atomic="true"
        ></p>
    </aside>