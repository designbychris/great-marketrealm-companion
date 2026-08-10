(function (window, document) {
    'use strict';

    const initialise = function (ledger) {
        if (!(ledger instanceof HTMLElement)) {
            return;
        }

        const tabs = Array.from(
            ledger.querySelectorAll('[role="tab"][data-ledger-tab]')
        );

        const panels = Array.from(
            ledger.querySelectorAll('[role="tabpanel"][data-ledger-panel]')
        );

        if (tabs.length === 0 || panels.length === 0) {
            return;
        }

        const activate = function (tab, moveFocus) {
            if (!(tab instanceof HTMLButtonElement)) {
                return;
            }

            const id = tab.getAttribute('aria-controls');

            tabs.forEach(function (candidate) {
                const active = candidate === tab;
                candidate.classList.toggle('is-active', active);
                candidate.setAttribute('aria-selected', active ? 'true' : 'false');
                candidate.tabIndex = active ? 0 : -1;
            });

            panels.forEach(function (panel) {
                const active = panel.id === id;
                panel.hidden = !active;
                panel.classList.toggle('is-active', active);
            });

            if (moveFocus) {
                tab.focus();
            }

            ledger.dataset.ledgerPage = tab.dataset.ledgerTab || 'overview';
        };

        tabs.forEach(function (tab, index) {
            tab.addEventListener('click', function () {
                activate(tab, false);
            });

            tab.addEventListener('keydown', function (event) {
                let next = null;

                if (event.key === 'ArrowRight') {
                    next = tabs[(index + 1) % tabs.length];
                } else if (event.key === 'ArrowLeft') {
                    next = tabs[(index - 1 + tabs.length) % tabs.length];
                } else if (event.key === 'Home') {
                    next = tabs[0];
                } else if (event.key === 'End') {
                    next = tabs[tabs.length - 1];
                }

                if (!(next instanceof HTMLButtonElement)) {
                    return;
                }

                event.preventDefault();
                activate(next, true);
            });
        });

        const requested = new URLSearchParams(
            window.location.search
        ).get('gmrc_ledger_tab');

        const requestedTab = requested
            ? tabs.find(function (tab) {
                return tab.dataset.ledgerTab === requested;
            })
            : null;

        const selected = tabs.find(function (tab) {
            return tab.getAttribute('aria-selected') === 'true';
        });

        activate(
            requestedTab instanceof HTMLButtonElement
                ? requestedTab
                : (selected instanceof HTMLButtonElement ? selected : tabs[0]),
            false
        );
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
