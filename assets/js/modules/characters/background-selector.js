/**
 * Great Marketrealm Companion
 * Background Parchment Selector
 */

document.addEventListener(
    'DOMContentLoaded',
    function () {
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

            const reducedMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            );

            /**
             * Find the card belonging to a background value.
             */
            const findOption = function (value) {
                return Array.from(options).find(
                    function (option) {
                        const input = option.querySelector(
                            'input[name="background"]'
                        );

                        return (
                            input instanceof HTMLInputElement
                            && input.value === value
                        );
                    }
                );
            };

            /**
             * Animate the selected parchment becoming active.
             */
            const animateSelection = function (option) {
                if (
                    !option
                    || reducedMotion.matches
                    || typeof option.animate !== 'function'
                ) {
                    return;
                }

                option.animate(
                    [
                        {
                            filter: 'brightness(1)',
                        },
                        {
                            filter: 'brightness(1.08)',
                        },
                        {
                            filter: 'brightness(1)',
                        },
                    ],
                    {
                        duration: 420,
                        easing: 'ease-out',
                    }
                );
            };

            /**
             * Update card and preview presentation.
             */
            const updateDisplay = function (
                value,
                shouldAnimate = true
            ) {
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

                if (shouldAnimate) {
                    animateSelection(
                        findOption(value)
                    );
                }
            };

            inputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    function () {
                        updateDisplay(
                            input.value
                        );
                    }
                );
            });

            const checked = selector.querySelector(
                'input[name="background"]:checked'
            );

            if (checked instanceof HTMLInputElement) {
                /*
                 * Establish the initial visual state without replaying
                 * the selection brightness animation on every page load.
                 */
                updateDisplay(
                    checked.value,
                    false
                );
            }
        });
    }
);
