/**
 * Accessible Guild Illuminator controls.
 */
(function (window) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    const CONTROL_SLOTS = [
        ['background', 'Background'],
        ['body', 'Body form'],
        ['eyes', 'Eyes'],
        ['mouth', 'Expression'],
        ['outfit', 'Outfit'],
        ['equipment', 'Equipment'],
        ['class_accessory', 'Accessory'],
        ['class_effects', 'Class aura'],
        ['guild_ornament', 'Guild ornament'],
        ['effects', 'Ambient effects'],
        ['frame', 'Frame'],
    ];

    class PortraitStudioControls {
        constructor(app) {
            this.app = app;
            this.status = null;
            this.positions = new Map();
        }

        mount() {
            const parent =
                this.app.studio.parentElement;

            if (!(parent instanceof HTMLElement)) {
                return;
            }

            const existing =
                parent.querySelector(
                    '.gmrc-portrait-controls'
                );

            if (existing instanceof HTMLElement) {
                existing.remove();
            }

            this.positions.clear();

            const controls =
                document.createElement('section');

            controls.className =
                'gmrc-portrait-controls';

            controls.setAttribute(
                'aria-labelledby',
                this.app.id + '-controls-title'
            );

            const header =
                document.createElement('header');

            header.className =
                'gmrc-portrait-controls__header';

            const eyebrow =
                document.createElement('p');

            eyebrow.className = 'gmrc-eyebrow';
            eyebrow.textContent =
                'Guild Illuminator';

            const heading =
                document.createElement('h3');

            heading.id =
                this.app.id + '-controls-title';

            heading.textContent =
                'Portrait customisation';

            const intro =
                document.createElement('p');

            intro.textContent =
                'Turn the Illuminator’s selectors to adjust '
                + 'the variations currently available for this portrait.';

            header.append(
                eyebrow,
                heading,
                intro
            );

            controls.appendChild(header);

            const rows =
                document.createElement('div');

            rows.className =
                'gmrc-portrait-controls__rows';

            let adjustable = 0;

            CONTROL_SLOTS.forEach(
                function (definition) {
                    const slot = definition[0];

                    if (
                        !this.app.variants
                            .isAdjustable(slot)
                    ) {
                        return;
                    }

                    rows.appendChild(
                        this.row(
                            slot,
                            definition[1]
                        )
                    );

                    adjustable += 1;
                },
                this
            );

            if (adjustable > 0) {
                controls.appendChild(rows);
            } else {
                const note =
                    document.createElement('div');

                note.className =
                    'gmrc-portrait-controls__locked';

                note.innerHTML =
                    '<span aria-hidden="true">✦</span>'
                    + '<p><strong>This illumination is already using '
                    + 'its only available painted set.</strong>'
                    + '<small>Additional variants will appear here as '
                    + 'the Guild portrait library expands.</small></p>';

                controls.appendChild(note);
            }

            const actions =
                this.globalActions();

            const ledger =
                this.app.studio.closest(
                    '.gmrc-open-ledger'
                );

            if (ledger instanceof HTMLElement) {
                actions.classList.add(
                    'gmrc-portrait-controls__actions--ledger'
                );
            } else if (adjustable > 0) {
                controls.appendChild(actions);
            }

            this.status =
                document.createElement('p');

            this.status.className =
                'gmrc-portrait-controls__status';

            this.status.setAttribute(
                'role',
                'status'
            );

            this.status.setAttribute(
                'aria-live',
                'polite'
            );

            controls.appendChild(this.status);

            const privateStudio =
                this.app.studio.closest(
                    '.gmrc-private-studio'
                );

            const privateControls =
                privateStudio instanceof HTMLElement
                    ? privateStudio.querySelector(
                        '[data-private-studio-controls]'
                    )
                    : null;

            if (privateControls instanceof HTMLElement) {
                privateControls.replaceChildren(
                    controls
                );
            } else {
                this.app.studio.insertAdjacentElement(
                    'afterend',
                    controls
                );
            }

            if (
                ledger instanceof HTMLElement
                && adjustable > 0
                && actions.parentElement === null
            ) {
                const portraitArea =
                    this.app.studio.closest(
                        '.gmrc-ledger-page__portrait'
                    );

                if (portraitArea instanceof HTMLElement) {
                    portraitArea.appendChild(
                        actions
                    );
                } else {
                    controls.appendChild(
                        actions
                    );
                }
            }

            this.refreshAll();
        }

        row(slot, label) {
            const row =
                document.createElement('div');

            row.className =
                'gmrc-portrait-controls__row';

            row.dataset.portraitControlSlot = slot;

            const name =
                document.createElement('span');

            name.className =
                'gmrc-portrait-controls__label';

            name.textContent = label;

            const previous = this.button(
                'Previous ' + label.toLowerCase(),
                '←',
                function () {
                    if (!this.app.move(slot, -1)) {
                        return;
                    }

                    this.refresh(slot);
                    this.announceCurrent(
                        slot,
                        label
                    );
                }
            );

            const random = this.button(
                'Randomise ' + label.toLowerCase(),
                '🎲',
                function () {
                    if (!this.app.randomise(slot)) {
                        return;
                    }

                    this.refresh(slot);
                    this.announceCurrent(
                        slot,
                        label
                    );
                }
            );

            const next = this.button(
                'Next ' + label.toLowerCase(),
                '→',
                function () {
                    if (!this.app.move(slot, 1)) {
                        return;
                    }

                    this.refresh(slot);
                    this.announceCurrent(
                        slot,
                        label
                    );
                }
            );

            const position =
                document.createElement('span');

            position.className =
                'gmrc-portrait-controls__position';

            position.dataset.portraitControlPosition =
                slot;

            this.positions.set(
                slot,
                position
            );

            row.append(
                name,
                previous,
                random,
                next,
                position
            );

            return row;
        }

        globalActions() {
            const actions =
                document.createElement('div');

            actions.className =
                'gmrc-portrait-controls__actions';

            actions.appendChild(
                this.button(
                    'Randomise every adjustable portrait layer',
                    'Randomise portrait',
                    function () {
                        if (!this.app.randomiseAll()) {
                            return;
                        }

                        this.refreshAll();
                        this.announce(
                            'All available portrait variations were randomised.'
                        );
                    },
                    'gmrc-button gmrc-button--secondary'
                )
            );

            actions.appendChild(
                this.button(
                    'Reset portrait to its current deterministic default',
                    'Reset portrait',
                    function () {
                        this.app.reset();
                        this.refreshAll();
                        this.announce(
                            'The portrait was restored to its deterministic Guild design.'
                        );
                    },
                    'gmrc-button gmrc-button--ghost'
                )
            );

            return actions;
        }

        button(
            label,
            text,
            callback,
            className = ''
        ) {
            const button =
                document.createElement('button');

            button.type = 'button';

            button.className =
                className
                || 'gmrc-portrait-controls__button';

            button.setAttribute(
                'aria-label',
                label
            );

            button.textContent = text;

            button.addEventListener(
                'click',
                callback.bind(this)
            );

            return button;
        }

        refresh(slot) {
            const output =
                this.positions.get(slot);

            if (!(output instanceof HTMLElement)) {
                return;
            }

            const position =
                this.app.variants.position(
                    slot,
                    this.app.state.value(slot)
                );

            output.textContent =
                position.total > 0
                    ? position.index
                        + ' of '
                        + position.total
                    : '—';

            output.setAttribute(
                'aria-label',
                'Variant '
                + position.index
                + ' of '
                + position.total
            );
        }

        refreshAll() {
            this.positions.forEach(
                function (_, slot) {
                    this.refresh(slot);
                },
                this
            );
        }

        announceCurrent(slot, label) {
            const position =
                this.app.variants.position(
                    slot,
                    this.app.state.value(slot)
                );

            this.announce(
                label
                + ' is now variant '
                + position.index
                + ' of '
                + position.total
                + '.'
            );
        }

        announce(message) {
            if (this.status instanceof HTMLElement) {
                this.status.textContent = message;
            }
        }
    }

    studioApi.Controls = PortraitStudioControls;

    studioApi.controlSlots =
        CONTROL_SLOTS.map(
            function (definition) {
                return definition[0];
            }
        );
})(window);
