(function (window, document) {
    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    /**
     * Phase III.7.3.4 — The Living Illuminator.
     * Living Effects Engine.
     *
     * Portrait recipes remain deterministic artwork. These behaviours are a
     * presentation layer derived from race/class identity and never become
     * persisted portrait slots themselves.
     */
    const raceEffects = Object.freeze({
        fructan: ['botanical-motes'],
        vegfolk: ['botanical-motes'],
        herbfolk: ['botanical-motes'],
        melonian: ['botanical-motes'],
        stalker: ['botanical-motes'],
        fungifolk: ['spore-drift'],
        'drink-folk': ['bubble-rise'],
        frostreem: ['frost-motes'],
        sweetfolk: ['sugar-sparkle'],
        fluffling: ['sugar-sparkle'],
        'marshmallow-folk': ['sugar-sparkle'],
        recalled: ['recall-flicker'],
    });

    const classEffects = Object.freeze({
        artificer: ['artificer-spark'],
        cleric: ['sacred-glint'],
        druid: ['nature-motes'],
        paladin: ['sacred-glint'],
        sorcerer: ['arcane-glimmer'],
        warlock: ['eldritch-wisp'],
        wizard: ['arcane-glimmer'],

        /* Legacy saved characters retain their old class personality. */
        'cleaver-saint': ['sacred-glint'],
        grocer: ['guild-sparkle'],
    });

    const particleCounts = Object.freeze({
        'botanical-motes': 5,
        'spore-drift': 6,
        'bubble-rise': 5,
        'frost-motes': 5,
        'sugar-sparkle': 5,
        'recall-flicker': 3,
        'artificer-spark': 4,
        'sacred-glint': 3,
        'nature-motes': 5,
        'arcane-glimmer': 5,
        'eldritch-wisp': 4,
        'guild-sparkle': 4,
    });

    const normalise = function (value) {
        return typeof value === 'string'
            ? value.trim().toLowerCase()
            : '';
    };

    const hash = function (value) {
        let result = 2166136261;

        for (let index = 0; index < value.length; index += 1) {
            result ^= value.charCodeAt(index);
            result = Math.imul(result, 16777619);
        }

        return result >>> 0;
    };

    const fraction = function (seed, salt) {
        return (
            hash(seed + ':' + salt) % 10000
        ) / 10000;
    };

    const effectsFor = function (registry, key) {
        return registry[key] || [];
    };

    const describe = function (studio) {
        const race = normalise(
            studio.dataset.portraitRace
        );

        const characterClass = normalise(
            studio.dataset.portraitClass
        );

        return {
            race: race,
            characterClass: characterClass,
            raceEffects: effectsFor(
                raceEffects,
                race
            ),
            classEffects: effectsFor(
                classEffects,
                characterClass
            ),
        };
    };

    const removeOverlay = function (studio) {
        const overlay = studio.querySelector(
            '[data-living-effects-layer]'
        );

        if (overlay instanceof HTMLElement) {
            overlay.remove();
        }
    };

    const createParticle = function (
        effect,
        index,
        seed
    ) {
        const particle = document.createElement('span');

        particle.className =
            'gmrc-living-effect__particle';

        particle.dataset.livingEffectParticle = effect;
        particle.setAttribute('aria-hidden', 'true');

        const x = 8 + fraction(
            seed,
            effect + ':x:' + index
        ) * 84;

        const y = 10 + fraction(
            seed,
            effect + ':y:' + index
        ) * 76;

        const delay = -fraction(
            seed,
            effect + ':delay:' + index
        ) * 8;

        const duration = 4.8 + fraction(
            seed,
            effect + ':duration:' + index
        ) * 5.2;

        const scale = 0.72 + fraction(
            seed,
            effect + ':scale:' + index
        ) * 0.68;

        particle.style.setProperty(
            '--gmrc-living-x',
            x.toFixed(2) + '%'
        );

        particle.style.setProperty(
            '--gmrc-living-y',
            y.toFixed(2) + '%'
        );

        particle.style.setProperty(
            '--gmrc-living-delay',
            delay.toFixed(2) + 's'
        );

        particle.style.setProperty(
            '--gmrc-living-duration',
            duration.toFixed(2) + 's'
        );

        particle.style.setProperty(
            '--gmrc-living-scale',
            scale.toFixed(2)
        );

        return particle;
    };

    const createEffect = function (
        overlay,
        effect,
        seed
    ) {
        const group = document.createElement('span');
        const count = particleCounts[effect] || 4;

        group.className =
            'gmrc-living-effect gmrc-living-effect--'
            + effect;

        group.dataset.livingEffect = effect;
        group.setAttribute('aria-hidden', 'true');

        for (let index = 0; index < count; index += 1) {
            group.appendChild(
                createParticle(
                    effect,
                    index,
                    seed
                )
            );
        }

        overlay.appendChild(group);
    };

    const render = function (studio) {
        if (!(studio instanceof HTMLElement)) {
            return;
        }

        removeOverlay(studio);

        const canvas = studio.querySelector(
            '.gmrc-illuminated-portrait__canvas'
        );

        if (!(canvas instanceof HTMLElement)) {
            return;
        }

        const description = describe(studio);
        const mode = normalise(
            studio.dataset.portraitMode
        );

        studio.dataset.livingRaceEffects =
            description.raceEffects.join(' ');

        studio.dataset.livingClassEffects =
            description.classEffects.join(' ');

        if (
            mode === 'custom'
            || reducedMotion.matches
        ) {
            studio.dataset.livingEffectsReady = 'false';
            return;
        }

        const effects = Array.from(
            new Set(
                description.raceEffects.concat(
                    description.classEffects
                )
            )
        );

        if (effects.length === 0) {
            studio.dataset.livingEffectsReady = 'false';
            return;
        }

        const overlay = document.createElement('span');
        const seed = normalise(
            studio.dataset.portraitSeed
        ) || (
            description.race
            + ':'
            + description.characterClass
        );

        overlay.className = 'gmrc-living-effects';
        overlay.dataset.livingEffectsLayer = 'true';
        overlay.setAttribute('aria-hidden', 'true');

        effects.forEach(function (effect) {
            createEffect(
                overlay,
                effect,
                seed
            );
        });

        canvas.appendChild(overlay);
        studio.dataset.livingEffectsReady = 'true';
    };

    const initialise = function (studio) {
        if (!(studio instanceof HTMLElement)) {
            return;
        }

        render(studio);

        const observer = new MutationObserver(function (mutations) {
            const relevant = mutations.some(function (mutation) {
                return mutation.type === 'attributes';
            });

            if (relevant) {
                render(studio);
            }
        });

        observer.observe(
            studio,
            {
                attributes: true,
                attributeFilter: [
                    'data-portrait-race',
                    'data-portrait-class',
                    'data-portrait-seed',
                    'data-portrait-mode',
                ],
            }
        );

        studio.addEventListener(
            'gmrc:portrait:generation-changed',
            function () {
                render(studio);
            }
        );
    };

    const refresh = function () {
        document
            .querySelectorAll(
                '.gmrc-illuminated-portrait'
            )
            .forEach(function (studio) {
                if (studio instanceof HTMLElement) {
                    render(studio);
                }
            });
    };

    reducedMotion.addEventListener(
        'change',
        refresh
    );

    document.addEventListener(
        'visibilitychange',
        function () {
            document.documentElement.classList.toggle(
                'gmrc-living-effects-paused',
                document.hidden
            );
        }
    );

    const boot = function () {
        document
            .querySelectorAll(
                '.gmrc-illuminated-portrait'
            )
            .forEach(initialise);
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
