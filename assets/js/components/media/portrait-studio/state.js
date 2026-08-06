/**
 * Portrait Studio state and form-field bridge.
 */
(function (window) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    class PortraitStudioState {
        constructor(studio) {
            this.studio = studio;
            this.form = studio.closest('form');
            this.initial = this.snapshot();
        }

        field(slot) {
            if (!(this.form instanceof HTMLFormElement)) {
                return null;
            }

            return this.form.querySelector(
                '[data-portrait-field="' + slot + '"]'
            );
        }

        value(slot) {
            const field = this.field(slot);

            return field instanceof HTMLInputElement
                ? field.value
                : '';
        }

        set(slot, value) {
            const field = this.field(slot);

            if (field instanceof HTMLInputElement) {
                field.value = value || '';
            }

            this.studio.dataset[
                'portrait' + slot
                    .split('_')
                    .map(function (part) {
                        return part.charAt(0).toUpperCase()
                            + part.slice(1);
                    })
                    .join('')
            ] = value || '';

            this.studio.dispatchEvent(
                new CustomEvent(
                    'gmrc:portrait:changed',
                    {
                        bubbles: true,
                        detail: {
                            slot: slot,
                            value: value || '',
                        },
                    }
                )
            );
        }

        snapshot() {
            const values = {};

            if (!(this.form instanceof HTMLFormElement)) {
                return values;
            }

            this.form
                .querySelectorAll('[data-portrait-field]')
                .forEach(function (field) {
                    if (!(field instanceof HTMLInputElement)) {
                        return;
                    }

                    values[field.dataset.portraitField] =
                        field.value;
                });

            return values;
        }

        refreshInitial() {
            this.initial = this.snapshot();
        }

        reset() {
            Object.entries(this.initial).forEach(
                function (entry) {
                    this.set(entry[0], entry[1]);
                },
                this
            );
        }
    }

    studioApi.State = PortraitStudioState;
})(window);
