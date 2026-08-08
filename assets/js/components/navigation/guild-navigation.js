(function (window, document) {
    'use strict';

    const mobileMedia = window.matchMedia(
        '(max-width: 900px)'
    );

    const closeNavigation = function (
        root,
        toggle,
        restoreFocus
    ) {
        root.classList.remove(
            'is-navigation-open'
        );

        toggle.setAttribute(
            'aria-expanded',
            'false'
        );

        if (restoreFocus) {
            toggle.focus();
        }
    };

    const openNavigation = function (
        root,
        toggle
    ) {
        root.classList.add(
            'is-navigation-open'
        );

        toggle.setAttribute(
            'aria-expanded',
            'true'
        );
    };

    const initialise = function (root) {
        if (!(root instanceof HTMLElement)) {
            return;
        }

        const toggle = root.querySelector(
            '[data-guild-navigation-toggle]'
        );

        const menu = root.querySelector(
            '[data-guild-navigation-menu]'
        );

        if (
            !(toggle instanceof HTMLButtonElement)
            || !(menu instanceof HTMLElement)
        ) {
            return;
        }

        toggle.addEventListener(
            'click',
            function () {
                const isOpen =
                    toggle.getAttribute(
                        'aria-expanded'
                    ) === 'true';

                if (isOpen) {
                    closeNavigation(
                        root,
                        toggle,
                        false
                    );

                    return;
                }

                openNavigation(
                    root,
                    toggle
                );
            }
        );

        root.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key !== 'Escape'
                    || !root.classList.contains(
                        'is-navigation-open'
                    )
                ) {
                    return;
                }

                event.preventDefault();

                closeNavigation(
                    root,
                    toggle,
                    true
                );
            }
        );

        menu.addEventListener(
            'click',
            function (event) {
                if (
                    !mobileMedia.matches
                    || !(
                        event.target instanceof Element
                    )
                    || !event.target.closest('a')
                ) {
                    return;
                }

                closeNavigation(
                    root,
                    toggle,
                    false
                );
            }
        );

        mobileMedia.addEventListener(
            'change',
            function (event) {
                if (!event.matches) {
                    closeNavigation(
                        root,
                        toggle,
                        false
                    );
                }
            }
        );
    };

    const boot = function () {
        document
            .querySelectorAll(
                '[data-guild-navigation]'
            )
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
