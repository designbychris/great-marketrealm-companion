(function (window, document) {
    'use strict';

    const findStatus = function (desk) {
        return desk.querySelector(
            '[data-portrait-status],'
            + '.gmrc-portrait-status,'
            + '[role="status"]'
        );
    };

    const setStatus = function (desk, message) {
        const status = findStatus(desk);

        if (!(status instanceof HTMLElement)) {
            return;
        }

        status.textContent = message;
    };

    const initialise = function (desk) {
        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    setStatus(
                        desk,
                        'Auby is illuminating your Guild portrait…'
                    );

                    observer.disconnect();
                });
            },
            {
                threshold: 0.24,
            }
        );

        observer.observe(desk);

        desk.addEventListener(
            'gmrc:portrait:illumination-complete',
            function () {
                setStatus(
                    desk,
                    'Recorded in the Guild Archives.'
                );
            }
        );
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
