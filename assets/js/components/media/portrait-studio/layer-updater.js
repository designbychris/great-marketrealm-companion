/**
 * Update existing SVG <use> layers without rebuilding the portrait.
 *
 * Phase III.7.2.3 aligns this module with the actual SVG contracts
 * emitted by the PHP renderers:
 *
 * - face layers are .gmrc-portrait-layer--eyes / --mouth;
 * - class layers identify their uses with data-portrait-asset-slot;
 * - legacy live-created uses may identify themselves with
 *   data-portrait-asset-use;
 * - newly replaced uses use data-portrait-asset-id.
 */
(function (window) {
    'use strict';

    const studioApi = window.GMRCPortraitStudio;

    const LAYERS = {
        background: '.gmrc-portrait-layer--background',
        body: '.gmrc-portrait-layer--race',
        eyes: '.gmrc-portrait-layer--eyes',
        mouth: '.gmrc-portrait-layer--mouth',
        outfit: '.gmrc-portrait-layer--class',
        equipment: '.gmrc-portrait-layer--class',
        class_accessory: '.gmrc-portrait-layer--accessory',
        class_effects: '.gmrc-portrait-layer--class-effects',
        guild_ornament: '.gmrc-portrait-layer--guild-ornament',
        effects: '.gmrc-portrait-layer--effects',
        frame: '.gmrc-portrait-layer--frame',
    };

    class PortraitLayerUpdater {
        constructor(studio) {
            this.studio = studio;
        }

        layer(slot) {
            const selector = LAYERS[slot];

            if (!selector) {
                return null;
            }

            return this.studio.querySelector(selector);
        }

        apply(slot, assetId) {
            const layer = this.layer(slot);

            if (!(layer instanceof SVGElement)) {
                /*
                 * Generation 2 currently renders a single painted
                 * collection rather than independent replaceable layer
                 * groups. The Workbench therefore exposes only genuinely
                 * replaceable variants until the Great Portrait Expansion
                 * supplies alternate Generation 2 collections.
                 */
                return false;
            }

            if (slot === 'outfit' || slot === 'equipment') {
                return this.replaceClassPart(
                    layer,
                    slot,
                    assetId
                );
            }

            return this.replaceWholeLayer(
                layer,
                assetId
            );
        }

        replaceWholeLayer(layer, assetId) {
            layer.replaceChildren();

            if (!assetId || assetId.endsWith('-none')) {
                layer.setAttribute('hidden', '');
                layer.dataset.layerId = assetId || '';
                return true;
            }

            layer.appendChild(
                this.useElement(assetId)
            );

            layer.dataset.layerId = assetId;
            layer.dataset.portraitUsingAssets = 'true';
            layer.removeAttribute('hidden');

            return true;
        }

        replaceClassPart(layer, slot, assetId) {
            const uses = Array.from(
                layer.querySelectorAll('use')
            );

            uses.forEach(function (use) {
                const renderedSlot =
                    use.dataset.portraitAssetSlot || '';

                const renderedId =
                    this.assetIdFor(use);

                const matchesSlot =
                    renderedSlot === slot;

                const matchesLegacyId =
                    slot === 'outfit'
                        ? renderedId.includes('-outfit-')
                        : renderedId.includes('-equipment-');

                if (matchesSlot || matchesLegacyId) {
                    use.remove();
                }
            }, this);

            if (!assetId || assetId.endsWith('-none')) {
                return true;
            }

            layer.appendChild(
                this.useElement(
                    assetId,
                    slot
                )
            );

            layer.dataset.portraitUsingAssets = 'true';
            layer.removeAttribute('hidden');

            return true;
        }

        assetIdFor(use) {
            if (!(use instanceof SVGUseElement)) {
                return '';
            }

            const explicit =
                use.dataset.portraitAssetId
                || use.dataset.portraitAssetUse
                || '';

            if (explicit !== '') {
                return explicit;
            }

            const href =
                use.getAttribute('href')
                || use.getAttribute('xlink:href')
                || '';

            return href.replace(
                /^#gmrc-portrait-asset-/,
                ''
            );
        }

        useElement(assetId, slot = '') {
            const use = document.createElementNS(
                'http://www.w3.org/2000/svg',
                'use'
            );

            const href =
                '#gmrc-portrait-asset-' + assetId;

            use.setAttribute('href', href);
            use.setAttribute('xlink:href', href);
            use.dataset.portraitAssetId = assetId;

            if (slot !== '') {
                use.dataset.portraitAssetSlot = slot;
            }

            return use;
        }
    }

    studioApi.LayerUpdater = PortraitLayerUpdater;
    studioApi.layerSelectors = LAYERS;
})(window);
