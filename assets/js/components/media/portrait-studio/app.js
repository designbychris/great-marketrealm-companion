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
            /*
             * The legacy renderer finishes synchronously during its
             * DOMContentLoaded listener. Queueing lets us snapshot the
             * completed deterministic recipe rather than empty fields.
             */
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
                    this.state.refreshInitial();
                    this.controls.mount();
                }.bind(this)
            );
        }

        move(slot, direction) {
            const next = this.variants.move(
                slot,
                this.state.value(slot),
                direction
            );

            if (next === null) {
                return;
            }

            this.state.set(slot, next);
            this.updater.apply(slot, next);
        }

        randomise(slot) {
            this.randomiser.layer(slot);
        }

        randomiseAll() {
            this.randomiser.all(
                studioApi.controlSlots
            );
        }

        reset() {
            const values = this.state.initial;

            Object.entries(values).forEach(
                function (entry) {
                    this.state.set(
                        entry[0],
                        entry[1]
                    );

                    this.updater.apply(
                        entry[0],
                        entry[1]
                    );
                },
                this
            );
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
