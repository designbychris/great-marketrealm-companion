(function () {
    'use strict';

    const filter = function (select, parent) {
        if (!(select instanceof HTMLSelectElement)) {
            return;
        }

        let selectedStillVisible =
            select.value === '';

        Array.from(select.options).forEach(
            function (option) {
                if (option.value === '') {
                    option.hidden = false;
                    return;
                }

                const visible =
                    option.dataset.parent === parent;

                option.hidden = !visible;
                option.disabled = !visible;

                if (
                    visible
                    && option.value === select.value
                ) {
                    selectedStillVisible = true;
                }
            }
        );

        if (!selectedStillVisible) {
            select.value = '';
            select.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles: true
                    }
                )
            );
        }
    };

    const init = function () {
        const heritage =
            document.querySelector(
                '[data-catalogue-child="heritage"]'
            );

        const subclass =
            document.querySelector(
                '[data-catalogue-child="subclass"]'
            );

        const race = function () {
            return document
                .querySelector(
                    'input[name="race"]:checked'
                )?.value || '';
        };

        const characterClass = function () {
            return document
                .querySelector(
                    'input[name="class"]:checked'
                )?.value || '';
        };

        const refreshSubclassPreview = function () {
            const selected =
                subclass instanceof HTMLSelectElement
                    ? subclass.value
                    : '';

            document
                .querySelectorAll(
                    '[data-subclass-preview]'
                )
                .forEach(
                    function (preview) {
                        if (!(preview instanceof HTMLElement)) {
                            return;
                        }

                        const visible =
                            selected !== ''
                            && preview.dataset.subclassPreview
                                === selected
                            && preview.dataset.parent
                                === characterClass();

                        preview.hidden = !visible;
                    }
                );
        };

        const refresh = function () {
            filter(
                heritage,
                race()
            );

            filter(
                subclass,
                characterClass()
            );

            refreshSubclassPreview();
        };

        document.addEventListener(
            'change',
            function (event) {
                const target = event.target;

                if (!(target instanceof HTMLElement)) {
                    return;
                }

                if (
                    target.matches(
                        'input[name="race"], input[name="class"]'
                    )
                ) {
                    refresh();
                }

                /*
                 * Heritage is now a first-class Catalogue selection.
                 * Portrait Studio reads this select directly and resolves it
                 * to a concrete race-heritage-* asset. Do not write the raw
                 * key into portrait_heritage, which stores the rendered asset
                 * identifier for persistence.
                 */
                if (
                    target === subclass
                    && subclass instanceof HTMLSelectElement
                ) {
                    refreshSubclassPreview();
                }

                if (
                    target === heritage
                    && heritage instanceof HTMLSelectElement
                ) {
                    heritage.dispatchEvent(
                        new CustomEvent(
                            'gmrc:catalogue:heritage-changed',
                            {
                                bubbles: true,
                                detail: {
                                    race: race(),
                                    heritage:
                                        heritage.value,
                                },
                            }
                        )
                    );
                }
            }
        );

        refresh();
    };

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            init
        );
    } else {
        init();
    }
})();
