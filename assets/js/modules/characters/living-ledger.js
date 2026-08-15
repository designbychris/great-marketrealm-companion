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

        ledger
            .querySelectorAll('[data-complete-adventurer]')
            .forEach(function (audit) {
                const toggle = audit.querySelector(
                    '[data-registrar-audit-toggle]'
                );
                const content = audit.querySelector(
                    '[data-registrar-audit-content]'
                );

                if (
                    !(toggle instanceof HTMLButtonElement)
                    || !(content instanceof HTMLElement)
                ) {
                    return;
                }

                const label = toggle.querySelector(
                    '[data-registrar-audit-toggle-label]'
                );
                const symbol = toggle.querySelector(
                    '[data-registrar-audit-toggle-symbol]'
                );
                const storageKey = toggle.dataset.auditStorageKey || '';

                const setCollapsed = function (collapsed, persist) {
                    content.hidden = collapsed;
                    audit.classList.toggle('is-audit-collapsed', collapsed);
                    toggle.setAttribute(
                        'aria-expanded',
                        collapsed ? 'false' : 'true'
                    );

                    if (label instanceof HTMLElement) {
                        label.textContent = collapsed
                            ? 'Show Audit'
                            : 'Hide Audit';
                    }

                    if (symbol instanceof HTMLElement) {
                        symbol.textContent = collapsed ? '+' : '−';
                    }

                    if (!persist || storageKey === '') {
                        return;
                    }

                    try {
                        window.localStorage.setItem(
                            storageKey,
                            collapsed ? 'true' : 'false'
                        );
                    } catch (error) {
                        // Storage can be unavailable in privacy-restricted contexts.
                    }
                };

                let collapsed = false;

                if (storageKey !== '') {
                    try {
                        collapsed = window.localStorage.getItem(storageKey)
                            === 'true';
                    } catch (error) {
                        collapsed = false;
                    }
                }

                setCollapsed(collapsed, false);

                toggle.addEventListener('click', function () {
                    setCollapsed(!content.hidden, true);
                });
            });

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

        ledger
            .querySelectorAll('[data-ledger-jump]')
            .forEach(function (trigger) {
                trigger.addEventListener(
                    'click',
                    function () {
                        const target = trigger.dataset.ledgerJump;

                        const tab = tabs.find(
                            function (candidate) {
                                return candidate.dataset.ledgerTab
                                    === target;
                            }
                        );

                        if (!(tab instanceof HTMLButtonElement)) {
                            return;
                        }

                        activate(tab, true);

                        const panelId = tab.getAttribute('aria-controls');
                        const panel = panelId
                            ? ledger.querySelector('#' + panelId)
                            : null;

                        if (panel instanceof HTMLElement) {
                            panel.scrollIntoView({
                                behavior: window.matchMedia(
                                    '(prefers-reduced-motion: reduce)'
                                ).matches
                                    ? 'auto'
                                    : 'smooth',
                                block: 'start',
                            });
                        }
                    }
                );
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
