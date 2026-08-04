/**
 * Great Marketrealm Companion
 * Living Ledger Choice Selector
 *
 * Shared by Race and Class cards.
 */

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const selectors = document.querySelectorAll(
            '[data-choice-selector]'
        );

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        selectors.forEach(function (selector) {
            const fieldName = selector.getAttribute(
                'data-choice-selector'
            );

            if (!fieldName) {
                return;
            }

            const inputs = selector.querySelectorAll(
                'input[name="' + fieldName + '"]'
            );

            const cards = selector.querySelectorAll(
                '[data-choice-card]'
            );

            /**
             * Find the card associated with a value.
             */
            const findCard = function (value) {
                return Array.from(cards).find(
                    function (card) {
                        const input = card.querySelector(
                            'input[name="'
                                + fieldName
                                + '"]'
                        );

                        return (
                            input instanceof HTMLInputElement
                            && input.value === value
                        );
                    }
                );
            };

            /**
             * Add a subtle enchanted brightness pulse.
             */
            const animateCard = function (card) {
                if (
                    !card
                    || reducedMotion.matches
                    || typeof card.animate !== 'function'
                ) {
                    return;
                }

                card.animate(
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
             * Update the selected cards.
             */
            const updateDisplay = function (
                value,
                shouldAnimate = true
            ) {
                cards.forEach(function (card) {
                    const input = card.querySelector(
                        'input[name="'
                            + fieldName
                            + '"]'
                    );

                    const details = card.querySelector(
                        '[data-choice-details]'
                    );

                    const selected =
                        input instanceof HTMLInputElement
                        && input.value === value;

                    card.classList.toggle(
                        'gmrc-choice-card--selected',
                        selected
                    );

                    card.setAttribute(
                        'aria-current',
                        selected
                            ? 'true'
                            : 'false'
                    );

                    if (details) {
                        details.hidden = !selected;
                    }
                });

                if (shouldAnimate) {
                    animateCard(
                        findCard(value)
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
                'input[name="'
                    + fieldName
                    + '"]:checked'
            );

            if (checked instanceof HTMLInputElement) {
                updateDisplay(
                    checked.value,
                    false
                );
            }
        });
    }
);
