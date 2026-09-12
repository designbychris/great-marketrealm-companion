(function (window, document) {
    'use strict';

    const STORAGE_KEY = 'gmrc.adventuringSheet.guildDiceEnabled';
    const i18n = window.gmrcAdventuringSheetI18n || {};

    const readPreference = function () {
        try {
            return window.localStorage.getItem(STORAGE_KEY) === 'true';
        } catch (error) {
            return false;
        }
    };

    const writePreference = function (enabled) {
        try {
            window.localStorage.setItem(STORAGE_KEY, enabled ? 'true' : 'false');
        } catch (error) {
            // Local storage can be unavailable in strict privacy contexts.
        }
    };

    const initialise = function (sheet) {
        if (!(sheet instanceof HTMLElement)) {
            return;
        }

        const toggle = sheet.querySelector('[data-adventuring-dice-toggle]');
        const state = sheet.querySelector('[data-adventuring-dice-state]');
        const rollButtons = Array.from(sheet.querySelectorAll('[data-guild-roll]'));

        if (!(toggle instanceof HTMLButtonElement)) {
            return;
        }

        const apply = function (enabled) {
            sheet.dataset.guildDiceEnabled = enabled ? 'true' : 'false';
            toggle.setAttribute('aria-checked', enabled ? 'true' : 'false');

            if (state instanceof HTMLElement) {
                state.textContent = enabled ? (i18n.on || 'On') : (i18n.off || 'Off');
            }

            rollButtons.forEach(function (button) {
                if (!(button instanceof HTMLButtonElement)) {
                    return;
                }

                button.disabled = !enabled;
                button.setAttribute('aria-disabled', enabled ? 'false' : 'true');
            });
        };

        apply(readPreference());

        toggle.addEventListener('click', function () {
            const enabled = sheet.dataset.guildDiceEnabled !== 'true';
            apply(enabled);
            writePreference(enabled);
        });
    };

    const boot = function () {
        document.querySelectorAll('[data-adventuring-sheet]').forEach(initialise);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(window, document);
