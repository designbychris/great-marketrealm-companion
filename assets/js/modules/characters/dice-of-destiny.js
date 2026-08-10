(function (window, document) {
    'use strict';

    const STANDARD = [15, 14, 13, 12, 10, 8];

    const secureD6 = function () {
        if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
            const range = 0x100000000;
            const limit = range - (range % 6);
            const values = new Uint32Array(1);
            let value = limit;
            while (value >= limit) {
                window.crypto.getRandomValues(values);
                value = values[0];
            }
            return (value % 6) + 1;
        }
        return Math.floor(Math.random() * 6) + 1;
    };

    const modifier = function (score) {
        const value = Math.floor((Number(score) - 10) / 2);
        return value >= 0 ? '+' + value : String(value);
    };

    const initialise = function (root) {
        if (!(root instanceof HTMLElement)) { return; }

        const methods = Array.from(root.querySelectorAll('[data-destiny-method]'));
        const toolbar = root.querySelector('[data-destiny-toolbar]');
        const rollAll = root.querySelector('[data-destiny-roll-all]');
        const abilities = Array.from(root.querySelectorAll('[data-destiny-ability]'));
        const live = root.querySelector('[data-destiny-live]');
        let standardState = {};

        const currentMethod = function () {
            const selected = methods.find(function (input) {
                return input instanceof HTMLInputElement && input.checked;
            });
            return selected instanceof HTMLInputElement ? selected.value : 'standard';
        };

        const scoreSelect = function (ability) {
            const select = ability.querySelector('[data-destiny-score]');
            return select instanceof HTMLSelectElement ? select : null;
        };

        const rememberStandard = function () {
            abilities.forEach(function (ability) {
                const key = ability.dataset.destinyAbility || '';
                const select = scoreSelect(ability);
                if (key && select) { standardState[key] = select.value; }
            });
        };

        const removeRolledOptions = function (select) {
            Array.from(select.querySelectorAll('[data-destiny-rolled-option]'))
                .forEach(function (option) { option.remove(); });
        };

        const selectScore = function (select, score) {
            removeRolledOptions(select);
            const existing = Array.from(select.options).find(function (option) {
                return Number(option.value) === score;
            });

            if (existing instanceof HTMLOptionElement) {
                existing.selected = true;
            } else {
                const option = document.createElement('option');
                option.value = String(score);
                option.textContent = String(score);
                option.dataset.destinyRolledOption = 'true';
                option.selected = true;
                select.prepend(option);
            }

            select.dispatchEvent(new Event('change', { bubbles: true }));
        };

        const paintDice = function (ability, dice, total) {
            const nodes = Array.from(ability.querySelectorAll('[data-destiny-die]'));
            const math = ability.querySelector('[data-destiny-math]');

            nodes.forEach(function (node, index) {
                if (!(node instanceof HTMLElement)) { return; }
                node.classList.remove('is-rolling');
                void node.offsetWidth;
                node.textContent = String(dice[index]);
                node.classList.add('is-rolling');
            });

            if (math instanceof HTMLElement) {
                math.textContent = dice.join(' + ') + ' = ' + total
                    + ' (' + modifier(total) + ')';
            }
        };

        const rollAbility = function (ability, announce) {
            const select = scoreSelect(ability);
            if (!select) { return null; }

            const dice = [secureD6(), secureD6(), secureD6()];
            const total = dice.reduce(function (sum, value) {
                return sum + value;
            }, 0);

            selectScore(select, total);
            paintDice(ability, dice, total);

            const label = ability.dataset.destinyAbility || 'ability';
            if (announce && live instanceof HTMLElement) {
                live.textContent = label + ' rolled ' + dice.join(', ')
                    + ' for a total of ' + total + '.';
            }

            return { key: label, dice: dice, total: total };
        };

        const setMode = function () {
            const rolled = currentMethod() === 'rolled';
            root.dataset.abilityMethod = rolled ? 'rolled' : 'standard';

            if (toolbar instanceof HTMLElement) { toolbar.hidden = !rolled; }

            abilities.forEach(function (ability) {
                const panel = ability.querySelector('[data-destiny-roll-panel]');
                const select = scoreSelect(ability);
                const key = ability.dataset.destinyAbility || '';

                if (panel instanceof HTMLElement) { panel.hidden = !rolled; }
                if (!select) { return; }

                if (!rolled) {
                    removeRolledOptions(select);
                    const restored = Number(
                        standardState[key] || STANDARD[abilities.indexOf(ability)]
                    );
                    selectScore(select, restored);
                }
            });

            if (live instanceof HTMLElement) {
                live.textContent = rolled
                    ? 'Dice of Destiny selected. Roll 3d6 for each ability.'
                    : 'Standard Guild Array selected.';
            }
        };

        rememberStandard();

        abilities.forEach(function (ability) {
            const button = ability.querySelector('[data-destiny-roll]');
            if (button instanceof HTMLButtonElement) {
                button.addEventListener('click', function () {
                    rollAbility(ability, true);
                });
            }
        });

        methods.forEach(function (input) {
            input.addEventListener('change', function () {
                if (currentMethod() === 'rolled') { rememberStandard(); }
                setMode();
            });
        });

        if (rollAll instanceof HTMLButtonElement) {
            rollAll.addEventListener('click', function () {
                const results = [];
                abilities.forEach(function (ability, index) {
                    window.setTimeout(function () {
                        const result = rollAbility(ability, false);
                        if (result) { results.push(result); }
                        if (index === abilities.length - 1 && live instanceof HTMLElement) {
                            live.textContent = 'All six abilities rolled: '
                                + results.map(function (entry) {
                                    return entry.key + ' ' + entry.total;
                                }).join(', ') + '.';
                        }
                    }, index * 120);
                });
            });
        }

        setMode();
    };

    const boot = function () {
        document.querySelectorAll('[data-dice-of-destiny]').forEach(initialise);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(window, document);
