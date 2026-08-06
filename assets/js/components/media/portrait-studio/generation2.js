/**
 * Generation 2 live portrait adapter.
 */
(function (window, document) {
    'use strict';

    const ASSET_IDS = [
        'g2-background-market-garden-01',
        'g2-fructan-grocer-ground-shadow-01',
        'g2-fructan-body-apple-base-01',
        'g2-fructan-body-apple-shadow-01',
        'g2-fructan-body-apple-highlight-01',
        'g2-fructan-heritage-apple-leaves-01',
        'g2-eyes-auby-bright-01',
        'g2-mouth-auby-smile-01',
        'g2-grocer-outfit-everyday-base-01',
        'g2-grocer-outfit-everyday-shadow-01',
        'g2-grocer-outfit-everyday-highlight-01',
        'g2-grocer-equipment-produce-satchel-01',
        'g2-effects-golden-pollen-01',
        'g2-frame-guild-woodland-01',
    ];

    const selectedValue = function (form, name) {
        const input = form.querySelector(
            'input[name="' + name + '"]:checked'
        );

        return input instanceof HTMLInputElement
            ? input.value
            : '';
    };

    const useElement = function (assetId) {
        const use = document.createElementNS(
            'http://www.w3.org/2000/svg',
            'use'
        );

        const href = '#gmrc-portrait-asset-' + assetId;

        use.setAttribute('href', href);
        use.setAttribute('xlink:href', href);
        use.dataset.portraitAssetId = assetId;

        return use;
    };

    const initialise = function (studio) {
        const form = studio.closest('form');
        const svg = studio.querySelector(
            '.gmrc-portrait-layers'
        );

        if (
            !(form instanceof HTMLFormElement)
            || !(svg instanceof SVGElement)
        ) {
            return;
        }

        let generationTwo = svg.querySelector(
            '.gmrc-portrait-generation-two'
        );

        if (!(generationTwo instanceof SVGElement)) {
            generationTwo = document.createElementNS(
                'http://www.w3.org/2000/svg',
                'g'
            );

            generationTwo.classList.add(
                'gmrc-portrait-generation-two'
            );

            generationTwo.dataset.portraitGeneration = '2';
            generationTwo.dataset.portraitCollection =
                'fructan-grocer';

            svg.appendChild(generationTwo);
        }

        const generationOneLayers = function () {
            return svg.querySelectorAll(
                '.gmrc-portrait-layer,'
                + '.gmrc-portrait-asset-library'
            );
        };

        const available = function () {
            return ASSET_IDS.every(function (assetId) {
                return svg.querySelector(
                    '#gmrc-portrait-asset-' + assetId
                ) instanceof SVGElement;
            });
        };

        const sync = function () {
            /*
             * Run after the Generation 1 listener has completed.
             */
            window.setTimeout(function () {
                const supported =
                    selectedValue(form, 'race') === 'fructan'
                    && selectedValue(form, 'class') === 'grocer'
                    && available();

                generationOneLayers().forEach(function (layer) {
                    if (supported) {
                        layer.setAttribute('display', 'none');
                    } else {
                        layer.removeAttribute('display');
                    }
                });

                generationTwo.replaceChildren();

                if (!supported) {
                    generationTwo.setAttribute('display', 'none');
                    svg.dataset.portraitGeneration = '1';
                    return;
                }

                ASSET_IDS.forEach(function (assetId) {
                    generationTwo.appendChild(
                        useElement(assetId)
                    );
                });

                generationTwo.removeAttribute('display');
                svg.dataset.portraitGeneration = '2';

                studio.dispatchEvent(
                    new CustomEvent(
                        'gmrc:portrait:generation-changed',
                        {
                            bubbles: true,
                            detail: {
                                generation: 2,
                                collection: 'fructan-grocer',
                            },
                        }
                    )
                );
            }, 0);
        };

        form.addEventListener('change', sync);
        form.addEventListener('input', sync);
        sync();
    };

    const boot = function () {
        document
            .querySelectorAll('.gmrc-illuminated-portrait')
            .forEach(function (studio) {
                if (studio instanceof HTMLElement) {
                    initialise(studio);
                }
            });
    };

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            boot
        );
    } else {
        boot();
    }
})(window, document);
