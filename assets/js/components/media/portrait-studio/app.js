/**
 * Portrait Studio application bootstrap.
 */
(function (window, document) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    class PortraitStudioApplication {
        constructor(studio, index) {
            this.studio = studio;

            this.id = studio.id
                || 'gmrc-portrait-studio-' + index;

            studio.id = this.id;

            this.state =
                new studioApi.State(studio);

            this.variants =
                new studioApi.Variants(studio);

            this.updater =
                new studioApi.LayerUpdater(studio);

            this.randomiser =
                new studioApi.Randomiser(
                    this.state,
                    this.variants,
                    this.updater
                );

            this.controls =
                new studioApi.Controls(this);
        }

        boot() {
            window.setTimeout(
                function () {
                    this.state.refreshInitial();
                    this.controls.mount();
                }.bind(this),
                0
            );

            this.studio.addEventListener(
                'gmrc:portrait:recipe-applied',
                function () {
                    /*
                     * A race/class/name change creates a new deterministic
                     * recipe. That recipe becomes Reset's baseline and
                     * controls are rebuilt for the newly available assets.
                     */
                    this.state.refreshInitial();
                    this.controls.mount();
                }.bind(this)
            );
        }

        move(slot, direction) {
            const next =
                this.variants.move(
                    slot,
                    this.state.value(slot),
                    direction
                );

            if (next === null) {
                return false;
            }

            if (!this.updater.apply(slot, next)) {
                return false;
            }

            this.state.set(slot, next);

            return true;
        }

        randomise(slot) {
            return this.randomiser.layer(slot);
        }

        adjustableSlots() {
            return studioApi.controlSlots.filter(
                function (slot) {
                    return this.variants
                        .isAdjustable(slot);
                },
                this
            );
        }

        randomiseAll() {
            return this.randomiser.all(
                this.adjustableSlots()
            );
        }

        reset() {
            const values = this.state.initial;
            let changed = false;

            Object.entries(values).forEach(
                function (entry) {
                    const slot = entry[0];
                    const value = entry[1];

                    if (
                        !studioApi.controlSlots.includes(slot)
                    ) {
                        this.state.set(
                            slot,
                            value
                        );

                        return;
                    }

                    if (
                        this.updater.apply(
                            slot,
                            value
                        )
                    ) {
                        this.state.set(
                            slot,
                            value
                        );

                        changed = true;
                    }
                },
                this
            );

            return changed;
        }
    }

    const boot = function () {
        document
            .querySelectorAll(
                '.gmrc-illuminated-portrait'
            )
            .forEach(function (studio, index) {
                if (
                    !(studio instanceof HTMLElement)
                    || studio.dataset
                        .portraitControlsReady === 'true'
                    || studio.dataset
                        .portraitControls === 'false'
                ) {
                    return;
                }

                studio.dataset
                    .portraitControlsReady = 'true';

                new PortraitStudioApplication(
                    studio,
                    index + 1
                ).boot();
            });
    };

    studioApi.Application =
        PortraitStudioApplication;

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            boot
        );
    } else {
        boot();
    }
})(window, document);
