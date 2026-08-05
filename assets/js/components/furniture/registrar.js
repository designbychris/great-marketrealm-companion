/**
 * Great Marketrealm Companion
 * Guild Registrar
 *
 * Provides a reusable animated quill for writing values
 * into ledger fields marked with data-register-anchor.
 */

(function () {
    'use strict';

    const initialiseRegistrars = function () {
        const previews = document.querySelectorAll(
            '[data-character-creation-preview]'
        );

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        previews.forEach(function (preview) {
            const paper = preview.querySelector(
                '.gmrc-creation-preview__paper'
            );

            const registrar = preview.querySelector(
                '.gmrc-registrar'
            );

            const quill = preview.querySelector(
                '.gmrc-registrar__quill'
            );

            if (
                !(paper instanceof HTMLElement)
                || !(registrar instanceof HTMLElement)
                || !(quill instanceof HTMLElement)
            ) {
                return;
            }

            if (
                preview.dataset.registrarInitialised
                    === 'true'
            ) {
                return;
            }

            preview.dataset.registrarInitialised =
                'true';

            preview.dataset.registrarReady =
                'true';

            let queue = Promise.resolve();

            /**
             * Pause an animation sequence.
             */
            const wait = function (duration) {
                return new Promise(function (resolve) {
                    window.setTimeout(
                        resolve,
                        duration
                    );
                });
            };

            /**
             * Find a writable ledger field.
             */
            const anchorFor = function (name) {
                return preview.querySelector(
                    '[data-register-anchor="'
                        + name
                        + '"]'
                );
            };

            /**
             * Calculate the quill position for an anchor.
             */
            const positionFor = function (anchor) {
                const paperRect =
                    paper.getBoundingClientRect();

                const anchorRect =
                    anchor.getBoundingClientRect();

                const quillWidth =
                    quill.offsetWidth || 42;

                const quillHeight =
                    quill.offsetHeight || 42;

                /*
                 * Position the nib near the final third of
                 * the writable field.
                 */
                const desiredX =
                    anchorRect.left
                    - paperRect.left
                    + Math.min(
                        anchorRect.width * 0.72,
                        Math.max(
                            18,
                            anchorRect.width - 12
                        )
                    )
                    - quillWidth * 0.72;

                const desiredY =
                    anchorRect.top
                    - paperRect.top
                    + anchorRect.height * 0.58
                    - quillHeight * 0.78;

                const maximumX =
                    paper.clientWidth
                    - quillWidth
                    - 12;

                const maximumY =
                    paper.clientHeight
                    - quillHeight
                    - 12;

                return {
                    x: Math.max(
                        12,
                        Math.min(
                            maximumX,
                            desiredX
                        )
                    ),
                    y: Math.max(
                        12,
                        Math.min(
                            maximumY,
                            desiredY
                        )
                    ),
                };
            };

            /**
             * Move the quill to a ledger field.
             */
            const moveTo = async function (anchor) {
                const position =
                    positionFor(anchor);

                registrar.style.setProperty(
                    '--gmrc-registrar-x',
                    position.x + 'px'
                );

                registrar.style.setProperty(
                    '--gmrc-registrar-y',
                    position.y + 'px'
                );

                registrar.classList.remove(
                    'gmrc-registrar--writing'
                );

                registrar.classList.add(
                    'gmrc-registrar--moving'
                );

                await wait(420);

                registrar.classList.remove(
                    'gmrc-registrar--moving'
                );
            };

            /**
             * Reveal one field as though it is being written.
             */
            const writeAnchor = async function (
                anchorName
            ) {
                const anchor = anchorFor(
                    anchorName
                );

                if (!(anchor instanceof HTMLElement)) {
                    return;
                }

                if (
                    reducedMotion.matches
                    || !preview.classList.contains(
                        'gmrc-creation-preview--visible'
                    )
                ) {
                    anchor.classList.remove(
                        'gmrc-register-anchor--pending'
                    );

                    anchor.classList.add(
                        'gmrc-register-anchor--written'
                    );

                    return;
                }

                registrar.classList.add(
                    'gmrc-registrar--active'
                );

                await moveTo(anchor);

                registrar.classList.add(
                    'gmrc-registrar--writing'
                );

                anchor.classList.remove(
                    'gmrc-register-anchor--pending'
                );

                anchor.classList.remove(
                    'gmrc-register-anchor--written'
                );

                /*
                 * Force the ink animation to restart.
                 */
                void anchor.offsetWidth;

                anchor.classList.add(
                    'gmrc-register-anchor--writing'
                );

                await wait(560);

                anchor.classList.remove(
                    'gmrc-register-anchor--writing'
                );

                anchor.classList.add(
                    'gmrc-register-anchor--written'
                );

                registrar.classList.remove(
                    'gmrc-registrar--writing'
                );

                await wait(90);
            };

            /**
             * Briefly rustle the parchment.
             */
            const rustlePaper = function () {
                if (reducedMotion.matches) {
                    return;
                }

                paper.classList.remove(
                    'gmrc-creation-preview__paper--rustling'
                );

                void paper.offsetWidth;

                paper.classList.add(
                    'gmrc-creation-preview__paper--rustling'
                );

                window.setTimeout(
                    function () {
                        paper.classList.remove(
                            'gmrc-creation-preview__paper--rustling'
                        );
                    },
                    520
                );
            };

            /**
             * Add work to the Registrar's writing queue.
             */
            const enqueue = function (
                anchors,
                rustle = false
            ) {
                const names = Array.isArray(anchors)
                    ? anchors
                    : [anchors];

                queue = queue.then(
                    async function () {
                        for (const name of names) {
                            await writeAnchor(name);
                        }

                        if (rustle) {
                            rustlePaper();
                        }
                    }
                );

                return queue;
            };

            /**
             * Write one updated field.
             */
            preview.addEventListener(
                'gmrc:registrar-write',
                function (event) {
                    const detail = event.detail || {};

                    if (
                        typeof detail.anchor
                            !== 'string'
                    ) {
                        return;
                    }

                    enqueue(
                        detail.anchor,
                        detail.rustle === true
                    );
                }
            );

            /**
             * Write a sequence of related fields.
             */
            preview.addEventListener(
                'gmrc:registrar-sequence',
                function (event) {
                    const detail = event.detail || {};

                    if (!Array.isArray(detail.anchors)) {
                        return;
                    }

                    enqueue(
                        detail.anchors,
                        detail.rustle === true
                    );
                }
            );

            /**
             * Return the quill to its inkwell.
             */
            preview.addEventListener(
                'gmrc:registrar-rest',
                function () {
                    queue = queue.then(
                        async function () {
                            registrar.classList.remove(
                                'gmrc-registrar--writing'
                            );

                            registrar.classList.add(
                                'gmrc-registrar--resting'
                            );

                            registrar.style.setProperty(
                                '--gmrc-registrar-x',
                                '0px'
                            );

                            registrar.style.setProperty(
                                '--gmrc-registrar-y',
                                '0px'
                            );

                            await wait(440);

                            registrar.classList.remove(
                                'gmrc-registrar--active',
                                'gmrc-registrar--moving'
                            );
                        }
                    );
                }
            );
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initialiseRegistrars
        );
    } else {
        initialiseRegistrars();
    }
})();
