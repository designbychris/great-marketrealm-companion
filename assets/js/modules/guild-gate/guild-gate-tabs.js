(() => {
    'use strict';

    const gates = document.querySelectorAll('[data-guild-gate]');

    const renderTurnstile = (panel) => {
        if (!panel || !window.turnstile) {
            return false;
        }

        const container = panel.querySelector('[data-gmrc-turnstile]');
        if (!container || container.dataset.turnstileRendered === 'true') {
            return true;
        }

        const sitekey = container.dataset.sitekey || '';
        if (!sitekey) {
            return true;
        }

        window.turnstile.render(container, {
            sitekey,
            action: container.dataset.action || undefined,
            theme: container.dataset.theme || 'auto',
            'refresh-expired': 'auto',
            'error-callback': () => {
                container.dataset.turnstileState = 'error';
            },
            'expired-callback': () => {
                container.dataset.turnstileState = 'expired';
            },
            callback: () => {
                container.dataset.turnstileState = 'verified';
            },
        });
        container.dataset.turnstileRendered = 'true';

        return true;
    };

    const renderWhenReady = (panel, attempts = 0) => {
        if (renderTurnstile(panel) || attempts >= 40) {
            return;
        }

        window.setTimeout(() => renderWhenReady(panel, attempts + 1), 125);
    };

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
                const selected = panel.dataset.guildGatePanel === target;
                panel.hidden = !selected;

                if (selected) {
                    renderWhenReady(panel);
                }
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

        const active = tabs.find((tab) => tab.getAttribute('aria-selected') === 'true');
        if (active) {
            const panel = panels.find((candidate) => candidate.dataset.guildGatePanel === active.dataset.guildGateTab);
            renderWhenReady(panel);
        }
    });
})();
