(function (window, document) {
    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    const timers = new WeakMap();

    const clearTimer = function (portrait) {
        const timer = timers.get(portrait);

        if (timer) {
            window.clearTimeout(timer);
            timers.delete(portrait);
        }
    };

    const scheduleBlink = function (portrait) {
        clearTimer(portrait);

        if (
            reducedMotion.matches
            || document.hidden
            || portrait.dataset.portraitGeneration !== '2'
            || portrait.dataset.illuminationReady !== 'true'
        ) {
            return;
        }

        const eyes = portrait.querySelector(
            '.gmrc-g2-eyes'
        );

        if (!(eyes instanceof SVGElement)) {
            return;
        }

        /*
         * Natural-looking interval: usually 4–9 seconds, with an
         * occasional quicker follow-up blink.
         */
        const delay = 4000 + Math.random() * 5000;

        const timer = window.setTimeout(function () {
            eyes.classList.add('is-blinking');

            window.setTimeout(function () {
                eyes.classList.remove('is-blinking');

                const doubleBlink = Math.random() < 0.18;

                if (doubleBlink) {
                    window.setTimeout(function () {
                        eyes.classList.add('is-blinking');

                        window.setTimeout(function () {
                            eyes.classList.remove(
                                'is-blinking'
                            );
                            scheduleBlink(portrait);
                        }, 115);
                    }, 190);

                    return;
                }

                scheduleBlink(portrait);
            }, 125);
        }, delay);

        timers.set(portrait, timer);
    };

    const initialise = function (studio) {
        const portrait = studio.querySelector(
            '.gmrc-portrait-layers'
        );

        if (!(portrait instanceof SVGElement)) {
            return;
        }

        scheduleBlink(portrait);

        const readinessObserver = new MutationObserver(
            function () {
                if (
                    portrait.dataset.illuminationReady
                    !== 'true'
                ) {
                    return;
                }

                readinessObserver.disconnect();
                scheduleBlink(portrait);
            }
        );

        readinessObserver.observe(
            portrait,
            {
                attributes: true,
                attributeFilter: [
                    'data-illumination-ready',
                ],
            }
        );

        studio.addEventListener(
            'gmrc:portrait:generation-changed',
            function () {
                scheduleBlink(portrait);
            }
        );

        const desk = studio.closest(
            '[data-living-desk]'
        );

        if (desk instanceof HTMLElement) {
            desk.addEventListener(
                'gmrc:portrait:illumination-complete',
                function () {
                    window.setTimeout(function () {
                        scheduleBlink(portrait);
                    }, 120);
                }
            );
        }
    };

    const refresh = function () {
        document
            .querySelectorAll(
                '.gmrc-illuminated-portrait'
            )
            .forEach(function (studio) {
                if (studio instanceof HTMLElement) {
                    const portrait = studio.querySelector(
                        '.gmrc-portrait-layers'
                    );

                    if (portrait instanceof SVGElement) {
                        scheduleBlink(portrait);
                    }
                }
            });
    };

    document.addEventListener(
        'visibilitychange',
        refresh
    );

    reducedMotion.addEventListener(
        'change',
        refresh
    );

    const boot = function () {
        document
            .querySelectorAll(
                '.gmrc-illuminated-portrait'
            )
            .forEach(function (studio) {
                if (studio instanceof HTMLElement) {
                    initialise(studio);
                }
            });
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
