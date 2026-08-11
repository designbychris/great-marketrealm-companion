(function (window, document) {
    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    const timers = new WeakMap();

    /**
     * Phase III.7.3.4 — The Living Illuminator.
     *
     * Generation 1 / expanded-library portraits do not have the bespoke
     * painted anatomy hooks used by Project Golden Apple.  Instead they
     * receive a deliberately subtle race-led motion profile.  Keeping the
     * profile on the SVG dataset means CSS owns the movement while this
     * controller remains responsible for lifecycle and accessibility.
     */
    const livingProfiles = Object.freeze({
        fructan: 'sprightly',
        vegfolk: 'sprightly',
        herbfolk: 'sprightly',
        rootkin: 'grounded',
        fungifolk: 'sporebound',
        'drink-folk': 'effervescent',
        boxfolk: 'clockwork',
        dairyfolk: 'gentle',
        sweetfolk: 'buoyant',
        fluffling: 'buoyant',
        'marshmallow-folk': 'buoyant',
        frostreem: 'frosted',
        meatfolk: 'grounded',
        meatkin: 'grounded',
        recalled: 'uncanny',
        melonian: 'sprightly',
        stalker: 'sprightly'
    });

    const applyLivingProfile = function (studio, portrait) {
        if (portrait.dataset.portraitGeneration === '2') {
            portrait.dataset.livingPortrait = 'golden-apple';
            return;
        }

        const race = (studio.dataset.portraitRace || '')
            .trim()
            .toLowerCase();

        const profile = livingProfiles[race] || 'gentle';

        portrait.dataset.livingPortrait = profile;
        portrait.dataset.livingReady = 'true';
    };

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

        const eyelids = portrait.querySelector(
            '.gmrc-g2-eyelids'
        );

        if (
            !(eyes instanceof SVGElement)
            || !(eyelids instanceof SVGElement)
        ) {
            return;
        }

        const setBlinkState = function (isBlinking) {
            eyes.classList.toggle(
                'is-blinking',
                isBlinking
            );

            eyelids.classList.toggle(
                'is-blinking',
                isBlinking
            );
        };

        /*
         * Natural-looking interval: usually 4–9 seconds, with an
         * occasional quicker follow-up blink.
         */
        const delay = 4000 + Math.random() * 5000;

        const timer = window.setTimeout(function () {
            setBlinkState(true);

            window.setTimeout(function () {
                setBlinkState(false);

                const doubleBlink = Math.random() < 0.18;

                if (doubleBlink) {
                    window.setTimeout(function () {
                        setBlinkState(true);

                        window.setTimeout(function () {
                            setBlinkState(false);
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

        applyLivingProfile(studio, portrait);

        const persisted =
            studio.dataset.portraitPersisted === 'true';

        if (
            persisted
            && portrait.dataset.portraitGeneration === '2'
        ) {
            portrait.dataset.illuminationReady = 'true';
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
                applyLivingProfile(studio, portrait);
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
