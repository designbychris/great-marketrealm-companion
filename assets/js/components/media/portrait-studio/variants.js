/**
 * Discover portrait variants already embedded as SVG symbols.
 */
(function (window) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    const SLOT_PATTERNS = {
        background: /^background-/,
        body: /-body-/,
        eyes: /^eyes-/,
        mouth: /^mouth-/,
        outfit: /-outfit-/,
        equipment: /-equipment-/,
        class_accessory: /-accessory-/,
        effects: /^effects-/,
        frame: /^frame-/,
    };

    class PortraitVariantCollection {
        constructor(studio) {
            this.studio = studio;
        }

        allAssetIds() {
            return Array.from(
                this.studio.querySelectorAll(
                    'symbol[id^="gmrc-portrait-asset-"]'
                )
            ).map(function (symbol) {
                return symbol.id.replace(
                    'gmrc-portrait-asset-',
                    ''
                );
            });
        }

        forSlot(slot) {
            const pattern = SLOT_PATTERNS[slot];

            if (!(pattern instanceof RegExp)) {
                return [];
            }

            const race = this.studio.dataset.portraitRace || '';
            const characterClass =
                this.studio.dataset.portraitClass || '';

            return this.allAssetIds()
                .filter(function (assetId) {
                    if (!pattern.test(assetId)) {
                        return false;
                    }

                    if (
                        slot === 'body'
                        && race !== ''
                        && assetId.indexOf(race + '-') !== 0
                    ) {
                        return false;
                    }

                    if (
                        [
                            'outfit',
                            'equipment',
                            'class_accessory',
                        ].includes(slot)
                        && characterClass !== ''
                        && assetId.indexOf(
                            characterClass + '-'
                        ) !== 0
                    ) {
                        return false;
                    }

                    return true;
                })
                .sort();
        }

        move(slot, current, direction) {
            const variants = this.forSlot(slot);

            if (variants.length === 0) {
                return null;
            }

            const currentIndex = variants.indexOf(current);

            if (currentIndex < 0) {
                return variants[0];
            }

            let next =
                (currentIndex + direction)
                % variants.length;

            if (next < 0) {
                next += variants.length;
            }

            return variants[next];
        }

        random(slot, current) {
            const variants = this.forSlot(slot);

            if (variants.length === 0) {
                return null;
            }

            if (variants.length === 1) {
                return variants[0];
            }

            const alternatives = variants.filter(
                function (variant) {
                    return variant !== current;
                }
            );

            return alternatives[
                Math.floor(
                    Math.random() * alternatives.length
                )
            ];
        }
    }

    studioApi.Variants = PortraitVariantCollection;
    studioApi.slotPatterns = SLOT_PATTERNS;
})(window);
