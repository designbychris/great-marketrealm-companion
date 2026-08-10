(function (window, document) {
    'use strict';

    const MAX_HISTORY = 6;

    const secureD20 = function () {
        if (
            window.crypto
            && typeof window.crypto.getRandomValues === 'function'
        ) {
            const range = 0x100000000;
            const limit = range - (range % 20);
            const values = new Uint32Array(1);
            let value = limit;

            while (value >= limit) {
                window.crypto.getRandomValues(values);
                value = values[0];
            }

            return (value % 20) + 1;
        }

        return Math.floor(Math.random() * 20) + 1;
    };

    const secureDie = function (sides) {
        const safeSides = Math.max(2, Number(sides) || 20);
        if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
            const range = 0x100000000;
            const limit = range - (range % safeSides);
            const values = new Uint32Array(1);
            let value = limit;
            while (value >= limit) { window.crypto.getRandomValues(values); value = values[0]; }
            return (value % safeSides) + 1;
        }
        return Math.floor(Math.random() * safeSides) + 1;
    };

    const rollFormula = function (formula, critical) {
        const match = String(formula || '').trim().match(/^(\d+)d(\d+)$/i);
        if (!match) { return null; }
        const count = Math.max(1, Number(match[1]));
        const sides = Math.max(2, Number(match[2]));
        const dice = [];
        const totalDice = critical ? count * 2 : count;
        for (let i = 0; i < totalDice; i += 1) { dice.push(secureDie(sides)); }
        return { dice: dice, total: dice.reduce(function (sum, value) { return sum + value; }, 0), sides: sides };
    };

    const signed = function (value) {
        const numeric = Number(value) || 0;
        return numeric >= 0 ? '+' + numeric : String(numeric);
    };

    const rollMode = function (mode) {
        const first = secureD20();

        if (mode === 'normal') {
            return {
                natural: first,
                dice: [first]
            };
        }

        const second = secureD20();

        return {
            natural: mode === 'advantage'
                ? Math.max(first, second)
                : Math.min(first, second),
            dice: [first, second]
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
        const result = tray.querySelector('[data-guild-dice-result]');
        const die = tray.querySelector('[data-guild-d20]');
        const dieValue = tray.querySelector('[data-guild-d20-value]');
        const modeNode = tray.querySelector('[data-guild-dice-mode]');
        const mathNode = tray.querySelector('[data-guild-dice-math]');
        const totalNode = tray.querySelector('[data-guild-dice-total]');
        const aubyNode = tray.querySelector('[data-guild-dice-auby]');
        const history = tray.querySelector('[data-guild-dice-history]');
        const historyList = tray.querySelector('[data-guild-dice-history-list]');
        const live = tray.querySelector('[data-guild-dice-live]');
        const close = tray.querySelector('[data-guild-dice-close]');
        const modeButtons = Array.from(
            tray.querySelectorAll('[data-guild-roll-mode]')
        );

        let activeTrigger = null;
        const recent = [];

        const current = function () {
            if (!(activeTrigger instanceof HTMLButtonElement)) {
                return null;
            }

            return {
                label: activeTrigger.dataset.rollLabel || 'D20 Roll',
                modifier: Number(activeTrigger.dataset.rollModifier) || 0,
                kind: activeTrigger.dataset.rollKind || 'check',
                formula: activeTrigger.dataset.rollFormula || '',
                damageType: activeTrigger.dataset.rollDamageType || '',
                resultSuffix: activeTrigger.dataset.rollResultSuffix || ''
            };
        };

        const paintHistory = function () {
            if (!(history instanceof HTMLElement) || !(historyList instanceof HTMLOListElement)) {
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

            if (result instanceof HTMLElement) {
                result.hidden = true;
            }

            if (aubyNode instanceof HTMLElement) {
                aubyNode.hidden = true;
                aubyNode.textContent = '';
            }

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

        const animateDie = function (
            value,
            natural = null
        ) {
            if (die instanceof HTMLElement) {
                die.classList.remove(
                    'is-rolling',
                    'is-natural-20',
                    'is-natural-1'
                );

                void die.offsetWidth;

                die.classList.add(
                    'is-rolling'
                );

                if (natural === 20) {
                    die.classList.add(
                        'is-natural-20'
                    );
                } else if (natural === 1) {
                    die.classList.add(
                        'is-natural-1'
                    );
                }
            }

            if (dieValue instanceof HTMLElement) {
                dieValue.textContent = String(value);
            }
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
                const damage = rollFormula(selection.formula, false);
                if (!damage) { return; }
                const total = damage.total + selection.modifier;

                animateDie(
                    damage.total
                );

                const isHealing =
                    selection.kind === 'healing';

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
                if (mathNode instanceof HTMLElement) { mathNode.textContent = selection.formula + ' (' + damage.dice.join(' + ') + ') ' + signed(selection.modifier); }
                if (totalNode instanceof HTMLElement) { totalNode.textContent = '= ' + total + ' ' + resultLabel; }
                if (result instanceof HTMLElement) { result.hidden = false; }
                if (aubyNode instanceof HTMLElement) { aubyNode.hidden = true; aubyNode.textContent = ''; }
                const historyText = selection.label + ': ' + damage.dice.join(' + ') + ' ' + signed(selection.modifier) + ' = ' + total + ' ' + resultLabel;
                recent.unshift(historyText); recent.splice(MAX_HISTORY); paintHistory();
                if (live instanceof HTMLElement) { live.textContent = historyText; }
                return;
            }

            const rolled = rollMode(mode);
            const total = rolled.natural + selection.modifier;
            const modeLabel = mode === 'advantage'
                ? 'Advantage'
                : mode === 'disadvantage'
                    ? 'Disadvantage'
                    : 'Normal Roll';

            animateDie(
                rolled.natural,
                rolled.natural
            );

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
                totalNode.textContent = '= ' + total + (selection.resultSuffix ? ' ' + selection.resultSuffix : '');
            }

            if (aubyNode instanceof HTMLElement) {
                if (rolled.natural === 20) {
                    aubyNode.textContent = selection.kind === 'attack' ? '“Critical hit! Double the weapon dice!” — Auby' : '“I definitely witnessed that.” — Auby';
                    aubyNode.hidden = false;
                } else if (rolled.natural === 1) {
                    aubyNode.textContent = '“The Guild Records will say: an attempt was made.” — Auby';
                    aubyNode.hidden = false;
                } else {
                    aubyNode.hidden = true;
                    aubyNode.textContent = '';
                }
            }

            if (result instanceof HTMLElement) {
                result.hidden = false;
            }

            const historyText = selection.label
                + ': '
                + rolled.natural
                + ' '
                + signed(selection.modifier)
                + ' = '
                + total
                + ' ('
                + modeLabel
                + ')';

            recent.unshift(historyText);
            recent.splice(MAX_HISTORY);
            paintHistory();

            if (live instanceof HTMLElement) {
                live.textContent = historyText;
            }
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
