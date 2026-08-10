(function (window, document) {
    'use strict';

    const ASSETS = [
        ['g2-background-market-garden-01', 'gmrc-g2-background'],
        ['g2-fructan-grocer-ground-shadow-01', 'gmrc-g2-ground-shadow'],
        ['g2-fructan-body-apple-base-01', 'gmrc-g2-character gmrc-g2-body'],
        ['g2-fructan-body-apple-shadow-01', 'gmrc-g2-character gmrc-g2-body'],
        ['g2-fructan-body-apple-highlight-01', 'gmrc-g2-character gmrc-g2-body'],
        ['g2-fructan-body-apple-blush-01', 'gmrc-g2-character gmrc-g2-face'],
        ['g2-fructan-body-apple-speckles-01', 'gmrc-g2-character gmrc-g2-face'],
        ['g2-fructan-heritage-apple-leaves-01', 'gmrc-g2-character gmrc-g2-leaves'],
        ['g2-fructan-heritage-apple-leaves-shadow-01', 'gmrc-g2-character gmrc-g2-leaves'],
        ['g2-fructan-heritage-apple-leaves-highlight-01', 'gmrc-g2-character gmrc-g2-leaves'],
        ['g2-fructan-heritage-apple-stem-01', 'gmrc-g2-character gmrc-g2-stem'],
        ['g2-brows-friendly-01', 'gmrc-g2-character gmrc-g2-brows'],
        ['g2-eyes-auby-bright-01', 'gmrc-g2-character gmrc-g2-eyes'],
        ['g2-mouth-auby-smile-01', 'gmrc-g2-character gmrc-g2-mouth'],
        ['g2-eyelids-apple-closed-01', 'gmrc-g2-face-overlay gmrc-g2-eyelids'],
        ['g2-grocer-shirt-everyday-01', 'gmrc-g2-character gmrc-g2-outfit'],
        ['g2-grocer-apron-everyday-01', 'gmrc-g2-character gmrc-g2-outfit'],
        ['g2-grocer-outfit-shadow-01', 'gmrc-g2-character gmrc-g2-outfit'],
        ['g2-grocer-outfit-highlight-01', 'gmrc-g2-character gmrc-g2-outfit'],
        ['g2-grocer-stitching-01', 'gmrc-g2-character gmrc-g2-outfit'],
        ['g2-grocer-ledger-01', 'gmrc-g2-character gmrc-g2-ledger'],
        ['g2-grocer-satchel-base-01', 'gmrc-g2-character gmrc-g2-satchel'],
        ['g2-grocer-satchel-detail-01', 'gmrc-g2-character gmrc-g2-satchel'],
        ['g2-grocer-produce-01', 'gmrc-g2-character gmrc-g2-satchel'],
        ['g2-grocer-hands-01', 'gmrc-g2-character gmrc-g2-hands'],
        ['g2-grocer-boots-01', 'gmrc-g2-character gmrc-g2-boots'],
        ['g2-effects-golden-pollen-far-01', 'gmrc-g2-pollen gmrc-g2-pollen--far'],
        ['g2-effects-golden-pollen-near-01', 'gmrc-g2-pollen gmrc-g2-pollen--near'],
        ['g2-frame-guild-woodland-01', 'gmrc-g2-frame'],
    ];

    const selectedValue = function (form, name) {
        const input = form.querySelector(
            'input[name="' + name + '"]:checked'
        );

        return input instanceof HTMLInputElement
            ? input.value
            : '';
    };

    const useElement = function (assetId, classNames) {
        const use = document.createElementNS(
            'http://www.w3.org/2000/svg',
            'use'
        );

        const href = '#gmrc-portrait-asset-' + assetId;

        use.setAttribute('href', href);
        use.setAttribute('xlink:href', href);
        use.dataset.portraitAssetId = assetId;

        classNames
            .split(' ')
            .filter(Boolean)
            .forEach(function (className) {
                use.classList.add(className);
            });

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
                '.gmrc-portrait-layer'
            );
        };

        const available = function () {
            return ASSETS.every(function (asset) {
                return svg.querySelector(
                    '#gmrc-portrait-asset-' + asset[0]
                ) instanceof SVGElement;
            });
        };

        const sync = function () {
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
                    generationTwo.setAttribute(
                        'display',
                        'none'
                    );

                    svg.dataset.portraitGeneration = '1';
                    return;
                }

                const breathingGroup =
                    document.createElementNS(
                        'http://www.w3.org/2000/svg',
                        'g'
                    );

                breathingGroup.classList.add(
                    'gmrc-g2-breathing-group'
                );

                generationTwo.appendChild(
                    breathingGroup
                );

                ASSETS.forEach(function (asset) {
                    const layer = useElement(
                        asset[0],
                        asset[1]
                    );

                    const belongsToCharacter =
                        layer.classList.contains(
                            'gmrc-g2-character'
                        )
                        || layer.classList.contains(
                            'gmrc-g2-face-overlay'
                        );

                    if (belongsToCharacter) {
                        breathingGroup.appendChild(
                            layer
                        );

                        return;
                    }

                    generationTwo.appendChild(
                        layer
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
            .querySelectorAll(
                '.gmrc-illuminated-portrait'
            )
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
