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

            const race =
                this.studio.dataset.portraitRace || '';

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
                        && assetId.indexOf(
                            race + '-'
                        ) !== 0
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

        count(slot) {
            return this.forSlot(slot).length;
        }

        isAdjustable(slot) {
            return this.count(slot) > 1;
        }

        position(slot, current) {
            const variants = this.forSlot(slot);

            if (variants.length === 0) {
                return {
                    index: 0,
                    total: 0,
                };
            }

            const currentIndex =
                variants.indexOf(current);

            return {
                index:
                    currentIndex >= 0
                        ? currentIndex + 1
                        : 1,
                total: variants.length,
            };
        }

        move(slot, current, direction) {
            const variants = this.forSlot(slot);

            if (variants.length <= 1) {
                return null;
            }

            const currentIndex =
                variants.indexOf(current);

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

            if (variants.length <= 1) {
                return null;
            }

            const alternatives = variants.filter(
                function (variant) {
                    return variant !== current;
                }
            );

            if (alternatives.length === 0) {
                return null;
            }

            return alternatives[
                Math.floor(
                    Math.random()
                    * alternatives.length
                )
            ];
        }
    }

    studioApi.Variants = PortraitVariantCollection;
    studioApi.slotPatterns = SLOT_PATTERNS;
})(window);
