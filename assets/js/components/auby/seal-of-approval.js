(function (window, document) {
    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    const surfaceFor = function (seal) {
        return seal.closest(
            '[data-auby-note],'
            + '.gmrc-living-desk__illuminator,'
            + '[data-auby-seal-surface]'
        );
    };

    const stamp = function (seal) {
        if (
            !(seal instanceof HTMLElement)
            || seal.dataset.aubySealStamped === 'true'
        ) {
            return;
        }

        seal.dataset.aubySealStamped = 'true';

        if (reducedMotion.matches) {
            seal.classList.add('is-approved');
            return;
        }

        const surface = surfaceFor(seal);

        seal.classList.remove(
            'is-approved',
            'is-stamping'
        );

        void seal.offsetWidth;

        seal.classList.add('is-stamping');

        window.setTimeout(function () {
            if (surface instanceof HTMLElement) {
                surface.classList.add(
                    'gmrc-auby-seal-surface--impact'
                );

                window.setTimeout(function () {
                    surface.classList.remove(
                        'gmrc-auby-seal-surface--impact'
                    );
                }, 260);
            }
        }, 300);

        window.setTimeout(function () {
            seal.classList.remove('is-stamping');
            seal.classList.add('is-approved');

            seal.dispatchEvent(
                new CustomEvent(
                    'gmrc:auby-seal:approved',
                    {
                        bubbles: true,
                    }
                )
            );
        }, 860);
    };

    const initialiseVisibleSeal = function (seal) {
        if (
            reducedMotion.matches
            || !('IntersectionObserver' in window)
        ) {
            stamp(seal);
            return;
        }

        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    stamp(seal);
                    observer.disconnect();
                });
            },
            {
                threshold: 0.45,
            }
        );

        observer.observe(seal);
    };

    const initialiseSeal = function (seal) {
        if (!(seal instanceof HTMLElement)) {
            return;
        }

        const trigger =
            seal.dataset.aubySealTrigger
            || 'visible';

        if (trigger === 'static') {
            seal.classList.add(
                'gmrc-auby-seal--static',
                'is-approved'
            );
            seal.dataset.aubySealStamped = 'true';
            return;
        }

        if (trigger === 'visible') {
            initialiseVisibleSeal(seal);
        }
    };

    const stampManualSeals = function (root) {
        root
            .querySelectorAll(
                '[data-auby-seal]'
                + '[data-auby-seal-trigger="manual"]'
            )
            .forEach(function (seal) {
                stamp(seal);
            });
    };

    const boot = function () {
        document
            .querySelectorAll('[data-auby-seal]')
            .forEach(initialiseSeal);

        document
            .querySelectorAll('[data-living-desk]')
            .forEach(function (desk) {
                desk.addEventListener(
                    'gmrc:portrait:illumination-complete',
                    function () {
                        /*
                         * Give Auby's painted flourish a tiny beat
                         * before the physical approval stamp lands.
                         */
                        window.setTimeout(function () {
                            stampManualSeals(desk);
                        }, 170);
                    }
                );
            });

        document.addEventListener(
            'gmrc:auby:approve',
            function (event) {
                const root =
                    event.target instanceof Element
                        ? event.target
                        : document;

                stampManualSeals(root);
            }
        );
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
