(function (window, document) {
    'use strict';

    const secureD20 = function () {
        if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
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

    const integerValue = function (input) {
        return input instanceof HTMLInputElement
            ? Math.max(0, Number.parseInt(input.value || '0', 10) || 0)
            : 0;
    };

    const paintState = function (combatant) {
        const hp = combatant.querySelector('[data-current-hp]');
        const state = combatant.querySelector('[data-combat-state]');
        if (!(hp instanceof HTMLInputElement) || !(state instanceof HTMLSelectElement)) {
            return;
        }
        if (integerValue(hp) > 0 && state.value !== 'standing') {
            return;
        }
        if (integerValue(hp) === 0 && state.value === 'standing') {
            const identity = combatant.querySelector('.gmrc-initiative-combatant__identity > span');
            state.value = identity && identity.textContent.trim() === 'Adventurer'
                ? 'unconscious'
                : 'defeated';
        }
    };

    document.querySelectorAll('[data-initiative-table]').forEach(function (table) {
        const live = table.querySelector('[data-initiative-live]');
        const removeId = table.querySelector('[data-remove-id]');

        table.querySelectorAll('[data-roll-initiative]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = button.parentElement
                    ? button.parentElement.querySelector('input')
                    : null;
                if (!(input instanceof HTMLInputElement)) {
                    return;
                }
                const natural = secureD20();
                const modifier = Number(button.getAttribute('data-modifier') || 0);
                input.value = String(natural + modifier);
                if (live) {
                    live.textContent = 'Initiative rolled: ' + natural
                        + (modifier >= 0 ? ' + ' : ' - ')
                        + Math.abs(modifier) + ' = ' + input.value + '.';
                }
            });
        });

        table.querySelectorAll('[data-combatant]').forEach(function (combatant) {
            combatant.querySelectorAll('[data-quick-vital]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const current = combatant.querySelector('[data-current-hp]');
                    const maximum = combatant.querySelector('[data-max-hp]');
                    const temporary = combatant.querySelector('[data-temp-hp]');
                    const amountInput = combatant.querySelector('[data-quick-amount]');
                    if (!(current instanceof HTMLInputElement)
                        || !(maximum instanceof HTMLInputElement)
                        || !(temporary instanceof HTMLInputElement)
                        || !(amountInput instanceof HTMLInputElement)) {
                        return;
                    }

                    const amount = Math.max(1, integerValue(amountInput));
                    const mode = button.getAttribute('data-quick-vital');
                    let hp = integerValue(current);
                    let temp = integerValue(temporary);

                    if (mode === 'damage') {
                        const absorbed = Math.min(temp, amount);
                        temp -= absorbed;
                        hp = Math.max(0, hp - (amount - absorbed));
                    } else if (mode === 'heal') {
                        hp = Math.min(integerValue(maximum), hp + amount);
                    }

                    current.value = String(hp);
                    temporary.value = String(temp);
                    paintState(combatant);
                    if (live) {
                        live.textContent = mode === 'damage'
                            ? 'Damage staged. Save the Console to record it.'
                            : 'Healing staged. Save the Console to record it.';
                    }
                });
            });

            combatant.querySelectorAll('[data-condition]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = combatant.querySelector('[data-conditions-input]');
                    if (!(input instanceof HTMLInputElement)) {
                        return;
                    }
                    const condition = button.getAttribute('data-condition') || '';
                    let values = input.value.split(',').map(function (item) {
                        return item.trim();
                    }).filter(Boolean);
                    const index = values.indexOf(condition);
                    if (index >= 0) {
                        values.splice(index, 1);
                    } else {
                        values.push(condition);
                    }
                    input.value = values.join(', ');
                    button.setAttribute('aria-pressed', index < 0 ? 'true' : 'false');
                });
            });
        });

        table.querySelectorAll('[data-remove-combatant]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (removeId instanceof HTMLInputElement) {
                    removeId.value = button.getAttribute('data-remove-combatant') || '';
                }
            });
        });

        table.querySelectorAll('[data-confirm]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                const message = button.getAttribute('data-confirm') || 'Continue?';
                if (!window.confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    });
}(window, document));
