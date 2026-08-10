/**
 * Phase III.2.1 — Illuminator's Workbench.
 */
(function () {
    'use strict';

    document.addEventListener('change', function (event) {
        const input = event.target;

        if (
            ! (input instanceof HTMLInputElement)
            || ! input.matches('[data-gmrc-portrait-file]')
        ) {
            return;
        }

        const form = input.closest(
            '.gmrc-illuminator-workbench'
        );

        const output = form
            ? form.querySelector(
                '[data-gmrc-portrait-file-name]'
            )
            : null;

        if (! (output instanceof HTMLElement)) {
            return;
        }

        const file = input.files && input.files[0];

        output.textContent = file
            ? 'Selected: ' + file.name
            : 'No image selected.';
    });
})();
