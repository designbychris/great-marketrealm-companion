(function (window, document) {
    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    const revealOrder = [
        ['.gmrc-g2-background', 0],
        ['.gmrc-g2-ground-shadow', 180],
        ['.gmrc-g2-body', 340],
        ['.gmrc-g2-face', 620],
        ['.gmrc-g2-leaves, .gmrc-g2-stem', 780],
        ['.gmrc-g2-eyes, .gmrc-g2-brows, .gmrc-g2-mouth', 980],
        ['.gmrc-g2-outfit', 1160],
        ['.gmrc-g2-hands, .gmrc-g2-boots', 1380],
        ['.gmrc-g2-ledger, .gmrc-g2-satchel', 1540],
        ['.gmrc-g2-pollen', 1760],
        ['.gmrc-g2-frame', 1940],
        ['.gmrc-g2-auby-mark', 2220],
    ];

    const preparePortrait = function (desk) {
        const portrait = desk.querySelector(
            '.gmrc-portrait-layers'
        );

        if (!(portrait instanceof SVGElement)) {
            return null;
        }

        portrait.dataset.illuminationReady = 'false';
        portrait.classList.add(
            'gmrc-g2-illumination-pending'
        );

        return portrait;
    };

    const assignRevealDelays = function (portrait) {
        revealOrder.forEach(function (entry) {
            portrait
                .querySelectorAll(entry[0])
                .forEach(function (layer, index) {
                    layer.style.setProperty(
                        '--gmrc-reveal-delay',
                        (
                            entry[1]
                            + (index * 55)
                        )
                        + 'ms'
                    );
                });
        });
    };

    const completeIllumination = function (
        desk,
        portrait
    ) {
        portrait.dataset.illuminationReady = 'true';
        portrait.classList.remove(
            'gmrc-g2-illumination-pending'
        );
        portrait.classList.add(
            'gmrc-g2-illumination-complete'
        );

        desk.classList.add(
            'gmrc-living-desk--illuminated'
        );

        desk.dispatchEvent(
            new CustomEvent(
                'gmrc:portrait:illumination-complete',
                {
                    bubbles: true,
                }
            )
        );
    };

    const illuminate = function (desk, portrait) {
        if (
            desk.dataset.illuminationStarted === 'true'
        ) {
            return;
        }

        desk.dataset.illuminationStarted = 'true';
        desk.classList.add(
            'gmrc-living-desk--in-view'
        );

        if (reducedMotion.matches) {
            completeIllumination(desk, portrait);
            return;
        }

        assignRevealDelays(portrait);

        portrait.classList.add(
            'gmrc-g2-illumination-active'
        );

        window.setTimeout(function () {
            completeIllumination(desk, portrait);
        }, 2550);
    };

    const initialise = function (desk) {
        const portrait = preparePortrait(desk);

        if (!(portrait instanceof SVGElement)) {
            return;
        }

        const refreshLayers = function () {
            if (
                desk.dataset.illuminationStarted !== 'true'
            ) {
                portrait.classList.add(
                    'gmrc-g2-illumination-pending'
                );
            }

            assignRevealDelays(portrait);
        };

        desk.addEventListener(
            'gmrc:portrait:generation-changed',
            refreshLayers
        );

        if (
            reducedMotion.matches
            || !('IntersectionObserver' in window)
        ) {
            illuminate(desk, portrait);
            return;
        }

        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    illuminate(desk, portrait);
                    observer.disconnect();
                });
            },
            {
                threshold: 0.24,
                rootMargin: '0px 0px -10% 0px',
            }
        );

        observer.observe(desk);
    };

    const boot = function () {
        document
            .querySelectorAll('[data-living-desk]')
            .forEach(function (desk) {
                if (desk instanceof HTMLElement) {
                    initialise(desk);
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
