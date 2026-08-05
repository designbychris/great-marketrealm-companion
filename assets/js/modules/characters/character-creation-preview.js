/**
 * Great Marketrealm Companion
 * Living Character Creation Preview
 */

(function () {
    'use strict';

    const initialisePreviews = function () {
        const previews = document.querySelectorAll(
            '[data-character-creation-preview]'
        );

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        previews.forEach(function (preview) {
            const form = preview.closest('form');

            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            const nameInput = form.querySelector(
                'input[name="name"]'
            );

            const raceInputs = form.querySelectorAll(
                'input[name="race"]'
            );

            const classInputs = form.querySelectorAll(
                'input[name="class"]'
            );

            const nameOutput = preview.querySelector(
                '[data-preview-name]'
            );

            const raceOutput = preview.querySelector(
                '[data-preview-race]'
            );

            const classOutput = preview.querySelector(
                '[data-preview-class]'
            );

            const hitDieOutput = preview.querySelector(
                '[data-preview-hit-die]'
            );

            const hitPointsOutput = preview.querySelector(
                '[data-preview-hit-points]'
            );

            const savingThrowsOutput =
                preview.querySelector(
                    '[data-preview-saving-throws]'
                );

            const noteOutput = preview.querySelector(
                '[data-preview-note]'
            );

            let awakenTimer = null;
            let nameTimer = null;
            let initialised = false;

            /**
             * Dispatch work to the Guild Registrar.
             */
            const registrarWrite = function (
                anchor,
                rustle = false
            ) {
                preview.dispatchEvent(
                    new CustomEvent(
                        'gmrc:registrar-write',
                        {
                            detail: {
                                anchor: anchor,
                                rustle: rustle,
                            },
                        }
                    )
                );
            };

            /**
             * Dispatch a sequence of fields.
             */
            const registrarSequence = function (
                anchors,
                rustle = false
            ) {
                preview.dispatchEvent(
                    new CustomEvent(
                        'gmrc:registrar-sequence',
                        {
                            detail: {
                                anchors: anchors,
                                rustle: rustle,
                            },
                        }
                    )
                );
            };

            /**
             * Return the quill to its inkwell.
             */
            const registrarRest = function () {
                preview.dispatchEvent(
                    new CustomEvent(
                        'gmrc:registrar-rest'
                    )
                );
            };

            /**
             * Update a field and prepare it for writing.
             */
            const setValue = function (
                element,
                value,
                anchor,
                animate = true
            ) {
                if (!(element instanceof HTMLElement)) {
                    return;
                }

                element.textContent = value;

                element.classList.remove(
                    'gmrc-preview-ink--writing',
                    'gmrc-register-anchor--writing',
                    'gmrc-register-anchor--written'
                );

                if (
                    !animate
                    || reducedMotion.matches
                    || preview.dataset.registrarReady
                        !== 'true'
                ) {
                    element.classList.remove(
                        'gmrc-register-anchor--pending'
                    );

                    element.classList.add(
                        'gmrc-register-anchor--written'
                    );

                    return;
                }

                element.classList.add(
                    'gmrc-register-anchor--pending'
                );

                registrarWrite(anchor);
            };

            /**
             * Briefly awaken the parchment.
             */
            const awakenPreview = function () {
                if (
                    !preview.classList.contains(
                        'gmrc-creation-preview--visible'
                    )
                    || reducedMotion.matches
                ) {
                    return;
                }

                preview.classList.remove(
                    'gmrc-creation-preview--awakened'
                );

                void preview.offsetWidth;

                preview.classList.add(
                    'gmrc-creation-preview--awakened'
                );

                window.clearTimeout(
                    awakenTimer
                );

                awakenTimer = window.setTimeout(
                    function () {
                        preview.classList.remove(
                            'gmrc-creation-preview--awakened'
                        );
                    },
                    520
                );
            };

            /**
             * Return a selected radio input.
             */
            const checkedInput = function (name) {
                return form.querySelector(
                    'input[name="'
                        + name
                        + '"]:checked'
                );
            };

            /**
             * Update the contextual archive note.
             */
            const updateNote = function (
                animate = true
            ) {
                const race = checkedInput('race');

                const characterClass =
                    checkedInput('class');

                let message =
                    'Choose a race and class to begin this '
                    + 'adventurer’s first inscription.';

                if (
                    race instanceof HTMLInputElement
                    && characterClass
                        instanceof HTMLInputElement
                ) {
                    message =
                        'The Archive prepares a new page for this '
                        + (
                            race.dataset.raceLabel
                            || 'Marketrealm'
                        )
                        + ' '
                        + (
                            characterClass.dataset.classLabel
                            || 'adventurer'
                        )
                        + '.';
                } else if (
                    race instanceof HTMLInputElement
                ) {
                    message =
                        'Their '
                        + (
                            race.dataset.raceLabel
                            || 'Marketrealm'
                        )
                        + ' heritage has been recorded. '
                        + 'Choose a class to complete the inscription.';
                } else if (
                    characterClass
                        instanceof HTMLInputElement
                ) {
                    message =
                        'The path of the '
                        + (
                            characterClass.dataset.classLabel
                            || 'adventurer'
                        )
                        + ' has been chosen. '
                        + 'Their heritage remains unwritten.';
                }

                setValue(
                    noteOutput,
                    message,
                    'note',
                    animate
                );
            };

            /**
             * Update the adventurer name.
             */
            const updateName = function (
                animate = true
            ) {
                const value =
                    nameInput
                        instanceof HTMLInputElement
                        ? nameInput.value.trim()
                        : '';

                setValue(
                    nameOutput,
                    value !== ''
                        ? value
                        : 'Unnamed Adventurer',
                    'name',
                    animate
                );

                awakenPreview();
            };

            /**
             * Update the selected race.
             */
            const updateRace = function (
                animate = true
            ) {
                const selected =
                    checkedInput('race');

                const label =
                    selected
                        instanceof HTMLInputElement
                        ? selected.dataset.raceLabel
                        : '';

                setValue(
                    raceOutput,
                    label
                        || 'Heritage awaiting selection',
                    'race',
                    animate
                );

                updateNote(animate);
                awakenPreview();
            };

            /**
             * Update class-derived fields.
             */
            const updateClass = function (
                animate = true
            ) {
                const selected =
                    checkedInput('class');

                if (
                    !(selected
                        instanceof HTMLInputElement)
                ) {
                    setValue(
                        classOutput,
                        'Class awaiting selection',
                        'class',
                        animate
                    );

                    setValue(
                        hitDieOutput,
                        '—',
                        'hitdie',
                        animate
                    );

                    setValue(
                        hitPointsOutput,
                        '—',
                        'hp',
                        animate
                    );

                    setValue(
                        savingThrowsOutput,
                        'Choose a class to reveal its '
                            + 'defensive training.',
                        'saving',
                        animate
                    );

                    updateNote(animate);

                    return;
                }

                /*
                 * Set the values before asking the Registrar
                 * to write them as one ordered sequence.
                 */
                const values = [
                    [
                        classOutput,
                        selected.dataset.classLabel
                            || 'Unknown Class',
                    ],
                    [
                        hitDieOutput,
                        selected.dataset.hitDie
                            ? 'd'
                                + selected.dataset.hitDie
                            : '—',
                    ],
                    [
                        hitPointsOutput,
                        selected.dataset.startingHitPoints
                            || '—',
                    ],
                    [
                        savingThrowsOutput,
                        selected.dataset.savingThrows
                            || 'None recorded',
                    ],
                ];

                values.forEach(function (item) {
                    const element = item[0];
                    const value = item[1];

                    if (
                        element
                            instanceof HTMLElement
                    ) {
                        element.textContent = value;

                        element.classList.remove(
                            'gmrc-register-anchor--writing',
                            'gmrc-register-anchor--written'
                        );

                        if (
                            animate
                            && !reducedMotion.matches
                            && preview.dataset.registrarReady
                                === 'true'
                        ) {
                            element.classList.add(
                                'gmrc-register-anchor--pending'
                            );
                        } else {
                            element.classList.remove(
                                'gmrc-register-anchor--pending'
                            );

                            element.classList.add(
                                'gmrc-register-anchor--written'
                            );
                        }
                    }
                });

                if (
                    animate
                    && !reducedMotion.matches
                    && preview.dataset.registrarReady
                        === 'true'
                ) {
                    registrarSequence(
                        [
                            'class',
                            'hitdie',
                            'hp',
                            'saving',
                        ],
                        true
                    );
                }

                updateNote(animate);
                awakenPreview();
            };

            /**
             * React after the user pauses while typing.
             */
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
                                updateName(true);
                            },
                            360
                        );
                    }
                );
            }

            raceInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    function () {
                        updateRace(true);
                    }
                );
            });

            classInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    function () {
                        updateClass(true);
                    }
                );
            });

            /**
             * Populate initial content without replaying all
             * animations before the preview is visible.
             */
            updateName(false);
            updateRace(false);
            updateClass(false);

            initialised = true;

            /**
             * Reveal and optionally inscribe restored values.
             */
            const revealPreview = function () {
                preview.classList.add(
                    'gmrc-creation-preview--visible'
                );

                if (
                    initialised
                    && !reducedMotion.matches
                ) {
                    window.setTimeout(
                        function () {
                            const anchors = [];

                            if (
                                nameInput
                                    instanceof HTMLInputElement
                                && nameInput.value.trim()
                                    !== ''
                            ) {
                                anchors.push('name');
                            }

                            if (
                                checkedInput('race')
                                    instanceof HTMLInputElement
                            ) {
                                anchors.push('race');
                            }

                            if (
                                checkedInput('class')
                                    instanceof HTMLInputElement
                            ) {
                                anchors.push(
                                    'class',
                                    'hitdie',
                                    'hp',
                                    'saving'
                                );
                            }

                            if (anchors.length > 0) {
                                registrarSequence(
                                    anchors,
                                    true
                                );
                            } else {
                                registrarRest();
                            }
                        },
                        850
                    );
                }
            };

            if (
                reducedMotion.matches
                || !(
                    'IntersectionObserver'
                    in window
                )
            ) {
                revealPreview();
            } else {
                const observer =
                    new IntersectionObserver(
                        function (entries) {
                            entries.forEach(
                                function (entry) {
                                    if (
                                        !entry.isIntersecting
                                    ) {
                                        return;
                                    }

                                    revealPreview();

                                    observer.unobserve(
                                        preview
                                    );
                                }
                            );
                        },
                        {
                            threshold: 0.05,
                            rootMargin:
                                '0px 0px -12% 0px',
                        }
                    );

                observer.observe(preview);
            }

            window.addEventListener(
                'pagehide',
                function () {
                    window.clearTimeout(
                        awakenTimer
                    );

                    window.clearTimeout(
                        nameTimer
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
            initialisePreviews
        );
    } else {
        initialisePreviews();
    }
})();
