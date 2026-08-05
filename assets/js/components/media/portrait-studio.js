/**
 * Great Marketrealm Companion
 * Guild Illuminator / Portrait Studio
 */

(function () {
    'use strict';

    const initialisePortraitStudios = function () {
        const studios = document.querySelectorAll(
            '[data-portrait-studio]'
        );

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        studios.forEach(function (studio) {
            const form = studio.closest('form');

            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (
                studio.dataset.portraitInitialised
                    === 'true'
            ) {
                return;
            }

            studio.dataset.portraitInitialised =
                'true';

            const nameOutput = studio.querySelector(
                '[data-portrait-name]'
            );

            const raceOutput = studio.querySelector(
                '[data-portrait-race-label]'
            );

            const classOutput = studio.querySelector(
                '[data-portrait-class-label]'
            );

            const statusOutput = studio.querySelector(
                '[data-portrait-status]'
            );

            const initialOutput = studio.querySelector(
                '[data-portrait-initial]'
            );

            let nameTimer = null;
            let updateTimer = null;

            /**
             * Return a selected input.
             */
            const selectedInput = function (name) {
                return form.querySelector(
                    'input[name="'
                        + name
                        + '"]:checked'
                );
            };

            /**
             * Return the written character name.
             */
            const characterName = function () {
                const input = form.querySelector(
                    'input[name="name"]'
                );

                if (!(input instanceof HTMLInputElement)) {
                    return '';
                }

                return input.value.trim();
            };

            /**
             * Return the first visible character.
             */
            const initialFor = function (name) {
                const characters = Array.from(
                    name.trim()
                );

                return characters.length > 0
                    ? characters[0].toLocaleUpperCase()
                    : '?';
            };

            /**
             * Restart the canvas awakening animation.
             */
            const awaken = function () {
                if (reducedMotion.matches) {
                    return;
                }

                studio.classList.remove(
                    'gmrc-illuminated-portrait--updating'
                );

                void studio.offsetWidth;

                studio.classList.add(
                    'gmrc-illuminated-portrait--updating'
                );

                window.clearTimeout(
                    updateTimer
                );

                updateTimer = window.setTimeout(
                    function () {
                        studio.classList.remove(
                            'gmrc-illuminated-portrait--updating'
                        );
                    },
                    660
                );
            };

            /**
             * Synchronise the complete portrait state.
             */
            const updatePortrait = function (
                animate = true
            ) {
                const name = characterName();

                const race = selectedInput('race');

                const characterClass =
                    selectedInput('class');

                const raceValue =
                    race instanceof HTMLInputElement
                        ? race.value
                        : '';

                const raceLabel =
                    race instanceof HTMLInputElement
                        ? race.dataset.raceLabel || ''
                        : '';

                const classValue =
                    characterClass
                        instanceof HTMLInputElement
                            ? characterClass.value
                            : '';

                const classLabel =
                    characterClass
                        instanceof HTMLInputElement
                            ? characterClass.dataset
                                .classLabel || ''
                            : '';

                studio.dataset.portraitRace =
                    raceValue;

                studio.dataset.portraitClass =
                    classValue;

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--named',
                    name !== ''
                );

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--has-race',
                    raceValue !== ''
                );

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--has-class',
                    classValue !== ''
                );

                const complete =
                    name !== ''
                    && raceValue !== ''
                    && classValue !== '';

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--complete',
                    complete
                );

                if (nameOutput instanceof HTMLElement) {
                    nameOutput.textContent =
                        name || 'Awaiting Subject';
                }

                if (initialOutput instanceof SVGElement) {
                    initialOutput.textContent =
                        initialFor(name);
                }

                if (raceOutput instanceof HTMLElement) {
                    raceOutput.textContent =
                        raceLabel
                        || 'Heritage unwritten';
                }

                if (classOutput instanceof HTMLElement) {
                    classOutput.textContent =
                        classLabel
                        || 'Calling unchosen';
                }

                if (statusOutput instanceof HTMLElement) {
                    if (complete) {
                        statusOutput.textContent =
                            'Illumination complete';
                    } else if (
                        raceValue !== ''
                        && classValue !== ''
                    ) {
                        statusOutput.textContent =
                            'Awaiting the subject’s name';
                    } else if (raceValue !== '') {
                        statusOutput.textContent =
                            'Sketch complete — awaiting attire';
                    } else if (classValue !== '') {
                        statusOutput.textContent =
                            'Calling recorded — awaiting heritage';
                    } else {
                        statusOutput.textContent =
                            'Portrait awaiting inscription';
                    }
                }

                if (animate) {
                    awaken();
                }
            };

            const nameInput = form.querySelector(
                'input[name="name"]'
            );

            if (
                nameInput
                    instanceof HTMLInputElement
            ) {
                nameInput.addEventListener(
                    'input',
                    function () {
                        window.clearTimeout(
                            nameTimer
                        );

                        nameTimer = window.setTimeout(
                            function () {
                                updatePortrait(true);
                            },
                            280
                        );
                    }
                );
            }

            form.addEventListener(
                'change',
                function (event) {
                    const target = event.target;

                    if (
                        !(target
                            instanceof HTMLInputElement)
                    ) {
                        return;
                    }

                    if (
                        target.name !== 'race'
                        && target.name !== 'class'
                    ) {
                        return;
                    }

                    updatePortrait(true);
                }
            );

            updatePortrait(false);

            window.addEventListener(
                'pagehide',
                function () {
                    window.clearTimeout(
                        nameTimer
                    );

                    window.clearTimeout(
                        updateTimer
                    );
                },
                {
                    once: true,
                }
            );
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initialisePortraitStudios
        );
    } else {
        initialisePortraitStudios();
    }
})();
