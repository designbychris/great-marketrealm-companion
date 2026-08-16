(function (window, document) {
    'use strict';

    const MAX_HISTORY = 12;
    const MAX_FAVOURITES = 8;
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
        const launcher = ledger.querySelector('[data-guild-dice-launcher]');
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
        const reaction = tray.querySelector('[data-guild-dice-reaction]');
        const reactionBanner = tray.querySelector(
            '[data-guild-dice-reaction-banner]'
        );
        const confetti = tray.querySelector('[data-guild-dice-confetti]');
        const history = tray.querySelector('[data-guild-dice-history]');
        const historyList = tray.querySelector('[data-guild-dice-history-list]');
        const historyClear = tray.querySelector(
            '[data-guild-dice-history-clear]'
        );
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
        const freeRollPin = tray.querySelector('[data-guild-free-roll-pin]');
        const favouriteToggle = tray.querySelector(
            '[data-guild-favourite-toggle]'
        );
        const favouriteSymbol = tray.querySelector(
            '[data-guild-favourite-symbol]'
        );
        const favouriteLabel = tray.querySelector(
            '[data-guild-favourite-label]'
        );
        const quickRolls = tray.querySelector('[data-guild-quick-rolls]');
        const quickRollList = tray.querySelector(
            '[data-guild-quick-roll-list]'
        );
        const quickRollCount = tray.querySelector(
            '[data-guild-quick-roll-count]'
        );
        const situationalPanel = tray.querySelector(
            '[data-guild-situational-panel]'
        );
        const situationalSummary = tray.querySelector(
            '[data-guild-situational-summary]'
        );
        const situationalFlat = tray.querySelector(
            '[data-guild-situational-flat]'
        );
        const situationalDie = tray.querySelector(
            '[data-guild-situational-die]'
        );
        const situationalShortcuts = Array.from(
            tray.querySelectorAll('[data-guild-situational-shortcut]')
        );

        let activeTrigger = null;
        const characterId = ledger.dataset.characterId || 'unknown';
        const historyKey = 'gmrc:guild-dice:history:' + characterId;
        const favouritesKey = 'gmrc:guild-dice:favourites:' + characterId;
        const recent = [];
        const favourites = [];

        const loadFavourites = function () {
            if (!window.localStorage) {
                return;
            }

            try {
                const stored = JSON.parse(
                    window.localStorage.getItem(favouritesKey) || '[]'
                );

                if (!Array.isArray(stored)) {
                    return;
                }

                stored.slice(0, MAX_FAVOURITES).forEach(function (entry) {
                    if (
                        entry
                        && typeof entry === 'object'
                        && typeof entry.type === 'string'
                        && typeof entry.key === 'string'
                    ) {
                        favourites.push(entry);
                    }
                });
            } catch (error) {
                window.localStorage.removeItem(favouritesKey);
            }
        };

        const persistFavourites = function () {
            if (!window.localStorage) {
                return;
            }

            try {
                window.localStorage.setItem(
                    favouritesKey,
                    JSON.stringify(favourites)
                );
            } catch (error) {
                // Favourites are optional; rolling must remain available.
            }
        };

        const triggerReference = function (trigger) {
            if (!(trigger instanceof HTMLButtonElement)) {
                return '';
            }

            return [
                trigger.dataset.rollKind || 'check',
                trigger.dataset.rollSource || '',
                trigger.dataset.rollLabel || '',
                trigger.dataset.rollFormula || '',
                trigger.dataset.rollDamageType || ''
            ].join('|');
        };

        const findTriggerByReference = function (reference) {
            return triggers.find(function (trigger) {
                return triggerReference(trigger) === reference;
            }) || null;
        };

        const loadHistory = function () {
            if (!window.sessionStorage) {
                return;
            }

            try {
                const stored = JSON.parse(
                    window.sessionStorage.getItem(historyKey) || '[]'
                );

                if (!Array.isArray(stored)) {
                    return;
                }

                stored.slice(0, MAX_HISTORY).forEach(function (entry) {
                    if (
                        entry
                        && typeof entry === 'object'
                        && typeof entry.text === 'string'
                    ) {
                        recent.push(entry);
                    }
                });
            } catch (error) {
                window.sessionStorage.removeItem(historyKey);
            }
        };

        const persistHistory = function () {
            if (!window.sessionStorage) {
                return;
            }

            try {
                window.sessionStorage.setItem(
                    historyKey,
                    JSON.stringify(recent)
                );
            } catch (error) {
                // Dice rolling must remain available if storage is blocked.
            }
        };

        const current = function () {
            if (!(activeTrigger instanceof HTMLButtonElement)) {
                return null;
            }

            return {
                label: activeTrigger.dataset.rollLabel || 'D20 Roll',
                modifier: Number(activeTrigger.dataset.rollModifier) || 0,
                kind: activeTrigger.dataset.rollKind || 'check',
                source: activeTrigger.dataset.rollSource || '',
                ability: activeTrigger.dataset.rollAbility || '',
                proficiency: activeTrigger.dataset.rollProficiency || 'none',
                formula: activeTrigger.dataset.rollFormula || '',
                damageType: activeTrigger.dataset.rollDamageType || '',
                resultSuffix: activeTrigger.dataset.rollResultSuffix || ''
            };
        };

        const favouriteIndex = function (key) {
            return favourites.findIndex(function (entry) {
                return entry.key === key;
            });
        };

        const updateFavouriteToggle = function () {
            if (
                !(favouriteToggle instanceof HTMLButtonElement)
                || !(favouriteSymbol instanceof HTMLElement)
                || !(favouriteLabel instanceof HTMLElement)
            ) {
                return;
            }

            if (!(activeTrigger instanceof HTMLButtonElement)) {
                favouriteToggle.hidden = true;
                return;
            }

            const key = triggerReference(activeTrigger);
            const pinned = favouriteIndex(key) !== -1;

            favouriteToggle.hidden = false;
            favouriteToggle.dataset.favouriteKey = key;
            favouriteToggle.setAttribute(
                'aria-pressed',
                pinned ? 'true' : 'false'
            );
            favouriteSymbol.textContent = pinned ? '★' : '☆';
            favouriteLabel.textContent = pinned
                ? 'Remove from Quick Rolls'
                : 'Add to Quick Rolls';
        };

        const removeFavourite = function (key) {
            const index = favouriteIndex(key);

            if (index === -1) {
                return;
            }

            const removed = favourites[index];

            favourites.splice(index, 1);
            persistFavourites();
            paintQuickRolls();
            updateFavouriteToggle();

            if (live instanceof HTMLElement) {
                live.textContent = 'Removed '
                    + (removed.label || 'Quick Roll')
                    + ' from Quick Rolls.';
            }
        };

        const addCharacterFavourite = function (trigger) {
            const key = triggerReference(trigger);

            if (key === '' || favouriteIndex(key) !== -1) {
                return;
            }

            if (favourites.length >= MAX_FAVOURITES) {
                if (live instanceof HTMLElement) {
                    live.textContent = 'Quick Rolls can hold up to '
                        + MAX_FAVOURITES
                        + ' favourites.';
                }
                return;
            }

            favourites.push({
                type: 'character',
                key: key,
                label: trigger.dataset.rollLabel || 'Guild Roll'
            });

            persistFavourites();
            paintQuickRolls();
            updateFavouriteToggle();

            if (live instanceof HTMLElement) {
                live.textContent = 'Added '
                    + (trigger.dataset.rollLabel || 'Guild Roll')
                    + ' to Quick Rolls.';
            }
        };

        const freeRollDefinition = function () {
            const quantity = Math.min(
                MAX_FREE_DICE,
                Math.max(
                    1,
                    Math.floor(
                        Number(
                            freeQuantity instanceof HTMLInputElement
                                ? freeQuantity.value
                                : 1
                        ) || 1
                    )
                )
            );
            const sides = Number(
                freeDie instanceof HTMLSelectElement
                    ? freeDie.value
                    : 6
            );
            const modifier = Number(
                freeModifier instanceof HTMLInputElement
                    ? freeModifier.value
                    : 0
            ) || 0;
            const safeSides = SUPPORTED_DICE.includes(sides)
                ? sides
                : 6;
            const formula = quantity + 'd' + safeSides;
            const key = 'free|' + formula + '|' + modifier;

            return {
                type: 'free',
                key: key,
                label: formula + ' ' + signed(modifier),
                quantity: quantity,
                sides: safeSides,
                modifier: modifier
            };
        };

        const addFreeFavourite = function () {
            const definition = freeRollDefinition();

            if (favouriteIndex(definition.key) !== -1) {
                if (live instanceof HTMLElement) {
                    live.textContent = definition.label
                        + ' is already in Quick Rolls.';
                }
                return;
            }

            if (favourites.length >= MAX_FAVOURITES) {
                if (live instanceof HTMLElement) {
                    live.textContent = 'Quick Rolls can hold up to '
                        + MAX_FAVOURITES
                        + ' favourites.';
                }
                return;
            }

            favourites.push(definition);
            persistFavourites();
            paintQuickRolls();

            if (live instanceof HTMLElement) {
                live.textContent = 'Added '
                    + definition.label
                    + ' to Quick Rolls.';
            }
        };

        const runFavourite = function (entry) {
            if (entry.type === 'character') {
                const trigger = findTriggerByReference(entry.key);

                if (!(trigger instanceof HTMLButtonElement)) {
                    return;
                }

                openTray(trigger);
                perform('normal');
                return;
            }

            if (
                entry.type === 'free'
                && freeQuantity instanceof HTMLInputElement
                && freeDie instanceof HTMLSelectElement
                && freeModifier instanceof HTMLInputElement
            ) {
                freeQuantity.value = String(entry.quantity || 1);
                freeDie.value = String(entry.sides || 6);
                freeModifier.value = String(entry.modifier || 0);
                activeTrigger = null;
                updateFavouriteToggle();
                performFreeRoll();
            }
        };

        const paintQuickRolls = function () {
            if (
                !(quickRolls instanceof HTMLElement)
                || !(quickRollList instanceof HTMLElement)
            ) {
                return;
            }

            quickRollList.replaceChildren();

            favourites.forEach(function (entry) {
                const wrapper = document.createElement('div');
                const roll = document.createElement('button');
                const remove = document.createElement('button');
                const trigger = entry.type === 'character'
                    ? findTriggerByReference(entry.key)
                    : null;

                wrapper.className = 'gmrc-guild-quick-roll';
                roll.type = 'button';
                roll.className = 'gmrc-guild-quick-roll__roll';

                if (entry.type === 'character') {
                    if (trigger instanceof HTMLButtonElement) {
                        const modifier = Number(
                            trigger.dataset.rollModifier
                        ) || 0;
                        roll.textContent = (entry.label || 'Guild Roll')
                            + ' '
                            + signed(modifier);
                    } else {
                        roll.textContent = (entry.label || 'Guild Roll')
                            + ' — unavailable';
                        roll.disabled = true;
                    }
                } else {
                    roll.textContent = entry.label || 'Free Roll';
                }

                roll.addEventListener('click', function () {
                    runFavourite(entry);
                });

                remove.type = 'button';
                remove.className = 'gmrc-guild-quick-roll__remove';
                remove.setAttribute(
                    'aria-label',
                    'Remove ' + (entry.label || 'Quick Roll')
                );
                remove.textContent = '×';
                remove.addEventListener('click', function () {
                    removeFavourite(entry.key);
                });

                wrapper.append(roll, remove);
                quickRollList.appendChild(wrapper);
            });

            quickRolls.hidden = favourites.length === 0;

            if (quickRollCount instanceof HTMLElement) {
                quickRollCount.textContent = favourites.length
                    + '/'
                    + MAX_FAVOURITES;
            }
        };

        const situationalAdjustment = function () {
            const flat = Math.min(
                20,
                Math.max(
                    -20,
                    Math.floor(
                        Number(
                            situationalFlat instanceof HTMLInputElement
                                ? situationalFlat.value
                                : 0
                        ) || 0
                    )
                )
            );
            const requestedSides = Number(
                situationalDie instanceof HTMLSelectElement
                    ? situationalDie.value
                    : 0
            );
            const dieSides = [4, 6, 8, 10, 12].includes(requestedSides)
                ? requestedSides
                : 0;
            const dieValue = dieSides > 0
                ? secureDie(dieSides)
                : 0;

            return {
                flat: flat,
                dieSides: dieSides,
                dieValue: dieValue,
                total: flat + dieValue
            };
        };

        const situationalText = function (adjustment) {
            const parts = [];

            if (adjustment.flat !== 0) {
                parts.push('situational ' + signed(adjustment.flat));
            }

            if (adjustment.dieSides > 0) {
                parts.push(
                    'situational d'
                    + adjustment.dieSides
                    + ' (' + adjustment.dieValue + ')'
                );
            }

            return parts.join(' + ');
        };

        const refreshSituationalSummary = function () {
            if (!(situationalSummary instanceof HTMLElement)) {
                return;
            }

            const flat = Math.min(
                20,
                Math.max(
                    -20,
                    Math.floor(
                        Number(
                            situationalFlat instanceof HTMLInputElement
                                ? situationalFlat.value
                                : 0
                        ) || 0
                    )
                )
            );
            const sides = Number(
                situationalDie instanceof HTMLSelectElement
                    ? situationalDie.value
                    : 0
            );
            const parts = [];

            if (flat !== 0) {
                parts.push(signed(flat));
            }

            if ([4, 6, 8, 10, 12].includes(sides)) {
                parts.push('+d' + sides);
            }

            situationalSummary.textContent = parts.length > 0
                ? parts.join(' ') + ' · next roll only'
                : 'Next roll only';
        };

        const resetSituational = function () {
            if (situationalFlat instanceof HTMLInputElement) {
                situationalFlat.value = '0';
            }

            if (situationalDie instanceof HTMLSelectElement) {
                situationalDie.value = '0';
            }

            refreshSituationalSummary();

            if (situationalPanel instanceof HTMLDetailsElement) {
                situationalPanel.open = false;
            }
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
                const copy = document.createElement('span');
                const meta = document.createElement('small');

                copy.textContent = entry.text;
                meta.textContent = entry.time || 'This session';

                item.dataset.rollKind = entry.kind || 'roll';
                item.append(copy, meta);
                historyList.appendChild(item);
            });

            history.hidden = recent.length === 0;
        };

        const remember = function (entry, metadata) {
            const details = metadata || {};
            const recorded = {
                text: entry,
                kind: details.kind || 'roll',
                formula: details.formula || '',
                dice: Array.isArray(details.dice) ? details.dice : [],
                modifier: Number(details.modifier) || 0,
                total: Number(details.total) || 0,
                natural: Number(details.natural) || 0,
                reaction: details.reaction || 'none',
                situational: details.situational
                    && typeof details.situational === 'object'
                        ? details.situational
                        : {
                            flat: 0,
                            dieSides: 0,
                            dieValue: 0
                        },
                time: new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                })
            };

            recent.unshift(recorded);
            recent.splice(MAX_HISTORY);
            persistHistory();
            paintHistory();

            if (live instanceof HTMLElement) {
                live.textContent = entry;
            }
        };

        const clearHistory = function () {
            recent.splice(0, recent.length);
            persistHistory();
            paintHistory();

            if (live instanceof HTMLElement) {
                live.textContent = 'The Dice Ledger has been cleared.';
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

        const paintSituationalDie = function (adjustment) {
            if (
                !(stage instanceof HTMLElement)
                || adjustment.dieSides <= 0
            ) {
                return;
            }

            const die = makeDie(
                adjustment.dieValue,
                adjustment.dieSides,
                false
            );

            die.classList.add('is-situational');
            die.dataset.situationalDie = 'true';
            stage.appendChild(die);
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
            hideAuby();
            updateFavouriteToggle();

            const normal = modeButtons.find(function (button) {
                return button.dataset.guildRollMode === 'normal';
            });

            if (normal instanceof HTMLButtonElement) {
                normal.focus();
            }
        };

        const openQuickRollsTray = function () {
            activeTrigger = null;
            tray.hidden = false;
            tray.classList.add('is-open');

            if (label instanceof HTMLElement) {
                label.textContent = 'Quick Rolls';
            }

            if (modifierNode instanceof HTMLElement) {
                modifierNode.textContent = '+0';
            }

            if (context instanceof HTMLElement) {
                context.hidden = true;
            }

            if (result instanceof HTMLElement) {
                result.hidden = true;
            }

            if (stage instanceof HTMLElement) {
                stage.replaceChildren();
            }

            clearReaction();
            hideAuby();
            updateFavouriteToggle();
            paintQuickRolls();

            const firstQuickRoll = quickRollList instanceof HTMLElement
                ? quickRollList.querySelector(
                    '.gmrc-guild-quick-roll__roll:not(:disabled)'
                )
                : null;

            if (firstQuickRoll instanceof HTMLButtonElement) {
                firstQuickRoll.focus();
            } else if (freePanel instanceof HTMLDetailsElement) {
                const summary = freePanel.querySelector('summary');

                if (summary instanceof HTMLElement) {
                    summary.focus();
                }
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

            const adjustment = situationalAdjustment();
            paintSituationalDie(adjustment);

            const total = rolled.total
                + selection.modifier
                + adjustment.total;
            const adjustmentText = situationalText(adjustment);
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
                    + signed(selection.modifier)
                    + (
                        adjustmentText
                            ? ' + ' + adjustmentText
                            : ''
                    );
            }

            if (totalNode instanceof HTMLElement) {
                totalNode.textContent = '= ' + total + ' ' + resultLabel;
            }

            hideAuby();
            showResult();

            remember(
                contextSummary(selection)
                + ' — '
                + selection.label
                + ': '
                + rolled.dice.join(' + ')
                + ' '
                + signed(selection.modifier)
                + (
                    adjustmentText
                        ? ' + ' + adjustmentText
                        : ''
                )
                + ' = '
                + total
                + ' '
                + resultLabel,
                {
                    kind: selection.kind,
                    formula: selection.formula,
                    dice: rolled.dice,
                    modifier: selection.modifier,
                    total: total,
                    situational: adjustment
                }
            );

            resetSituational();
        };

        const performD20 = function (selection, mode) {
            const rolled = rollMode(mode);
            const adjustment = situationalAdjustment();
            const total = rolled.natural
                + selection.modifier
                + adjustment.total;
            const adjustmentText = situationalText(adjustment);
            const modeLabel = mode === 'advantage'
                ? 'Advantage'
                : mode === 'disadvantage'
                    ? 'Disadvantage'
                    : 'Normal Roll';

            paintDice(rolled.dice, 20, rolled.keptIndex);
            paintSituationalDie(adjustment);

            if (modeNode instanceof HTMLElement) {
                modeNode.textContent = modeLabel;
            }

            if (mathNode instanceof HTMLElement) {
                const diceText = rolled.dice.length === 2
                    ? rolled.dice.join(' / ') + ' → ' + rolled.natural
                    : String(rolled.natural);

                mathNode.textContent = diceText
                    + ' '
                    + signed(selection.modifier)
                    + (
                        adjustmentText
                            ? ' + ' + adjustmentText
                            : ''
                    );
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
                + (
                    adjustmentText
                        ? ' + ' + adjustmentText
                        : ''
                )
                + ' = '
                + total
                + ' ('
                + modeLabel
                + ')'
                + (
                    reactionAnnouncement
                        ? ' — ' + reactionAnnouncement
                        : ''
                ),
                {
                    kind: selection.kind,
                    formula: '1d20',
                    dice: rolled.dice,
                    modifier: selection.modifier,
                    total: total,
                    natural: rolled.natural,
                    reaction: naturalReaction(rolled.natural),
                    situational: adjustment
                }
            );

            resetSituational();
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
            activeTrigger = null;
            updateFavouriteToggle();

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
            const adjustment = situationalAdjustment();
            const total = subtotal
                + modifier
                + adjustment.total;
            const adjustmentText = situationalText(adjustment);
            const formula = quantity + 'd' + sides;

            clearReaction();
            paintDice(values, sides, null);
            paintSituationalDie(adjustment);

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
                    + signed(modifier)
                    + (
                        adjustmentText
                            ? ' + ' + adjustmentText
                            : ''
                    );
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
                + (
                    adjustmentText
                        ? ' + ' + adjustmentText
                        : ''
                )
                + ' = '
                + total
                + (freeReaction ? ' — ' + freeReaction : ''),
                {
                    kind: 'free-roll',
                    formula: formula,
                    dice: values,
                    modifier: modifier,
                    total: total,
                    natural: sides === 20 && quantity === 1
                        ? values[0]
                        : 0,
                    reaction: sides === 20 && quantity === 1
                        ? naturalReaction(values[0])
                        : 'none',
                    situational: adjustment
                }
            );

            resetSituational();
        };

        if (situationalFlat instanceof HTMLInputElement) {
            situationalFlat.addEventListener('input', function () {
                const value = Math.min(
                    20,
                    Math.max(
                        -20,
                        Math.floor(Number(situationalFlat.value) || 0)
                    )
                );

                situationalFlat.value = String(value);
                refreshSituationalSummary();
            });
        }

        if (situationalDie instanceof HTMLSelectElement) {
            situationalDie.addEventListener(
                'change',
                refreshSituationalSummary
            );
        }

        situationalShortcuts.forEach(function (button) {
            button.addEventListener('click', function () {
                if (!(situationalFlat instanceof HTMLInputElement)) {
                    return;
                }

                situationalFlat.value = String(
                    Number(button.dataset.guildSituationalShortcut) || 0
                );
                refreshSituationalSummary();
                situationalFlat.focus();
            });
        });

        refreshSituationalSummary();

        if (historyClear instanceof HTMLButtonElement) {
            historyClear.addEventListener('click', clearHistory);
        }

        if (favouriteToggle instanceof HTMLButtonElement) {
            favouriteToggle.addEventListener('click', function () {
                if (!(activeTrigger instanceof HTMLButtonElement)) {
                    return;
                }

                const key = triggerReference(activeTrigger);

                if (favouriteIndex(key) !== -1) {
                    removeFavourite(key);
                } else {
                    addCharacterFavourite(activeTrigger);
                }
            });
        }

        if (freeRollPin instanceof HTMLButtonElement) {
            freeRollPin.addEventListener('click', addFreeFavourite);
        }

        loadFavourites();
        paintQuickRolls();
        loadHistory();
        paintHistory();

        if (launcher instanceof HTMLButtonElement) {
            launcher.addEventListener('click', openQuickRollsTray);
        }

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
