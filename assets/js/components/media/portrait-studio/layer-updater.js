/**
 * Update existing SVG <use> layers without rebuilding the portrait.
 */
(function (window) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    const LAYERS = {
        background: '.gmrc-portrait-layer--background',
        body: '.gmrc-portrait-layer--race',
        eyes: '.gmrc-portrait-layer--face',
        mouth: '.gmrc-portrait-layer--face',
        outfit: '.gmrc-portrait-layer--class',
        equipment: '.gmrc-portrait-layer--class',
        class_accessory: '.gmrc-portrait-layer--accessory',
        effects: '.gmrc-portrait-layer--effects',
        frame: '.gmrc-portrait-layer--frame',
    };

    const layerParts = {
        eyes: ['eyes-'],
        mouth: ['mouth-'],
        outfit: ['-outfit-'],
        equipment: ['-equipment-'],
    };

    class PortraitLayerUpdater {
        constructor(studio) {
            this.studio = studio;
        }

        layer(slot) {
            return this.studio.querySelector(
                LAYERS[slot] || ''
            );
        }

        apply(slot, assetId) {
            const layer = this.layer(slot);

            if (!(layer instanceof SVGElement)) {
                return false;
            }

            if (slot === 'eyes' || slot === 'mouth'
                || slot === 'outfit' || slot === 'equipment') {
                return this.replacePart(
                    layer,
                    slot,
                    assetId
                );
            }

            layer.replaceChildren();

            if (!assetId || assetId.endsWith('-none')) {
                return true;
            }

            layer.appendChild(
                this.useElement(assetId)
            );

            layer.dataset.portraitUsingAssets = 'true';

            return true;
        }

        replacePart(layer, slot, assetId) {
            const marker = layerParts[slot][0];

            layer.querySelectorAll(
                'use[data-portrait-asset-id]'
            ).forEach(function (use) {
                const value =
                    use.dataset.portraitAssetId || '';

                if (value.includes(marker)) {
                    use.remove();
                }
            });

            if (!assetId || assetId.endsWith('-none')) {
                return true;
            }

            layer.appendChild(
                this.useElement(assetId)
            );

            layer.dataset.portraitUsingAssets = 'true';

            return true;
        }

        useElement(assetId) {
            const use = document.createElementNS(
                'http://www.w3.org/2000/svg',
                'use'
            );

            const href =
                '#gmrc-portrait-asset-' + assetId;

            use.setAttribute('href', href);
            use.setAttribute('xlink:href', href);
            use.dataset.portraitAssetId = assetId;

            return use;
        }
    }

    studioApi.LayerUpdater = PortraitLayerUpdater;
})(window);
