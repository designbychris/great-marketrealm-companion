/**
 * Portrait layer and whole-recipe randomisation.
 */
(function (window) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    class PortraitRandomiser {
        constructor(state, variants, updater) {
            this.state = state;
            this.variants = variants;
            this.updater = updater;
        }

        layer(slot) {
            const next = this.variants.random(
                slot,
                this.state.value(slot)
            );

            if (next === null) {
                return false;
            }

            this.state.set(slot, next);
            this.updater.apply(slot, next);

            return true;
        }

        all(slots) {
            let changed = false;

            slots.forEach(
                function (slot) {
                    changed = this.layer(slot)
                        || changed;
                },
                this
            );

            return changed;
        }
    }

    studioApi.Randomiser = PortraitRandomiser;
})(window);
