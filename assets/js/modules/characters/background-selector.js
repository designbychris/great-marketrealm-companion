/**
 * Great Marketrealm Companion
 * Background Parchment Selector
 */

document.addEventListener('DOMContentLoaded', function () {
    const selectors = document.querySelectorAll(
        '.gmrc-background-selector'
    );

    selectors.forEach(function (selector) {
        const inputs = selector.querySelectorAll(
            'input[name="background"]'
        );

        const options = selector.querySelectorAll(
            '[data-background-option]'
        );

        const form = selector.closest('form');

        const previewPanels = form
            ? form.querySelectorAll(
                '[data-background-preview-panel]'
            )
            : document.querySelectorAll(
                '[data-background-preview-panel]'
            );

        /**
         * Update card and preview presentation.
         */
        const updateDisplay = function (value) {
            options.forEach(function (option) {
                const input = option.querySelector(
                    'input[name="background"]'
                );

                const details = option.querySelector(
                    '[data-background-details]'
                );

                const selected =
                    input instanceof HTMLInputElement
                    && input.value === value;

                option.classList.toggle(
                    'gmrc-background-option--selected',
                    selected
                );

                option.setAttribute(
                    'aria-current',
                    selected
                        ? 'true'
                        : 'false'
                );

                if (details) {
                    details.hidden = !selected;
                }
            });

            previewPanels.forEach(function (panel) {
                const panelValue =
                    panel.getAttribute(
                        'data-background-preview-panel'
                    );

                panel.hidden =
                    panelValue !== value;
            });
        };

        inputs.forEach(function (input) {
            input.addEventListener(
                'change',
                function () {
                    updateDisplay(input.value);
                }
            );
        });

        const checked = selector.querySelector(
            'input[name="background"]:checked'
        );

        if (checked instanceof HTMLInputElement) {
            updateDisplay(checked.value);
        }
    });
});
