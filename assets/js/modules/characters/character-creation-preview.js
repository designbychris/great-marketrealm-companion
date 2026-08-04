/**
 * Great Marketrealm Companion
 * Living Character Creation Preview
 */

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const preview = document.querySelector(
            '[data-character-creation-preview]'
        );

        if (!preview) {
            return;
        }

        const form = preview.closest('form');

        if (!form) {
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

        const savingThrowsOutput = preview.querySelector(
            '[data-preview-saving-throws]'
        );

        const noteOutput = preview.querySelector(
            '[data-preview-note]'
        );

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        let awakenTimer = null;

        /**
         * Replay the handwritten ink effect.
         */
        const writeValue = function (
            element,
            value
        ) {
            if (!element) {
                return;
            }

            element.textContent = value;

            if (reducedMotion.matches) {
                return;
            }

            element.classList.remove(
                'gmrc-preview-ink--writing'
            );

            void element.offsetWidth;

            element.classList.add(
                'gmrc-preview-ink--writing'
            );
        };

        /**
         * Briefly awaken the full parchment.
         */
        const awakenPreview = function () {

            if ( !preview.classList.contains(
                    'gmrc-creation-preview--visible'
                )
            ) {
                return;
            }
            
            if (reducedMotion.matches) {
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
         * Return the currently selected radio.
         */
        const checkedInput = function (name) {
            return form.querySelector(
                'input[name="'
                    + name
                    + '"]:checked'
            );
        };

        /**
         * Update the adventurer's written name.
         */
        const updateName = function () {
            const value =
                nameInput instanceof HTMLInputElement
                    ? nameInput.value.trim()
                    : '';

            writeValue(
                nameOutput,
                value !== ''
                    ? value
                    : 'Unnamed Adventurer'
            );
        };

        /**
         * Update selected heritage.
         */
        const updateRace = function () {
            const selected = checkedInput(
                'race'
            );

            const label =
                selected instanceof HTMLInputElement
                    ? selected.dataset.raceLabel
                    : '';

            writeValue(
                raceOutput,
                label || 'Heritage awaiting selection'
            );

            updateNote();
            awakenPreview();
        };

        /**
         * Update selected class statistics.
         */
        const updateClass = function () {
            const selected = checkedInput(
                'class'
            );

            if (!(selected instanceof HTMLInputElement)) {
                writeValue(
                    classOutput,
                    'Class awaiting selection'
                );

                writeValue(
                    hitDieOutput,
                    '—'
                );

                writeValue(
                    hitPointsOutput,
                    '—'
                );

                writeValue(
                    savingThrowsOutput,
                    'Choose a class to reveal its defensive training.'
                );

                updateNote();

                return;
            }

            writeValue(
                classOutput,
                selected.dataset.classLabel
                    || 'Unknown Class'
            );

            writeValue(
                hitDieOutput,
                selected.dataset.hitDie
                    ? 'd' + selected.dataset.hitDie
                    : '—'
            );

            writeValue(
                hitPointsOutput,
                selected.dataset.startingHitPoints
                    || '—'
            );

            writeValue(
                savingThrowsOutput,
                selected.dataset.savingThrows
                    || 'None recorded'
            );

            updateNote();
            awakenPreview();
        };

        /**
         * Update the contextual archive note.
         */
        const updateNote = function () {
            const race = checkedInput(
                'race'
            );

            const characterClass = checkedInput(
                'class'
            );

            if (
                race instanceof HTMLInputElement
                && characterClass
                    instanceof HTMLInputElement
            ) {
                writeValue(
                    noteOutput,
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
                        + '.'
                );

                return;
            }

            if (race instanceof HTMLInputElement) {
                writeValue(
                    noteOutput,
                    'Their '
                        + (
                            race.dataset.raceLabel
                            || 'Marketrealm'
                        )
                        + ' heritage has been recorded. '
                        + 'Choose a class to complete the inscription.'
                );

                return;
            }

            if (
                characterClass
                instanceof HTMLInputElement
            ) {
                writeValue(
                    noteOutput,
                    'The path of the '
                        + (
                            characterClass.dataset.classLabel
                            || 'adventurer'
                        )
                        + ' has been chosen. '
                        + 'Their heritage remains unwritten.'
                );

                return;
            }

            writeValue(
                noteOutput,
                'Choose a race and class to begin this '
                    + 'adventurer’s first inscription.'
            );
        };

        if (nameInput instanceof HTMLInputElement) {
            nameInput.addEventListener(
                'input',
                updateName
            );
        }

        raceInputs.forEach(function (input) {
            input.addEventListener(
                'change',
                updateRace
            );
        });

        classInputs.forEach(function (input) {
            input.addEventListener(
                'change',
                updateClass
            );
        });

        updateName();
        updateRace();
        updateClass();

        /**
         * Unfurl the parchment when its upper portion approaches
         * the visible browser window.
         */
        const revealPreview = function () {
            preview.classList.add(
                'gmrc-creation-preview--visible'
            );
        };
        
        if (
            reducedMotion.matches
            || !('IntersectionObserver' in window)
        ) {
            /*
             * Keep the preview available when reduced motion is
             * requested or IntersectionObserver is unsupported.
             */
            revealPreview();
        } else {
            const observer = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) {
                            return;
                        }
        
                        revealPreview();
        
                        observer.unobserve(
                            preview
                        );
                    });
                },
                {
                    /*
                     * A small threshold works for a parchment that may
                     * be taller than the browser viewport.
                     */
                    threshold: 0.05,
        
                    /*
                     * Begin shortly before the preview reaches the
                     * lower part of the viewport.
                     */
                    rootMargin:
                        '0px 0px -12% 0px',
                }
            );
        
            observer.observe(
                preview
            );
        }
    }
);
