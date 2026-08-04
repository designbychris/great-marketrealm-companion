/**
 * Great Marketrealm Companion
 * Reactive Auby Note
 */

(function () {
    'use strict';

    /**
     * Initialise every Living Desk on the current page.
     */
    const initialiseAubyNotes = function () {
        const desks = document.querySelectorAll(
            '[data-living-desk]'
        );

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        desks.forEach(function (desk) {
            const form = desk.closest('form');

            const note = desk.querySelector(
                '[data-auby-note]'
            );

            const quote = desk.querySelector(
                '[data-auby-quote]'
            );

            const correction = desk.querySelector(
                '[data-auby-correction]'
            );

            if (!form || !note || !quote) {
                console.warn(
                    'GMRC Auby note could not initialise.',
                    {
                        desk: desk,
                        form: form,
                        note: note,
                        quote: quote,
                    }
                );

                return;
            }

            /*
             * Prevent duplicate event listeners if another script
             * attempts to initialise the component again.
             */
            if (desk.dataset.aubyInitialised === 'true') {
                return;
            }

            desk.dataset.aubyInitialised = 'true';

            let animationTimer = null;
            let textTimer = null;
            let readyTimer = null;
            let nameTimer = null;
            let ambientTimer = null;

            /**
             * Retrieve one of the server-rendered messages.
             */
            const messageFor = function (state) {
                const messages = {
                    start: desk.getAttribute(
                        'data-auby-start'
                    ),
                    name: desk.getAttribute(
                        'data-auby-name'
                    ),
                    race: desk.getAttribute(
                        'data-auby-race'
                    ),
                    class: desk.getAttribute(
                        'data-auby-class'
                    ),
                    ready: desk.getAttribute(
                        'data-auby-ready'
                    ),
                };

                return messages[state]
                    || messages.start
                    || 'A fresh page awaits.';
            };

            /**
             * Find the character-name field.
             */
            const nameField = function () {
                return form.querySelector(
                    '[name="name"]'
                );
            };

            /**
             * Determine whether a radio group has a selection.
             */
            const hasSelection = function (name) {
                return form.querySelector(
                    '[name="'
                        + name
                        + '"]:checked'
                ) !== null;
            };

            /**
             * Determine whether the character has a name.
             */
            const hasName = function () {
                const input = nameField();

                if (!input) {
                    return false;
                }

                return String(input.value || '')
                    .trim() !== '';
            };

            /**
             * Determine whether the registration can be completed.
             */
            const isReady = function () {
                return (
                    hasName()
                    && hasSelection('race')
                    && hasSelection('class')
                );
            };

            /**
             * Replace Auby's written message.
             */
            const showMessage = function (state) {
                const message = messageFor(state);

                window.clearTimeout(textTimer);
                window.clearTimeout(animationTimer);

                note.classList.remove(
                    'auby-note--ambient',
                    'auby-note--changing'
                );

                if (reducedMotion.matches) {
                    quote.textContent = message;

                    if (correction) {
                        correction.hidden = true;
                    }

                    return;
                }

                /*
                 * Restart the paper and quill animations.
                 */
                void note.offsetWidth;

                note.classList.add(
                    'auby-note--changing'
                );

                /*
                 * Change the words while the old note is hidden.
                 */
                textTimer = window.setTimeout(
                    function () {
                        quote.textContent = message;

                        if (correction) {
                            correction.hidden = true;
                        }
                    },
                    170
                );

                animationTimer = window.setTimeout(
                    function () {
                        note.classList.remove(
                            'auby-note--changing'
                        );
                    },
                    780
                );
            };

            /**
             * Follow an interaction with the completed-record note.
             */
            const scheduleReadyMessage = function () {
                window.clearTimeout(readyTimer);

                if (!isReady()) {
                    return;
                }

                readyTimer = window.setTimeout(
                    function () {
                        showMessage('ready');
                    },
                    1800
                );
            };

            /**
             * React when the name is edited.
             */
            const reactToName = function () {
                window.clearTimeout(nameTimer);
                window.clearTimeout(readyTimer);

                nameTimer = window.setTimeout(
                    function () {
                        showMessage(
                            hasName()
                                ? 'name'
                                : 'start'
                        );

                        scheduleReadyMessage();
                    },
                    350
                );
            };

            /**
             * React when a race or class is selected.
             */
            const reactToSelection = function (
                fieldName
            ) {
                window.clearTimeout(readyTimer);

                showMessage(fieldName);

                scheduleReadyMessage();
            };

            /**
             * Use form-level event delegation.
             *
             * This continues working even if a field component is
             * replaced or re-rendered later.
             */
            form.addEventListener(
                'input',
                function (event) {
                    const target = event.target;

                    if (
                        target
                        && target.getAttribute('name')
                            === 'name'
                    ) {
                        reactToName();
                    }
                }
            );

            form.addEventListener(
                'change',
                function (event) {
                    const target = event.target;

                    if (!target) {
                        return;
                    }

                    const fieldName = target.getAttribute(
                        'name'
                    );

                    if (fieldName === 'race') {
                        reactToSelection('race');
                    }

                    if (fieldName === 'class') {
                        reactToSelection('class');
                    }
                }
            );

            /**
             * Occasionally let the note and quill move.
             */
            const scheduleAmbientLife = function () {
                if (reducedMotion.matches) {
                    return;
                }

                window.clearTimeout(ambientTimer);

                ambientTimer = window.setTimeout(
                    function () {
                        note.classList.remove(
                            'auby-note--ambient'
                        );

                        void note.offsetWidth;

                        note.classList.add(
                            'auby-note--ambient'
                        );

                        window.setTimeout(
                            function () {
                                note.classList.remove(
                                    'auby-note--ambient'
                                );

                                scheduleAmbientLife();
                            },
                            1000
                        );
                    },
                    12000
                        + Math.floor(
                            Math.random() * 10000
                        )
                );
            };

            scheduleAmbientLife();

            console.info(
                'GMRC reactive Auby note initialised.'
            );

            window.addEventListener(
                'pagehide',
                function () {
                    window.clearTimeout(
                        animationTimer
                    );

                    window.clearTimeout(
                        textTimer
                    );

                    window.clearTimeout(
                        readyTimer
                    );

                    window.clearTimeout(
                        nameTimer
                    );

                    window.clearTimeout(
                        ambientTimer
                    );
                },
                {
                    once: true,
                }
            );
        });
    };

    /*
     * Work whether the script loads before or after
     * DOMContentLoaded.
     */
    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initialiseAubyNotes
        );
    } else {
        initialiseAubyNotes();
    }
})();
