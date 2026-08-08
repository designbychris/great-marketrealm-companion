(function (window, document) {
    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    const fallback = {
        story_beats: [],
        seasons: {
            spring: [3, 4, 5],
            summer: [6, 7, 8],
            harvest: [9, 10, 11],
            winter: [12, 1, 2],
        },
    };

    const seasonForMonth = function (manifest, month) {
        const seasons = manifest.seasons || fallback.seasons;
        const entries = Object.entries(seasons);

        const match = entries.find(function (entry) {
            return Array.isArray(entry[1])
                && entry[1].includes(month);
        });

        return match ? match[0] : 'spring';
    };

    const deterministicIndex = function (length) {
        if (length <= 1) {
            return 0;
        }

        const now = new Date();
        const key =
            now.getFullYear()
            + '-'
            + now.getMonth()
            + '-'
            + now.getDate();

        let hash = 0;

        for (let index = 0; index < key.length; index += 1) {
            hash =
                ((hash << 5) - hash)
                + key.charCodeAt(index);

            hash |= 0;
        }

        return Math.abs(hash) % length;
    };

    const availableBeats = function (manifest, daypart) {
        return (manifest.story_beats || []).filter(
            function (beat) {
                return !Array.isArray(beat.dayparts)
                    || beat.dayparts.includes(daypart);
            }
        );
    };

    const clearBeat = function (desk) {
        desk
            .querySelectorAll(
                '[data-living-guild-beat]'
            )
            .forEach(function (element) {
                element.classList.remove('is-active');

                if (
                    element instanceof HTMLButtonElement
                ) {
                    element.tabIndex = -1;
                }
            });

        desk.removeAttribute('data-living-guild-beat');
    };

    const activateBeat = function (desk, beat) {
        if (!beat || !beat.id) {
            return;
        }

        clearBeat(desk);

        const element = desk.querySelector(
            '[data-living-guild-beat="'
            + beat.id
            + '"]'
        );

        if (!(element instanceof HTMLElement)) {
            return;
        }

        desk.dataset.livingGuildBeat = beat.id;
        element.classList.add('is-active');

        if (
            beat.interactive
            && element instanceof HTMLButtonElement
        ) {
            element.tabIndex = 0;
        }
    };

    const showStatus = function (desk, message) {
        const status = desk.querySelector(
            '[data-living-guild-status]'
        );

        if (!(status instanceof HTMLElement)) {
            return;
        }

        status.textContent = message;

        window.setTimeout(function () {
            status.textContent = '';
        }, 5200);
    };

    const wireCoin = function (desk, manifest) {
        const coin = desk.querySelector(
            '[data-living-guild-coin]'
        );

        if (!(coin instanceof HTMLButtonElement)) {
            return;
        }

        coin.addEventListener('click', function () {
            const beat = (manifest.story_beats || []).find(
                function (candidate) {
                    return candidate.id === 'copper-coin';
                }
            );

            coin.classList.add('is-collected');
            coin.tabIndex = -1;

            window.sessionStorage.setItem(
                'gmrcLivingGuildCoinFound',
                'true'
            );

            showStatus(
                desk,
                beat && beat.message
                    ? beat.message
                    : 'You found a copper coin.'
            );

            window.setTimeout(function () {
                coin.classList.remove(
                    'is-active',
                    'is-collected'
                );
            }, 620);
        });
    };

    const applyEnvironment = function (desk, manifest) {
        const daypart =
            desk.dataset.guildHallDaypart
            || 'afternoon';

        const month =
            new Date().getMonth() + 1;

        desk.dataset.guildSeason =
            seasonForMonth(manifest, month);

        if (reducedMotion.matches) {
            return;
        }

        const beats =
            availableBeats(manifest, daypart);

        if (beats.length === 0) {
            return;
        }

        const beat = beats[
            deterministicIndex(beats.length)
        ];

        /*
         * The room settles first. One story beat happens once.
         */
        const delay =
            7600
            + Math.floor(Math.random() * 4200);

        window.setTimeout(function () {
            activateBeat(desk, beat);
        }, delay);
    };

    const loadManifest = function (desk) {
        const url =
            desk.dataset.livingGuildManifest || '';

        if (!url || !('fetch' in window)) {
            return Promise.resolve(fallback);
        }

        return window
            .fetch(
                url,
                { credentials: 'same-origin' }
            )
            .then(function (response) {
                if (!response.ok) {
                    throw new Error(
                        'Living Guild manifest unavailable.'
                    );
                }

                return response.json();
            })
            .catch(function () {
                return fallback;
            });
    };

    const initialise = function (desk) {
        if (!(desk instanceof HTMLElement)) {
            return;
        }

        const layer = desk.querySelector(
            '[data-living-guild]'
        );

        if (!(layer instanceof HTMLElement)) {
            return;
        }

        loadManifest(layer).then(function (manifest) {
            wireCoin(desk, manifest);
            applyEnvironment(desk, manifest);

            desk.addEventListener(
                'gmrc:guild-hall:daypart-changed',
                function () {
                    clearBeat(desk);
                    applyEnvironment(desk, manifest);
                }
            );
        });
    };

    const boot = function () {
        document
            .querySelectorAll('[data-auby-desk]')
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
