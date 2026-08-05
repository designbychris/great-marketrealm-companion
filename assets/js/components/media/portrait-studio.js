/**
 * Great Marketrealm Companion
 * Guild Illuminator / Portrait Studio
 */

(function () {
    'use strict';

    const SVG_NAMESPACE =
        'http://www.w3.org/2000/svg';

    /**
     * Portrait colour collections.
     *
     * These mirror the provisional palettes rendered by PHP.
     */
    const backgroundPalettes = [
        [
            '#fff4ce',
            '#e5c884',
            '#a77a3c',
        ],
        [
            '#eef1d2',
            '#b9c48d',
            '#68774b',
        ],
        [
            '#eee1f1',
            '#b48cb8',
            '#604368',
        ],
    ];

    const bodyPalettes = [
        [
            '#705078',
            '#392240',
        ],
        [
            '#77895d',
            '#38482f',
        ],
        [
            '#a25f4e',
            '#593125',
        ],
    ];

    const outfitPalettes = [
        [
            '#9d5162',
            '#5c2433',
        ],
        [
            '#687f50',
            '#344329',
        ],
        [
            '#596f94',
            '#2e3d5a',
        ],
    ];

    /**
     * Produce a stable positive integer from text.
     */
    const hashValue = function (value) {
        let hash = 2166136261;

        Array.from(String(value)).forEach(
            function (character) {
                hash ^= character.codePointAt(0);

                hash = Math.imul(
                    hash,
                    16777619
                );
            }
        );

        return hash >>> 0;
    };

    /**
     * Return a deterministic variant between 1 and quantity.
     */
    const variantFor = function (
        seed,
        slot,
        quantity = 3
    ) {
        quantity = Math.max(
            1,
            Number(quantity) || 1
        );

        return (
            hashValue(
                seed + '|' + slot
            ) % quantity
        ) + 1;
    };

    /**
     * Create an SVG element with attributes.
     */
    const svgElement = function (
        tagName,
        attributes = {}
    ) {
        const element = document.createElementNS(
            SVG_NAMESPACE,
            tagName
        );

        Object.entries(attributes).forEach(
            function (entry) {
                element.setAttribute(
                    entry[0],
                    String(entry[1])
                );
            }
        );

        return element;
    };

    /**
     * Apply a numbered variant class to a portrait layer.
     */
    const applyVariantClass = function (
        layer,
        variant
    ) {
        if (!(layer instanceof SVGElement)) {
            return;
        }

        Array.from(layer.classList).forEach(
            function (className) {
                if (
                    className.indexOf(
                        'gmrc-portrait-layer--variant-'
                    ) === 0
                ) {
                    layer.classList.remove(
                        className
                    );
                }
            }
        );

        layer.classList.add(
            'gmrc-portrait-layer--variant-'
                + variant
        );
    };

    /**
     * Apply colours to a gradient.
     */
    const applyGradient = function (
        gradient,
        colours
    ) {
        if (!(gradient instanceof SVGElement)) {
            return;
        }

        const stops = gradient.querySelectorAll(
            'stop'
        );

        colours.forEach(function (
            colour,
            index
        ) {
            const stop = stops[index];

            if (stop instanceof SVGElement) {
                stop.setAttribute(
                    'stop-color',
                    colour
                );
            }
        });
    };

    /**
     * Remove dynamically managed equipment artwork.
     */
    const clearEquipment = function (
        classLayer
    ) {
        if (!(classLayer instanceof SVGElement)) {
            return;
        }

        classLayer
            .querySelectorAll(
                '[data-live-portrait-equipment]'
            )
            .forEach(function (element) {
                element.remove();
            });

        /*
         * Remove the equipment initially rendered by PHP.
         *
         * Garment elements remain untouched because only the
         * equipment path has this class and its following artwork
         * is positioned at the end of the class layer.
         */
        const originalEquipment =
            classLayer.querySelector(
                '.gmrc-portrait-layers__equipment'
            );

        if (
            originalEquipment
                instanceof SVGElement
        ) {
            let element = originalEquipment;

            while (element) {
                const next =
                    element.nextElementSibling;

                element.remove();

                element = next;
            }
        }
    };

    /**
     * Draw the selected provisional equipment variant.
     */
    const drawEquipment = function (
        classLayer,
        variant
    ) {
        if (!(classLayer instanceof SVGElement)) {
            return;
        }

        clearEquipment(classLayer);

        if (variant === 1) {
            /*
             * Sword or cleaver.
             */
            const blade = svgElement(
                'path',
                {
                    d:
                        'M350 170 '
                        + 'L366 185 '
                        + 'L250 390 '
                        + 'L228 378 Z',
                    fill: '#69513f',
                    stroke: '#35271e',
                    'stroke-width': '5',
                    class:
                        'gmrc-portrait-layers__equipment',
                    'data-live-portrait-equipment':
                        'true',
                }
            );

            const handle = svgElement(
                'path',
                {
                    d: 'M337 156 L382 202',
                    fill: 'none',
                    stroke: '#bc8c35',
                    'stroke-width': '13',
                    'stroke-linecap': 'round',
                    'data-live-portrait-equipment':
                        'true',
                }
            );

            classLayer.append(
                blade,
                handle
            );

            return;
        }

        if (variant === 2) {
            /*
             * Bow.
             */
            const bow = svgElement(
                'path',
                {
                    d:
                        'M338 160 '
                        + 'Q410 290 339 430',
                    fill: 'none',
                    stroke: '#78502c',
                    'stroke-width': '12',
                    'stroke-linecap': 'round',
                    class:
                        'gmrc-portrait-layers__equipment',
                    'data-live-portrait-equipment':
                        'true',
                }
            );

            const string = svgElement(
                'path',
                {
                    d:
                        'M338 160 '
                        + 'Q292 295 339 430',
                    fill: 'none',
                    stroke: '#d7bd7b',
                    'stroke-width': '3',
                    'data-live-portrait-equipment':
                        'true',
                }
            );

            classLayer.append(
                bow,
                string
            );

            return;
        }

        /*
         * Staff.
         */
        const staff = svgElement(
            'path',
            {
                d:
                    'M348 150 '
                    + 'L360 450',
                fill: 'none',
                stroke: '#68442b',
                'stroke-width': '12',
                'stroke-linecap': 'round',
                class:
                    'gmrc-portrait-layers__equipment',
                'data-live-portrait-equipment':
                    'true',
            }
        );

        const crystal = svgElement(
            'circle',
            {
                cx: '348',
                cy: '145',
                r: '30',
                fill: '#7c5790',
                stroke: '#efd58c',
                'stroke-width': '7',
                'data-live-portrait-equipment':
                    'true',
            }
        );

        classLayer.append(
            staff,
            crystal
        );
    };

    const backgroundLayers = [
    'background-parchment-01',
    'background-market-arch-01',
    'background-guild-hall-01',
    ];
    
    const eyeLayers = [
        'eyes-round-01',
        'eyes-bright-01',
        'eyes-determined-01',
    ];
    
    const mouthLayers = [
        'mouth-neutral-01',
        'mouth-smile-01',
        'mouth-grin-01',
    ];
    
    const frameLayers = [
        'frame-guild-gold-01',
        'frame-vine-gold-01',
        'frame-market-scroll-01',
    ];
    
    const effectLayers = [
        'effects-none',
        'effects-gold-motes-01',
        'effects-ink-sparks-01',
    ];

    const portraitSeedFor = function (value) {
            const first = hashValue(
                'primary|' + value
            );
        
            const second = hashValue(
                'secondary|' + value
            );
        
            return first
                .toString(16)
                .padStart(8, '0')
                .slice(-8)
                + second
                    .toString(16)
                    .padStart(8, '0')
                    .slice(-8);
        };
    
    /**
     * Initialise every portrait studio on the page.
     */
    const initialisePortraitStudios = function () {
        const studios = document.querySelectorAll(
            '[data-portrait-studio]'
        );

        const reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        studios.forEach(function (studio) {
            const form = studio.closest('form');

            /*
             * Saved portraits are rendered by PHP and do not need
             * the live provisional recipe.
             */
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (
                studio.dataset.portraitInitialised
                    === 'true'
            ) {
                return;
            }

            studio.dataset.portraitInitialised =
                'true';

            const nameOutput = studio.querySelector(
                '[data-portrait-name]'
            );

            const raceOutput = studio.querySelector(
                '[data-portrait-race-label]'
            );

            const classOutput = studio.querySelector(
                '[data-portrait-class-label]'
            );

            const statusOutput = studio.querySelector(
                '[data-portrait-status]'
            );

            const initialOutput = studio.querySelector(
                '[data-portrait-initial]'
            );

            const backgroundLayer =
                studio.querySelector(
                    '.gmrc-portrait-layer--background'
                );

            const raceLayer = studio.querySelector(
                '.gmrc-portrait-layer--race'
            );

            const classLayer = studio.querySelector(
                '.gmrc-portrait-layer--class'
            );

            const effectsLayer =
                studio.querySelector(
                    '.gmrc-portrait-layer--effects'
                );

            const backgroundGradient =
                studio.querySelector(
                    'radialGradient'
                );

            const linearGradients =
                studio.querySelectorAll(
                    'linearGradient'
                );

            const bodyGradient =
                linearGradients[0] || null;

            const garmentGradient =
                linearGradients[1] || null;

            let nameTimer = null;
            let updateTimer = null;

            /**
             * Return a selected input.
             */
            const selectedInput = function (name) {
                return form.querySelector(
                    'input[name="'
                        + name
                        + '"]:checked'
                );
            };

            /**
             * Return the written character name.
             */
            const characterName = function () {
                const input = form.querySelector(
                    'input[name="name"]'
                );

                if (!(input instanceof HTMLInputElement)) {
                    return '';
                }

                return input.value.trim();
            };

            /**
             * Return the first visible character.
             */
            const initialFor = function (name) {
                const characters = Array.from(
                    name.trim()
                );

                return characters.length > 0
                    ? characters[0]
                        .toLocaleUpperCase()
                    : '?';
            };

            /**
             * Restart the canvas awakening animation.
             */
            const awaken = function () {
                if (reducedMotion.matches) {
                    return;
                }

                studio.classList.remove(
                    'gmrc-illuminated-portrait--updating'
                );

                void studio.offsetWidth;

                studio.classList.add(
                    'gmrc-illuminated-portrait--updating'
                );

                window.clearTimeout(
                    updateTimer
                );

                updateTimer = window.setTimeout(
                    function () {
                        studio.classList.remove(
                            'gmrc-illuminated-portrait--updating'
                        );
                    },
                    660
                );
            };

            /**
             * Write a provisional portrait value into its
             * corresponding hidden form field.
             */
            const writePortraitField = function (
                field,
                value
            ) {
                const input = form.querySelector(
                    '[data-portrait-field="'
                        + field
                        + '"]'
                );

                if (!(input instanceof HTMLInputElement)) {
                    return;
                }

                input.value = String(value || '');
            };

            /**
 * Apply the provisional portrait recipe.
 */
const applyRecipe = function (
    name,
    race,
    characterClass
) {
    /*
     * The separators prevent combinations such as
     * "ab" + "c" matching "a" + "bc".
     */
    const seed = [
        name.toLocaleLowerCase(),
        race,
        characterClass,
    ].join('|');

    const backgroundVariant =
        variantFor(
            seed,
            'background'
        );

    const bodyVariant =
        variantFor(
            seed,
            'body'
        );

    const headVariant =
        variantFor(
            seed,
            'head'
        );

    const eyesVariant =
        variantFor(
            seed,
            'eyes'
        );

    const mouthVariant =
        variantFor(
            seed,
            'mouth'
        );

    const heritageVariant =
        variantFor(
            seed,
            'heritage'
        );

    const outfitVariant =
        variantFor(
            seed,
            'outfit'
        );

    const equipmentVariant =
        variantFor(
            seed,
            'equipment'
        );

    const accessoryVariant =
        variantFor(
            seed,
            'class-accessory'
        );

    const frameVariant =
        variantFor(
            seed,
            'frame'
        );

    const effectsVariant =
        variantFor(
            seed,
            'effects'
        );

    const seedValue =
        portraitSeedFor(seed);

    /*
     * These identifiers match the PHP
     * PortraitLayerRegistry exactly.
     */
    const backgroundLayerId =
        backgroundLayers[
            backgroundVariant - 1
        ];

    const bodyLayerId =
        race !== ''
            ? race
                + '-body-0'
                + bodyVariant
            : '';

    const headLayerId =
        race !== ''
            ? race
                + '-head-0'
                + headVariant
            : '';

    const eyesLayerId =
        eyeLayers[
            eyesVariant - 1
        ];

    const mouthLayerId =
        mouthLayers[
            mouthVariant - 1
        ];

    const paletteLayerId =
        race !== ''
            ? race
                + '-palette-0'
                + bodyVariant
            : '';

    const heritageLayerId =
        race !== ''
            ? (
                heritageVariant === 1
                    ? race
                        + '-heritage-none'
                    : race
                        + '-heritage-0'
                        + (
                            heritageVariant - 1
                        )
            )
            : '';

    const outfitLayerId =
        characterClass !== ''
            ? characterClass
                + '-outfit-0'
                + outfitVariant
            : '';

    const equipmentLayerId =
        characterClass !== ''
            ? characterClass
                + '-equipment-0'
                + equipmentVariant
            : '';

    const accessoryLayerId =
        characterClass !== ''
            ? (
                accessoryVariant === 1
                    ? characterClass
                        + '-accessory-none'
                    : characterClass
                        + '-accessory-0'
                        + (
                            accessoryVariant - 1
                        )
            )
            : '';

    const frameLayerId =
        frameLayers[
            frameVariant - 1
        ];

    const effectsLayerId =
        effectLayers[
            effectsVariant - 1
        ];

    /*
     * Keep the component data attributes synchronised.
     */
    studio.dataset.portraitSeed =
        seedValue;

    studio.dataset.portraitBackground =
        backgroundLayerId;

    studio.dataset.portraitBody =
        bodyLayerId;

    studio.dataset.portraitHead =
        headLayerId;

    studio.dataset.portraitEyes =
        eyesLayerId;

    studio.dataset.portraitMouth =
        mouthLayerId;

    studio.dataset.portraitPalette =
        paletteLayerId;

    studio.dataset.portraitHeritage =
        heritageLayerId;

    studio.dataset.portraitOutfit =
        outfitLayerId;

    studio.dataset.portraitEquipment =
        equipmentLayerId;

    studio.dataset.portraitAccessory =
        accessoryLayerId;

    studio.dataset.portraitFrame =
        frameLayerId;

    studio.dataset.portraitEffects =
        effectsLayerId;

    /*
     * Write the same canonical values into the form.
     */
    writePortraitField(
        'seed',
        seedValue
    );

    writePortraitField(
        'background',
        backgroundLayerId
    );

    writePortraitField(
        'body',
        bodyLayerId
    );

    writePortraitField(
        'head',
        headLayerId
    );

    writePortraitField(
        'eyes',
        eyesLayerId
    );

    writePortraitField(
        'mouth',
        mouthLayerId
    );

    writePortraitField(
        'palette',
        paletteLayerId
    );

    writePortraitField(
        'heritage',
        heritageLayerId
    );

    writePortraitField(
        'outfit',
        outfitLayerId
    );

    writePortraitField(
        'equipment',
        equipmentLayerId
    );

    writePortraitField(
        'class_accessory',
        accessoryLayerId
    );

    writePortraitField(
        'frame',
        frameLayerId
    );

    writePortraitField(
        'effects',
        effectsLayerId
    );

    /*
     * Apply the live provisional artwork.
     */
    applyGradient(
        backgroundGradient,
        backgroundPalettes[
            backgroundVariant - 1
        ]
    );

    applyGradient(
        bodyGradient,
        bodyPalettes[
            bodyVariant - 1
        ]
    );

    applyGradient(
        garmentGradient,
        outfitPalettes[
            outfitVariant - 1
        ]
    );

    applyVariantClass(
        backgroundLayer,
        backgroundVariant
    );

    applyVariantClass(
        raceLayer,
        bodyVariant
    );

    applyVariantClass(
        classLayer,
        outfitVariant
    );

    applyVariantClass(
        effectsLayer,
        effectsVariant
    );

    /*
     * Adjust the provisional body proportions.
     */
    if (raceLayer instanceof SVGElement) {
        const ellipses =
            raceLayer.querySelectorAll(
                'ellipse'
            );

        const torso = ellipses[0];
        const head = ellipses[1];

        if (torso instanceof SVGElement) {
            torso.setAttribute(
                'rx',
                bodyVariant === 2
                    ? '138'
                    : (
                        bodyVariant === 3
                            ? '116'
                            : '126'
                    )
            );
        }

        if (head instanceof SVGElement) {
            head.setAttribute(
                'rx',
                bodyVariant === 3
                    ? '82'
                    : '92'
            );

            head.setAttribute(
                'ry',
                bodyVariant === 2
                    ? '118'
                    : '110'
            );
        }
    }

    drawEquipment(
        classLayer,
        equipmentVariant
    );

    /*
     * Change the visible effect glyphs.
     */
    if (effectsLayer instanceof SVGElement) {
        const effects =
            effectsLayer.querySelectorAll(
                'text'
            );

        if (effects[0]) {
            effects[0].textContent =
                effectsVariant === 2
                    ? '❧'
                    : '✦';
        }

        if (effects[1]) {
            effects[1].textContent =
                effectsVariant === 3
                    ? '✺'
                    : '✧';
        }

        if (effects[2]) {
            effects[2].textContent =
                effectsVariant === 1
                    ? '✧'
                    : '✦';
        }
    }
};


            /**
             * Synchronise the complete portrait state.
             */
            const updatePortrait = function (
                animate = true
            ) {
                const name = characterName();

                const race = selectedInput(
                    'race'
                );

                const characterClass =
                    selectedInput(
                        'class'
                    );

                const raceValue =
                    race instanceof HTMLInputElement
                        ? race.value
                        : '';

                const raceLabel =
                    race instanceof HTMLInputElement
                        ? race.dataset.raceLabel || ''
                        : '';

                const classValue =
                    characterClass
                        instanceof HTMLInputElement
                            ? characterClass.value
                            : '';

                const classLabel =
                    characterClass
                        instanceof HTMLInputElement
                            ? characterClass.dataset
                                .classLabel || ''
                            : '';

                studio.dataset.portraitRace =
                    raceValue;

                studio.dataset.portraitClass =
                    classValue;

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--named',
                    name !== ''
                );

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--has-race',
                    raceValue !== ''
                );

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--has-class',
                    classValue !== ''
                );

                const complete =
                    name !== ''
                    && raceValue !== ''
                    && classValue !== '';

                studio.classList.toggle(
                    'gmrc-illuminated-portrait--complete',
                    complete
                );

                if (nameOutput instanceof HTMLElement) {
                    nameOutput.textContent =
                        name || 'Awaiting Subject';
                }

                if (initialOutput instanceof SVGElement) {
                    initialOutput.textContent =
                        initialFor(name);
                }

                if (raceOutput instanceof HTMLElement) {
                    raceOutput.textContent =
                        raceLabel
                            || 'Heritage unwritten';
                }

                if (classOutput instanceof HTMLElement) {
                    classOutput.textContent =
                        classLabel
                            || 'Calling unchosen';
                }

                if (statusOutput instanceof HTMLElement) {
                    if (complete) {
                        statusOutput.textContent =
                            'Illumination complete';
                    } else if (
                        raceValue !== ''
                        && classValue !== ''
                    ) {
                        statusOutput.textContent =
                            'Awaiting the subject’s name';
                    } else if (raceValue !== '') {
                        statusOutput.textContent =
                            'Sketch complete — awaiting attire';
                    } else if (classValue !== '') {
                        statusOutput.textContent =
                            'Calling recorded — awaiting heritage';
                    } else {
                        statusOutput.textContent =
                            'Portrait awaiting inscription';
                    }
                }

                applyRecipe(
                    name,
                    raceValue,
                    classValue
                );

                if (animate) {
                    awaken();
                }
            };

            const nameInput = form.querySelector(
                'input[name="name"]'
            );

            if (
                nameInput
                    instanceof HTMLInputElement
            ) {
                nameInput.addEventListener(
                    'input',
                    function () {
                        window.clearTimeout(
                            nameTimer
                        );

                        nameTimer = window.setTimeout(
                            function () {
                                updatePortrait(true);
                            },
                            280
                        );
                    }
                );
            }

            form.addEventListener(
                'change',
                function (event) {
                    const target = event.target;

                    if (
                        !(
                            target
                                instanceof HTMLInputElement
                        )
                    ) {
                        return;
                    }

                    if (
                        target.name !== 'race'
                        && target.name !== 'class'
                    ) {
                        return;
                    }

                    updatePortrait(true);
                }
            );

            updatePortrait(false);

            window.addEventListener(
                'pagehide',
                function () {
                    window.clearTimeout(
                        nameTimer
                    );

                    window.clearTimeout(
                        updateTimer
                    );
                },
                {
                    once: true,
                }
            );
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initialisePortraitStudios
        );
    } else {
        initialisePortraitStudios();
    }
})();
