(function (window, document) {
    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    const reveal = function (note) {
        note.classList.add('is-revealed');
    };

    const initialise = function (note) {
        if (!(note instanceof HTMLElement)) {
            return;
        }

        if (
            reducedMotion.matches
            || !('IntersectionObserver' in window)
        ) {
            reveal(note);
            return;
        }

        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    reveal(note);
                    observer.disconnect();
                });
            },
            {
                threshold: 0.3,
            }
        );

        observer.observe(note);
    };

    const boot = function () {
        document
            .querySelectorAll(
                '[data-auby-sticky-note]'
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
