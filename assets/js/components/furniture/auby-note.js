/**
 * Great Marketrealm Companion
 * Reactive Auby Note
 */

document.addEventListener(
    'DOMContentLoaded',
    function () {
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

            if (
                !(form instanceof HTMLFormElement)
                || !(note instanceof HTMLElement)
                || !(quote instanceof HTMLElement)
            ) {
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

            let messageTimer = null;
            let animationTimer = null;
            let ambientTimer = null;
            let readyTimer = null;
            let nameTimer = null;

            /**
             * Retrieve a message stored on the Living Desk.
             */
            const messageFor = function (state) {
                const messages = {
                    start:
                        desk.dataset.aubyStart,
                    name:
                        desk.dataset.aubyName,
                    race:
                        desk.dataset.aubyRace,
                    class:
                        desk.dataset.aubyClass,
                    ready:
                        desk.dataset.aubyReady,
                };

                return messages[state]
                    || messages.start
                    || 'A fresh page awaits.';
            };

            /**
             * Determine whether a radio field has a selection.
             */
            const hasSelection = function (name) {
                return form.querySelector(
                    'input[name="'
                        + name
                        + '"]:checked'
                ) instanceof HTMLInputElement;
            };

            /**
             * Determine whether the adventurer has a name.
             */
            const hasName = function () {
                return (
                    nameInput instanceof HTMLInputElement
                    && nameInput.value.trim() !== ''
                );
            };

            /**
             * Determine whether the main inscription is complete.
             */
            const isReady = function () {
                return (
                    hasName()
                    && hasSelection('race')
                    && hasSelection('class')
                );
            };

            /**
             * Change Auby's note with a paper-swap animation.
             */
            const replaceMessage = function (state) {
                const message = messageFor(state);

                window.clearTimeout(
                    messageTimer
                );

                window.clearTimeout(
                    animationTimer
                );

                note.classList.remove(
                    'auby-note--ambient',
                    'auby-note--changing'
                );

                if (reducedMotion.matches) {
                    quote.textContent = message;

                    if (correction instanceof HTMLElement) {
                        correction.hidden = true;
                    }

                    return;
                }

                /*
                 * Force the animation to restart even if the same
                 * interaction occurs more than once.
                 */
                void note.offsetWidth;

                note.classList.add(
                    'auby-note--changing'
                );

                /*
                 * Swap the text while the old paper is faded out.
                 */
                messageTimer = window.setTimeout(
                    function () {
                        quote.textContent = message;

                        if (
                            correction
                                instanceof HTMLElement
                        ) {
                            correction.hidden = true;
                        }
                    },
                    180
                );

                animationTimer = window.setTimeout(
                    function () {
                        note.classList.remove(
                            'auby-note--changing'
                        );
                    },
                    760
                );
            };

            /**
             * Show the ready message shortly after another reaction.
             */
            const scheduleReadyMessage = function () {
                window.clearTimeout(
                    readyTimer
                );

                if (!isReady()) {
                    return;
                }

                readyTimer = window.setTimeout(
                    function () {
                        replaceMessage('ready');
                    },
                    1250
                );
            };

            /**
             * React to the adventurer's name.
             */
            const reactToName = function () {
                window.clearTimeout(
                    nameTimer
                );

                /*
                 * Avoid replacing the note for every individual
                 * keystroke while the player is typing.
                 */
                nameTimer = window.setTimeout(
                    function () {
                        if (!hasName()) {
                            replaceMessage('start');

                            return;
                        }

                        replaceMessage('name');
                        scheduleReadyMessage();
                    },
                    500
                );
            };

            /**
             * React immediately to a race selection.
             */
            const reactToRace = function () {
                replaceMessage('race');
                scheduleReadyMessage();
            };

            /**
             * React immediately to a class selection.
             */
            const reactToClass = function () {
                replaceMessage('class');
                scheduleReadyMessage();
            };

            /**
             * Schedule occasional movement around Auby's note.
             */
            const scheduleAmbientLife = function () {
                if (reducedMotion.matches) {
                    return;
                }

                window.clearTimeout(
                    ambientTimer
                );

                const delay =
                    12000
                    + Math.floor(
                        Math.random() * 10000
                    );

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
                            1100
                        );
                    },
                    delay
                );
            };

            if (
                nameInput
                    instanceof HTMLInputElement
            ) {
                nameInput.addEventListener(
                    'input',
                    reactToName
                );
            }

            raceInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    reactToRace
                );
            });

            classInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    reactToClass
                );
            });

            /*
             * Keep the original server-rendered welcome note on load.
             * Only move to ready automatically when the form was
             * restored with all fields already populated.
             */
            if (isReady()) {
                scheduleReadyMessage();
            }

            scheduleAmbientLife();

            window.addEventListener(
                'pagehide',
                function () {
                    window.clearTimeout(
                        messageTimer
                    );

                    window.clearTimeout(
                        animationTimer
                    );

                    window.clearTimeout(
                        ambientTimer
                    );

                    window.clearTimeout(
                        readyTimer
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
    }
);
