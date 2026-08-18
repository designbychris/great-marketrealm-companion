(function (window, document) {
    'use strict';

    const initialise = function (hall) {
        if (!(hall instanceof HTMLElement)) {
            return;
        }

        const tabs = Array.from(
            hall.querySelectorAll(
                '[role="tab"][data-fellowship-tab]'
            )
        );

        const panels = Array.from(
            hall.querySelectorAll(
                '[role="tabpanel"][data-fellowship-panel]'
            )
        );

        if (tabs.length === 0 || panels.length === 0) {
            return;
        }

        const fellowshipId = hall.dataset.fellowshipId || '';
        const storageKey = fellowshipId === ''
            ? ''
            : 'gmrc:fellowship-hall:' + fellowshipId + ':tab';

        const activate = function (tab, moveFocus, persist) {
            if (!(tab instanceof HTMLButtonElement)) {
                return;
            }

            const panelId = tab.getAttribute('aria-controls');

            tabs.forEach(function (candidate) {
                const active = candidate === tab;

                candidate.classList.toggle(
                    'is-active',
                    active
                );
                candidate.setAttribute(
                    'aria-selected',
                    active ? 'true' : 'false'
                );
                candidate.tabIndex = active ? 0 : -1;
            });

            panels.forEach(function (panel) {
                const active = panel.id === panelId;

                panel.hidden = !active;
                panel.classList.toggle(
                    'is-active',
                    active
                );
            });

            hall.dataset.fellowshipHallTab =
                tab.dataset.fellowshipTab || 'overview';

            if (persist && storageKey !== '') {
                try {
                    window.localStorage.setItem(
                        storageKey,
                        hall.dataset.fellowshipHallTab
                    );
                } catch (error) {
                    // Storage may be unavailable in private contexts.
                }
            }

            if (moveFocus) {
                tab.focus();
            }
        };

        tabs.forEach(function (tab, index) {
            tab.addEventListener(
                'click',
                function () {
                    activate(tab, false, true);
                }
            );

            tab.addEventListener(
                'keydown',
                function (event) {
                    let next = null;

                    if (event.key === 'ArrowRight') {
                        next = tabs[
                            (index + 1) % tabs.length
                        ];
                    } else if (event.key === 'ArrowLeft') {
                        next = tabs[
                            (index - 1 + tabs.length)
                            % tabs.length
                        ];
                    } else if (event.key === 'Home') {
                        next = tabs[0];
                    } else if (event.key === 'End') {
                        next = tabs[tabs.length - 1];
                    }

                    if (!(next instanceof HTMLButtonElement)) {
                        return;
                    }

                    event.preventDefault();
                    activate(next, true, true);
                }
            );
        });

        const requested = new URLSearchParams(
            window.location.search
        ).get('gmrc_fellowship_tab');

        let remembered = '';

        if (storageKey !== '') {
            try {
                remembered =
                    window.localStorage.getItem(storageKey)
                    || '';
            } catch (error) {
                remembered = '';
            }
        }

        const requestedTab = tabs.find(
            function (tab) {
                return tab.dataset.fellowshipTab
                    === requested;
            }
        );

        const rememberedTab = tabs.find(
            function (tab) {
                return tab.dataset.fellowshipTab
                    === remembered;
            }
        );

        const selected = tabs.find(
            function (tab) {
                return tab.getAttribute('aria-selected')
                    === 'true';
            }
        );

        hall.classList.add('is-ready');

        activate(
            requestedTab instanceof HTMLButtonElement
                ? requestedTab
                : (
                    rememberedTab instanceof HTMLButtonElement
                        ? rememberedTab
                        : (
                            selected instanceof HTMLButtonElement
                                ? selected
                                : tabs[0]
                        )
                ),
            false,
            false
        );
    };

    const boot = function () {
        document
            .querySelectorAll('[data-fellowship-tabs]')
            .forEach(initialise);
    };

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            boot
        );
    } else {
        boot();
    }
})(window, document);
