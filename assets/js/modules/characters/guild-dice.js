(function (window, document) {
    'use strict';

    const MAX_HISTORY = 6;
    const MAX_FREE_DICE = 20;
    const SUPPORTED_DICE = [4, 6, 8, 10, 12, 20, 100];

    const secureDie = function (sides) {
        const safeSides = Math.max(2, Number(sides) || 20);

        if (
            window.crypto
            && typeof window.crypto.getRandomValues === 'function'
        ) {
            const range = 0x100000000;
            const limit = range - (range % safeSides);
            const values = new Uint32Array(1);
            let value = limit;

            while (value >= limit) {
                window.crypto.getRandomValues(values);
                value = values[0];
            }

            return (value % safeSides) + 1;
        }

        return Math.floor(Math.random() * safeSides) + 1;
    };

    const secureD20 = function () {
        return secureDie(20);
    };

    const normaliseFormula = function (formula) {
        const match = String(formula || '')
            .trim()
            .match(/^(\d+)d(4|6|8|10|12|20|100)$/i);

        if (!match) {
            return null;
        }

        return {
            count: Math.min(MAX_FREE_DICE, Math.max(1, Number(match[1]))),
            sides: Number(match[2])
        };
    };

    const rollFormula = function (formula, critical) {
        const parsed = normaliseFormula(formula);

        if (!parsed) {
            return null;
        }

        const totalDice = critical
            ? parsed.count * 2
            : parsed.count;
        const dice = [];

        for (let index = 0; index < totalDice; index += 1) {
            dice.push(secureDie(parsed.sides));
        }

        return {
            dice: dice,
            total: dice.reduce(function (sum, value) {
                return sum + value;
            }, 0),
            sides: parsed.sides,
            count: totalDice,
            formula: totalDice + 'd' + parsed.sides
        };
    };

    const signed = function (value) {
        const numeric = Number(value) || 0;
        return numeric >= 0 ? '+' + numeric : String(numeric);
    };

    const naturalReaction = function (natural) {
        if (natural === 20) {
            return 'natural-20';
        }

        if (natural === 1) {
            return 'natural-1';
        }

        return 'none';
    };

    const rollMode = function (mode) {
        const first = secureD20();

        if (mode === 'normal') {
            return {
                natural: first,
                dice: [first],
                keptIndex: 0
            };
        }

        const second = secureD20();
        const keepHigher = mode === 'advantage';
        const natural = keepHigher
            ? Math.max(first, second)
            : Math.min(first, second);

        return {
            natural: natural,
            dice: [first, second],
            keptIndex: first === natural ? 0 : 1
        };
    };

    const initialise = function (ledger) {
        if (!(ledger instanceof HTMLElement)) {
            return;
        }

        const tray = ledger.querySelector('[data-guild-dice-tray]');
        const triggers = Array.from(
            ledger.querySelectorAll('[data-guild-roll]')
        );

        if (!(tray instanceof HTMLElement) || triggers.length === 0) {
            return;
        }

        const label = tray.querySelector('[data-guild-dice-label]');
        const modifierNode = tray.querySelector('[data-guild-dice-modifier]');
        const context = tray.querySelector('[data-guild-roll-context]');
        const contextKind = tray.querySelector('[data-guild-context-kind]');
        const contextSource = tray.querySelector('[data-guild-context-source]');
        const contextAbility = tray.querySelector('[data-guild-context-ability]');
        const contextProficiency = tray.querySelector('[data-guild-context-proficiency]');
        const result = tray.querySelector('[data-guild-dice-result]');
        const stage = tray.querySelector('[data-guild-dice-stage]');
        const modeNode = tray.querySelector('[data-guild-dice-mode]');
        const mathNode = tray.querySelector('[data-guild-dice-math]');
        const totalNode = tray.querySelector('[data-guild-dice-total]');
        const aubyNode = tray.querySelector('[data-guild-dice-auby]');
        const vitalTarget = ledger.querySelector('[data-vital-measures]');
        const vitalActions = tray.querySelector('[data-guild-dice-vitals]');
        const applyVitals = tray.querySelector(
            '[data-guild-dice-apply-vitals]'
        );
        const vitalStatus = tray.querySelector(
            '[data-guild-dice-vitals-status]'
        );
        const reaction = tray.querySelector('[data-guild-dice-reaction]');
        const reactionBanner = tray.querySelector(
            '[data-guild-dice-reaction-banner]'
        );
        const confetti = tray.querySelector('[data-guild-dice-confetti]');
        const history = tray.querySelector('[data-guild-dice-history]');
        const historyList = tray.querySelector('[data-guild-dice-history-list]');
        const live = tray.querySelector('[data-guild-dice-live]');
        const close = tray.querySelector('[data-guild-dice-close]');
        const modeButtons = Array.from(
            tray.querySelectorAll('[data-guild-roll-mode]')
        );
        const freePanel = tray.querySelector('[data-guild-free-roll-panel]');
        const freeQuantity = tray.querySelector('[data-guild-free-quantity]');
        const freeDie = tray.querySelector('[data-guild-free-die]');
        const freeModifier = tray.querySelector('[data-guild-free-modifier]');
        const freeRoll = tray.querySelector('[data-guild-free-roll]');

        let activeTrigger = null;
        let pendingVitalResult = null;
        const recent = [];

        const current = function () {
            if (!(activeTrigger instanceof HTMLButtonElement)) {
                return null;
            }

            const panel = activeTrigger.closest('[data-ledger-panel]');

            return {
                label: activeTrigger.dataset.rollLabel || 'D20 Roll',
                modifier: Number(activeTrigger.dataset.rollModifier) || 0,
                kind: activeTrigger.dataset.rollKind || 'check',
                source: activeTrigger.dataset.rollSource || '',
                ability: activeTrigger.dataset.rollAbility || '',
                proficiency: activeTrigger.dataset.rollProficiency || 'none',
                formula: activeTrigger.dataset.rollFormula || '',
                damageType: activeTrigger.dataset.rollDamageType || '',
                resultSuffix: activeTrigger.dataset.rollResultSuffix || '',
                returnTab: panel instanceof HTMLElement
                    ? panel.dataset.ledgerPanel || 'overview'
                    : 'overview'
            };
        };

        const readableKind = function (kind) {
            const labels = {
                'ability-check': 'Ability Check',
                'skill-check': 'Skill Check',
                'saving-throw': 'Saving Throw',
                'initiative': 'Initiative',
                'attack': 'Weapon Attack',
                'spell-attack': 'Spell Attack',
                'damage': 'Damage',
                'healing': 'Healing',
                'check': 'D20 Check',
                'free-roll': 'Free Roll'
            };

            return labels[kind] || 'Guild Roll';
        };

        const readableProficiency = function (proficiency) {
            if (proficiency === 'expertise') {
                return 'Expertise';
            }

            if (proficiency === 'proficient') {
                return 'Proficient';
            }

            return 'Untrained';
        };

        const contextSummary = function (selection) {
            const parts = [readableKind(selection.kind)];

            if (selection.source) {
                parts.push(selection.source);
            }

            if (selection.ability) {
                parts.push(selection.ability);
            }

            if (
                selection.proficiency
                && selection.proficiency !== 'none'
            ) {
                parts.push(readableProficiency(selection.proficiency));
            }

            return parts.join(' · ');
        };

        const paintContext = function (selection) {
            if (!(context instanceof HTMLElement)) {
                return;
            }

            if (!selection) {
                context.hidden = true;
                return;
            }

            if (contextKind instanceof HTMLElement) {
                contextKind.textContent = readableKind(selection.kind);
            }

            if (contextSource instanceof HTMLElement) {
                contextSource.textContent = selection.source || 'Guild Dice';
            }

            if (contextAbility instanceof HTMLElement) {
                contextAbility.textContent = selection.ability || '—';
            }

            if (contextProficiency instanceof HTMLElement) {
                contextProficiency.textContent = readableProficiency(
                    selection.proficiency
                );
            }

            context.hidden = false;
        };

        const paintHistory = function () {
            if (
                !(history instanceof HTMLElement)
                || !(historyList instanceof HTMLOListElement)
            ) {
                return;
            }

            historyList.replaceChildren();

            recent.forEach(function (entry) {
                const item = document.createElement('li');
                item.textContent = entry;
                historyList.appendChild(item);
            });

            history.hidden = recent.length === 0;
        };

        const remember = function (entry) {
            recent.unshift(entry);
            recent.splice(MAX_HISTORY);
            paintHistory();

            if (live instanceof HTMLElement) {
                live.textContent = entry;
            }
        };

        const clearVitalAction = function () {
            pendingVitalResult = null;

            if (vitalActions instanceof HTMLElement) {
                vitalActions.hidden = true;
            }

            if (applyVitals instanceof HTMLButtonElement) {
                applyVitals.textContent = '';
                applyVitals.disabled = false;
            }

            if (vitalStatus instanceof HTMLElement) {
                vitalStatus.textContent = '';
            }
        };

        const prepareVitalAction = function (
            selection,
            amount
        ) {
            clearVitalAction();

            if (
                ! ['damage', 'healing'].includes(selection.kind)
                || amount < 1
                || !(vitalTarget instanceof HTMLElement)
                || !(vitalActions instanceof HTMLElement)
                || !(applyVitals instanceof HTMLButtonElement)
            ) {
                return;
            }

            pendingVitalResult = {
                action: selection.kind,
                amount: amount,
                source: selection.source || selection.label,
                returnTab: selection.returnTab || 'overview'
            };

            const noun = selection.kind === 'healing'
                ? 'Healing'
                : 'Damage';

            applyVitals.textContent = 'Apply '
                + amount
                + ' '
                + noun;
            applyVitals.disabled = false;
            vitalActions.hidden = false;

            if (vitalStatus instanceof HTMLElement) {
                vitalStatus.textContent = 'Ready to apply '
                    + amount
                    + ' '
                    + selection.kind
                    + ' to this adventurer.';
            }
        };

        const clearReaction = function () {
            if (reaction instanceof HTMLElement) {
                reaction.dataset.reaction = 'none';
            }

            if (reactionBanner instanceof HTMLElement) {
                reactionBanner.textContent = '';
            }

            if (confetti instanceof HTMLElement) {
                confetti.replaceChildren();
            }
        };

        const addConfettiPiece = function (index, lonely) {
            if (!(confetti instanceof HTMLElement)) {
                return;
            }

            const piece = document.createElement('i');
            piece.className = 'gmrc-guild-dice-confetti-piece';
            piece.setAttribute('aria-hidden', 'true');
            piece.style.setProperty('--gmrc-confetti-index', String(index));

            if (lonely) {
                piece.classList.add('is-lonely');
            }

            confetti.appendChild(piece);
        };

        const paintReaction = function (natural, kind) {
            clearReaction();

            const state = naturalReaction(natural);

            if (state === 'none') {
                return '';
            }

            if (reaction instanceof HTMLElement) {
                reaction.dataset.reaction = state;
            }

            if (state === 'natural-20') {
                if (reactionBanner instanceof HTMLElement) {
                    reactionBanner.textContent = kind === 'attack'
                        ? 'Natural 20 — Critical Hit!'
                        : 'Natural 20!';
                }

                for (let index = 0; index < 28; index += 1) {
                    addConfettiPiece(index, false);
                }

                return kind === 'attack'
                    ? 'Natural 20. Critical hit.'
                    : 'Natural 20.';
            }

            if (reactionBanner instanceof HTMLElement) {
                reactionBanner.textContent = 'Natural 1 — Oh dear.';
            }

            // One. Lonely. Piece. Of. Confetti.
            addConfettiPiece(0, true);

            return 'Natural 1.';
        };

        const makeDie = function (value, sides, kept) {
            const die = document.createElement('span');
            die.className = 'gmrc-guild-die gmrc-guild-die--d' + sides;
            die.dataset.dieSides = String(sides);
            die.dataset.dieValue = String(value);

            if (kept) {
                die.classList.add('is-kept');
            }

            const valueNode = document.createElement('strong');
            valueNode.textContent = String(value);
            die.appendChild(valueNode);

            return die;
        };

        const paintDice = function (values, sides, keptIndex) {
            if (!(stage instanceof HTMLElement)) {
                return;
            }

            stage.replaceChildren();
            stage.classList.toggle('is-pool', values.length > 4);
            stage.classList.toggle('is-large-pool', values.length > 8);

            values.forEach(function (value, index) {
                stage.appendChild(
                    makeDie(value, sides, keptIndex === index)
                );
            });

            void stage.offsetWidth;
            stage.classList.remove('is-rolling');
            void stage.offsetWidth;
            stage.classList.add('is-rolling');
        };

        const showResult = function () {
            if (result instanceof HTMLElement) {
                result.hidden = false;
            }
        };

        const hideAuby = function () {
            if (aubyNode instanceof HTMLElement) {
                aubyNode.hidden = true;
                aubyNode.textContent = '';
            }
        };

        const openTray = function (trigger) {
            activeTrigger = trigger;
            const selection = current();

            if (!selection) {
                return;
            }

            tray.hidden = false;
            tray.classList.add('is-open');

            if (label instanceof HTMLElement) {
                label.textContent = selection.label;
            }

            if (modifierNode instanceof HTMLElement) {
                modifierNode.textContent = signed(selection.modifier);
            }

            paintContext(selection);

            if (result instanceof HTMLElement) {
                result.hidden = true;
            }

            if (stage instanceof HTMLElement) {
                stage.replaceChildren();
            }

            clearReaction();
            clearVitalAction();
            hideAuby();

            const normal = modeButtons.find(function (button) {
                return button.dataset.guildRollMode === 'normal';
            });

            if (normal instanceof HTMLButtonElement) {
                normal.focus();
            }
        };

        const closeTray = function () {
            tray.classList.remove('is-open');
            tray.hidden = true;

            if (activeTrigger instanceof HTMLButtonElement) {
                activeTrigger.focus();
            }
        };

        const performFormula = function (selection) {
            clearReaction();

            const rolled = rollFormula(selection.formula, false);

            if (!rolled) {
                return;
            }

            paintDice(rolled.dice, rolled.sides, null);

            const total = rolled.total + selection.modifier;
            const isHealing = selection.kind === 'healing';
            const resultLabel = isHealing
                ? 'healing'
                : (
                    selection.damageType
                        ? selection.damageType + ' damage'
                        : 'damage'
                );

            if (modeNode instanceof HTMLElement) {
                modeNode.textContent = isHealing
                    ? 'Healing Roll'
                    : 'Damage Roll';
            }

            if (mathNode instanceof HTMLElement) {
                mathNode.textContent = selection.formula
                    + ' ('
                    + rolled.dice.join(' + ')
                    + ') '
                    + signed(selection.modifier);
            }

            if (totalNode instanceof HTMLElement) {
                totalNode.textContent = '= ' + total + ' ' + resultLabel;
            }

            hideAuby();
            prepareVitalAction(selection, total);
            showResult();

            remember(
                contextSummary(selection)
                + ' — '
                + selection.label
                + ': '
                + rolled.dice.join(' + ')
                + ' '
                + signed(selection.modifier)
                + ' = '
                + total
                + ' '
                + resultLabel
            );
        };

        const performD20 = function (selection, mode) {
            clearVitalAction();

            const rolled = rollMode(mode);
            const total = rolled.natural + selection.modifier;
            const modeLabel = mode === 'advantage'
                ? 'Advantage'
                : mode === 'disadvantage'
                    ? 'Disadvantage'
                    : 'Normal Roll';

            paintDice(rolled.dice, 20, rolled.keptIndex);

            if (modeNode instanceof HTMLElement) {
                modeNode.textContent = modeLabel;
            }

            if (mathNode instanceof HTMLElement) {
                const diceText = rolled.dice.length === 2
                    ? rolled.dice.join(' / ') + ' → ' + rolled.natural
                    : String(rolled.natural);

                mathNode.textContent = diceText + ' ' + signed(selection.modifier);
            }

            if (totalNode instanceof HTMLElement) {
                totalNode.textContent = '= '
                    + total
                    + (
                        selection.resultSuffix
                            ? ' ' + selection.resultSuffix
                            : ''
                    );
            }

            const reactionAnnouncement = paintReaction(
                rolled.natural,
                selection.kind
            );

            if (aubyNode instanceof HTMLElement) {
                if (rolled.natural === 20) {
                    aubyNode.textContent = selection.kind === 'attack'
                        ? '“Critical hit! Double the weapon dice!” — Auby'
                        : '“I definitely witnessed that.” — Auby';
                    aubyNode.hidden = false;
                } else if (rolled.natural === 1) {
                    aubyNode.textContent = '“The Guild has elected not to record that one.” — Auby';
                    aubyNode.hidden = false;
                } else {
                    hideAuby();
                }
            }

            showResult();

            remember(
                contextSummary(selection)
                + ' — '
                + selection.label
                + ': '
                + rolled.natural
                + ' '
                + signed(selection.modifier)
                + ' = '
                + total
                + ' ('
                + modeLabel
                + ')'
                + (
                    reactionAnnouncement
                        ? ' — ' + reactionAnnouncement
                        : ''
                )
            );
        };

        const perform = function (mode) {
            const selection = current();

            if (!selection) {
                return;
            }

            if (
                selection.kind === 'damage'
                || selection.kind === 'healing'
            ) {
                performFormula(selection);
                return;
            }

            performD20(selection, mode);
        };

        const performFreeRoll = function () {
            const requestedQuantity = Number(
                freeQuantity instanceof HTMLInputElement
                    ? freeQuantity.value
                    : 1
            );
            const requestedSides = Number(
                freeDie instanceof HTMLSelectElement
                    ? freeDie.value
                    : 6
            );
            const modifier = Number(
                freeModifier instanceof HTMLInputElement
                    ? freeModifier.value
                    : 0
            ) || 0;

            const quantity = Math.min(
                MAX_FREE_DICE,
                Math.max(1, Math.floor(requestedQuantity || 1))
            );
            const sides = SUPPORTED_DICE.includes(requestedSides)
                ? requestedSides
                : 6;
            const values = [];

            for (let index = 0; index < quantity; index += 1) {
                values.push(secureDie(sides));
            }

            const subtotal = values.reduce(function (sum, value) {
                return sum + value;
            }, 0);
            const total = subtotal + modifier;
            const formula = quantity + 'd' + sides;

            clearReaction();
            clearVitalAction();
            paintDice(values, sides, null);

            const freeReaction = sides === 20 && quantity === 1
                ? paintReaction(values[0], 'free-roll')
                : '';

            if (label instanceof HTMLElement) {
                label.textContent = 'Guild Free Roll';
            }

            paintContext({
                kind: 'free-roll',
                source: formula,
                ability: '',
                proficiency: 'none'
            });

            if (modifierNode instanceof HTMLElement) {
                modifierNode.textContent = signed(modifier);
            }

            if (modeNode instanceof HTMLElement) {
                modeNode.textContent = formula;
            }

            if (mathNode instanceof HTMLElement) {
                mathNode.textContent = values.join(' + ')
                    + ' '
                    + signed(modifier);
            }

            if (totalNode instanceof HTMLElement) {
                totalNode.textContent = '= ' + total;
            }

            hideAuby();

            if (
                sides === 20
                && quantity === 1
                && aubyNode instanceof HTMLElement
            ) {
                if (values[0] === 20) {
                    aubyNode.textContent = '“I definitely witnessed that.” — Auby';
                    aubyNode.hidden = false;
                } else if (values[0] === 1) {
                    aubyNode.textContent = '“The Guild has elected not to record that one.” — Auby';
                    aubyNode.hidden = false;
                }
            }

            showResult();

            remember(
                'Guild Free Roll: '
                + formula
                + ' ('
                + values.join(' + ')
                + ') '
                + signed(modifier)
                + ' = '
                + total
                + (freeReaction ? ' — ' + freeReaction : '')
            );
        };

        triggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                openTray(trigger);
            });
        });

        modeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                perform(button.dataset.guildRollMode || 'normal');
            });
        });

        if (freeRoll instanceof HTMLButtonElement) {
            freeRoll.addEventListener('click', performFreeRoll);
        }

        if (freeQuantity instanceof HTMLInputElement) {
            freeQuantity.addEventListener('change', function () {
                freeQuantity.value = String(
                    Math.min(
                        MAX_FREE_DICE,
                        Math.max(1, Math.floor(Number(freeQuantity.value) || 1))
                    )
                );
            });
        }

        if (freePanel instanceof HTMLDetailsElement) {
            freePanel.addEventListener('toggle', function () {
                if (
                    freePanel.open
                    && freeQuantity instanceof HTMLInputElement
                ) {
                    freeQuantity.focus();
                }
            });
        }

        if (applyVitals instanceof HTMLButtonElement) {
            applyVitals.addEventListener('click', function () {
                if (
                    ! pendingVitalResult
                    || !(vitalTarget instanceof HTMLElement)
                ) {
                    return;
                }

                applyVitals.disabled = true;

                if (vitalStatus instanceof HTMLElement) {
                    vitalStatus.textContent = 'Entering the result into Adventuring Measures…';
                }

                vitalTarget.dispatchEvent(
                    new CustomEvent(
                        'gmrc:vital-apply',
                        {
                            bubbles: false,
                            detail: pendingVitalResult
                        }
                    )
                );
            });
        }

        if (close instanceof HTMLButtonElement) {
            close.addEventListener('click', closeTray);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !tray.hidden) {
                event.preventDefault();
                closeTray();
            }
        });
    };

    const boot = function () {
        document.querySelectorAll('[data-living-ledger]').forEach(initialise);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(window, document);
