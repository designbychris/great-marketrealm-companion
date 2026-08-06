/**
 * Accessible Guild Illuminator controls.
 */
(function (window) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    const CONTROL_SLOTS = [
        ['background', 'Background'],
        ['body', 'Heritage form'],
        ['eyes', 'Eyes'],
        ['mouth', 'Expression'],
        ['outfit', 'Outfit'],
        ['equipment', 'Equipment'],
        ['class_accessory', 'Accessory'],
        ['effects', 'Effects'],
        ['frame', 'Frame'],
    ];

    class PortraitStudioControls {
        constructor(app) {
            this.app = app;
            this.status = null;
        }

        mount() {
            const existing =
                this.app.studio.parentElement
                    .querySelector(
                        '.gmrc-portrait-controls'
                    );

            if (existing instanceof HTMLElement) {
                existing.remove();
            }

            const controls =
                document.createElement('section');

            controls.className =
                'gmrc-portrait-controls';

            controls.setAttribute(
                'aria-labelledby',
                this.app.id + '-controls-title'
            );

            const heading =
                document.createElement('h3');

            heading.id =
                this.app.id + '-controls-title';

            heading.textContent =
                'Portrait customisation';

            controls.appendChild(heading);

            const rows =
                document.createElement('div');

            rows.className =
                'gmrc-portrait-controls__rows';

            CONTROL_SLOTS.forEach(
                function (definition) {
                    const variants =
                        this.app.variants.forSlot(
                            definition[0]
                        );

                    if (variants.length === 0) {
                        return;
                    }

                    rows.appendChild(
                        this.row(
                            definition[0],
                            definition[1]
                        )
                    );
                },
                this
            );

            controls.appendChild(rows);
            controls.appendChild(
                this.globalActions()
            );

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

            this.app.studio.insertAdjacentElement(
                'afterend',
                controls
            );
        }

        row(slot, label) {
            const row = document.createElement('div');
            row.className =
                'gmrc-portrait-controls__row';

            const name = document.createElement('span');
            name.className =
                'gmrc-portrait-controls__label';
            name.textContent = label;

            row.appendChild(name);
            row.appendChild(
                this.button(
                    'Previous ' + label.toLowerCase(),
                    '←',
                    function () {
                        this.app.move(slot, -1);
                        this.announce(
                            label + ' moved to previous variant.'
                        );
                    }
                )
            );

            row.appendChild(
                this.button(
                    'Randomise ' + label.toLowerCase(),
                    '🎲',
                    function () {
                        this.app.randomise(slot);
                        this.announce(
                            label + ' randomised.'
                        );
                    }
                )
            );

            row.appendChild(
                this.button(
                    'Next ' + label.toLowerCase(),
                    '→',
                    function () {
                        this.app.move(slot, 1);
                        this.announce(
                            label + ' moved to next variant.'
                        );
                    }
                )
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
                    'Randomise the whole portrait',
                    'Randomise portrait',
                    function () {
                        this.app.randomiseAll();
                        this.announce(
                            'The whole portrait was randomised.'
                        );
                    },
                    'gmrc-button gmrc-button--secondary'
                )
            );

            actions.appendChild(
                this.button(
                    'Reset portrait to its deterministic default',
                    'Reset portrait',
                    function () {
                        this.app.reset();
                        this.announce(
                            'The portrait was reset to its original design.'
                        );
                    },
                    'gmrc-button gmrc-button--ghost'
                )
            );

            return actions;
        }

        button(label, text, callback, className = '') {
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

        announce(message) {
            if (this.status instanceof HTMLElement) {
                this.status.textContent = message;
            }
        }
    }

    studioApi.Controls = PortraitStudioControls;
    studioApi.controlSlots = CONTROL_SLOTS.map(
        function (definition) {
            return definition[0];
        }
    );
})(window);
