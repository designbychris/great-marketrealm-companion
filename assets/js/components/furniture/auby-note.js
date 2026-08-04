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
                !form
                || !note
                || !quote
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
            let ambientTimer = null;
            let lastState = '';

            const messageFor = function (state) {
                const attribute =
                    'auby'
                    + state.charAt(0).toUpperCase()
                    + state.slice(1);

                return desk.dataset[attribute]
                    || desk.dataset.aubyStart
                    || 'A fresh page awaits.';
            };

            const replaceMessage = function (
                state
            ) {
                if (state === lastState) {
                    return;
                }

                lastState = state;

                const message = messageFor(
                    state
                );

                window.clearTimeout(
                    messageTimer
                );

                if (reducedMotion.matches) {
                    quote.textContent = message;

                    if (correction) {
                        correction.hidden = true;
                    }

                    return;
                }

                note.classList.remove(
                    'auby-note--changing'
                );

                void note.offsetWidth;

                note.classList.add(
                    'auby-note--changing'
                );

                messageTimer = window.setTimeout(
                    function () {
                        quote.textContent = message;

                        if (correction) {
                            correction.hidden = true;
                        }
                    },
                    175
                );

                window.setTimeout(
                    function () {
                        note.classList.remove(
                            'auby-note--changing'
                        );
                    },
                    470
                );
            };

            const checked = function (name) {
                return form.querySelector(
                    'input[name="'
                        + name
                        + '"]:checked'
                );
            };

            const currentState = function () {
                const hasName =
                    nameInput instanceof HTMLInputElement
                    && nameInput.value.trim() !== '';

                const hasRace =
                    checked('race')
                        instanceof HTMLInputElement;

                const hasClass =
                    checked('class')
                        instanceof HTMLInputElement;

                if (
                    hasName
                    && hasRace
                    && hasClass
                ) {
                    return 'ready';
                }

                if (hasClass) {
                    return 'class';
                }

                if (hasRace) {
                    return 'race';
                }

                if (hasName) {
                    return 'name';
                }

                return 'start';
            };

            const updateMessage = function () {
                replaceMessage(
                    currentState()
                );
            };

            const scheduleAmbientLife = function () {
                if (reducedMotion.matches) {
                    return;
                }

                const delay =
                    12000
                    + Math.floor(
                        Math.random() * 10000
                    );

                ambientTimer = window.setTimeout(
                    function () {
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
                            950
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
                    updateMessage
                );
            }

            raceInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    updateMessage
                );
            });

            classInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    updateMessage
                );
            });

            updateMessage();
            scheduleAmbientLife();

            window.addEventListener(
                'pagehide',
                function () {
                    window.clearTimeout(
                        messageTimer
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
    }
);
