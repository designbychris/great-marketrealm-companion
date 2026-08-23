(() => {
    'use strict';

    const gates = document.querySelectorAll('[data-guild-gate]');

    gates.forEach((gate) => {
        const tabs = Array.from(gate.querySelectorAll('[data-guild-gate-tab]'));
        const panels = Array.from(gate.querySelectorAll('[data-guild-gate-panel]'));

        if (tabs.length < 2 || panels.length < 2) {
            return;
        }

        const activate = (tab, focus = false) => {
            const target = tab.dataset.guildGateTab;

            tabs.forEach((candidate) => {
                const selected = candidate === tab;
                candidate.setAttribute('aria-selected', selected ? 'true' : 'false');
                candidate.tabIndex = selected ? 0 : -1;
            });

            panels.forEach((panel) => {
                panel.hidden = panel.dataset.guildGatePanel !== target;
            });

            gate.dataset.guildGateActive = target;

            if (window.history && window.URL) {
                const url = new URL(tab.href, window.location.href);
                window.history.replaceState({}, '', url.toString());
            }

            if (focus) {
                tab.focus();
            }
        };

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', (event) => {
                event.preventDefault();
                activate(tab);
            });

            tab.addEventListener('keydown', (event) => {
                let nextIndex = index;

                if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                    nextIndex = (index + 1) % tabs.length;
                } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                    nextIndex = (index - 1 + tabs.length) % tabs.length;
                } else if (event.key === 'Home') {
                    nextIndex = 0;
                } else if (event.key === 'End') {
                    nextIndex = tabs.length - 1;
                } else {
                    return;
                }

                event.preventDefault();
                activate(tabs[nextIndex], true);
            });
        });
    });
})();
