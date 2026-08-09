(function (window, document) {
    'use strict';

    const labels = {
        strength: 'STR',
        dexterity: 'DEX',
        constitution: 'CON',
        intelligence: 'INT',
        wisdom: 'WIS',
        charisma: 'CHA'
    };

    const modifier = function (score) {
        const value = Math.floor((Number(score) - 10) / 2);

        return value >= 0 ? '+' + value : String(value);
    };

    const selectedText = function (select) {
        if (!(select instanceof HTMLSelectElement)) {
            return '';
        }

        const option = select.options[select.selectedIndex];

        return option ? option.textContent.trim() : '';
    };

    const initialise = function (form) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const backgrounds = Array.from(
            form.querySelectorAll('input[name="background"]')
        );

        const languageSlots = Array.from(
            form.querySelectorAll('[data-language-slot]')
        );

        const artisan = form.querySelector('[data-tool-choice="artisan"]');
        const gaming = form.querySelector('[data-tool-choice="gaming"]');
        const noChoices = form.querySelector('[data-registration-no-extra-choices]');
        const review = form.querySelector('[data-registration-review]');

        const activeBackground = function () {
            return backgrounds.find(function (input) {
                return input.checked;
            }) || null;
        };

        const setRequired = function (container, visible) {
            if (!(container instanceof HTMLElement)) {
                return;
            }

            container.hidden = !visible;

            const select = container.querySelector('select');

            if (select instanceof HTMLSelectElement) {
                select.required = visible;
            }
        };

        const updateChoices = function () {
            const background = activeBackground();

            const languageCount = background
                ? Number(background.dataset.languageChoices || 0)
                : 0;

            languageSlots.forEach(function (slot) {
                const index = Number(slot.dataset.languageSlot || 0);

                setRequired(
                    slot,
                    index > 0 && index <= languageCount
                );
            });

            const needsArtisan = Boolean(
                background
                && background.dataset.needsArtisanTools === '1'
            );

            const needsGaming = Boolean(
                background
                && background.dataset.needsGamingSet === '1'
            );

            setRequired(artisan, needsArtisan);
            setRequired(gaming, needsGaming);

            if (noChoices instanceof HTMLElement) {
                noChoices.hidden = Boolean(
                    languageCount
                    || needsArtisan
                    || needsGaming
                );
            }
        };

        const updateModifiers = function () {
            form.querySelectorAll('[data-registration-ability]')
                .forEach(function (select) {
                    if (!(select instanceof HTMLSelectElement)) {
                        return;
                    }

                    const key = select.dataset.registrationAbility;

                    const target = form.querySelector(
                        '[data-registration-modifier="' + key + '"]'
                    );

                    if (target instanceof HTMLElement) {
                        target.textContent = modifier(select.value);
                    }
                });
        };

        const updateReview = function () {
            if (!(review instanceof HTMLElement)) {
                return;
            }

            const set = function (selector, value) {
                const node = review.querySelector(selector);

                if (node instanceof HTMLElement) {
                    node.textContent = value;
                }
            };

            const name = form.querySelector('[name="name"]');
            const race = form.querySelector('input[name="race"]:checked');
            const characterClass = form.querySelector('input[name="class"]:checked');
            const background = activeBackground();

            set(
                '[data-registration-review-name]',
                name instanceof HTMLInputElement && name.value.trim()
                    ? name.value.trim()
                    : 'Awaiting inscription'
            );

            set(
                '[data-registration-review-race]',
                race instanceof HTMLInputElement
                    ? (race.dataset.raceLabel || race.value)
                    : 'Awaiting selection'
            );

            set(
                '[data-registration-review-class]',
                characterClass instanceof HTMLInputElement
                    ? (characterClass.dataset.classLabel || characterClass.value)
                    : 'Awaiting selection'
            );

            set(
                '[data-registration-review-background]',
                background
                    ? (background.dataset.backgroundLabel || background.value)
                    : 'Awaiting selection'
            );

            const abilities = Array.from(
                form.querySelectorAll('[data-registration-ability]')
            ).map(function (select) {
                if (!(select instanceof HTMLSelectElement)) {
                    return '';
                }

                const key = select.dataset.registrationAbility;

                return (labels[key] || key.toUpperCase()) + ' ' + select.value;
            }).filter(Boolean);

            set(
                '[data-registration-review-abilities]',
                abilities.length
                    ? abilities.join(' · ')
                    : 'Awaiting assignment'
            );

            const choices = [];

            languageSlots.forEach(function (slot) {
                if (!(slot instanceof HTMLElement) || slot.hidden) {
                    return;
                }

                const text = selectedText(slot.querySelector('select'));

                if (
                    text
                    && !text.toLowerCase().startsWith('choose')
                ) {
                    choices.push(text);
                }
            });

            [artisan, gaming].forEach(function (container) {
                if (
                    !(container instanceof HTMLElement)
                    || container.hidden
                ) {
                    return;
                }

                const text = selectedText(container.querySelector('select'));

                if (
                    text
                    && !text.toLowerCase().startsWith('choose')
                ) {
                    choices.push(text);
                }
            });

            set(
                '[data-registration-review-choices]',
                choices.length
                    ? choices.join(' · ')
                    : (
                        background
                            ? 'No additional choices'
                            : 'Awaiting background'
                    )
            );
        };

        const refresh = function () {
            updateChoices();
            updateModifiers();
            updateReview();
        };

        form.addEventListener('change', refresh);
        form.addEventListener('input', updateReview);

        refresh();
    };

    const boot = function () {
        document
            .querySelectorAll('.gmrc-character-form')
            .forEach(initialise);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(window, document);
