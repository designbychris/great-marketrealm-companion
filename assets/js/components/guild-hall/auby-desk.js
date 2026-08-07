(function (window, document) {
    'use strict';

    const fallbackScenes = [
        {
            id: 'dawn',
            daypart: 'dawn',
            hours: [5, 6, 7],
            image: 'auby-desk-dawn-hero.webp',
            ambient_effects: ['steam', 'dust', 'window-glow'],
            heading: 'A new adventure awaits.',
            status: 'The Guild Hall is quiet. Auby is already preparing today\'s records.',
            note_title: 'First note of the day',
            note_message: 'Fresh parchment. Fresh tea. Fresh adventures. I had better find my good quill.',
            tea_message: 'Fresh tea has just been brewed.',
        },
        {
            id: 'morning',
            daypart: 'morning',
            hours: [8, 9, 10, 11],
            image: 'auby-desk-morning-hero.webp',
            ambient_effects: ['steam', 'dust', 'window-glow'],
            heading: 'Good morning, adventurer!',
            status: 'Auby has already organised today\'s Guild Records. Mostly.',
            note_title: 'Auby left this here',
            note_message: 'You\'re just in time. I have the Register open and I only misplaced one page this morning.',
            tea_message: 'The first proper pot of tea is ready.',
        },
        {
            id: 'afternoon',
            daypart: 'afternoon',
            hours: [12, 13, 14, 15, 16],
            image: 'auby-desk-afternoon-hero.webp',
            ambient_effects: ['dust', 'window-glow'],
            heading: 'Someone has been busy.',
            status: 'Auby appears to be thinking very hard about something. Possibly cake.',
            note_title: 'Auby left this here',
            note_message: 'I left your Guild Journal open for you. I was absolutely not reading it. Also, I found a copper coin under the desk. We should probably work out whose it is.',
            tea_message: 'Tea is still warm. Probably.',
        },
        {
            id: 'evening',
            daypart: 'evening',
            hours: [17, 18, 19, 20],
            image: 'auby-desk-evening-hero.webp',
            ambient_effects: ['lamp-glow', 'dust'],
            heading: 'Welcome back to the Guild Hall.',
            status: 'The lamps are lit, the records are filed, and the kettle is still warm.',
            note_title: 'Evening note',
            note_message: 'I finished the last stack of records. Well... the last stack I am admitting exists.',
            tea_message: 'One last cup before the lamps go down?',
        },
        {
            id: 'night',
            daypart: 'night',
            hours: [21, 22, 23],
            image: 'auby-desk-night-hero.webp',
            ambient_effects: ['steam', 'lamp-glow', 'stars'],
            heading: 'Burning the midnight oil?',
            status: 'Auby is still awake... somehow. The Guild Hall has gone wonderfully quiet.',
            note_title: 'A late note from Auby',
            note_message: 'Don\'t forget to get some sleep. This advice is aimed at both of us.',
            tea_message: 'Herbal tea only at this hour.',
        },
        {
            id: 'late-night',
            daypart: 'late-night',
            hours: [0, 1, 2, 3, 4],
            image: 'auby-desk-late-night-hero.webp',
            ambient_effects: ['lamp-glow', 'stars', 'sleep'],
            heading: 'Shhh... Auby is asleep.',
            status: 'Please tread quietly. The Guild Hall never truly closes.',
            note_title: 'Leave this for morning',
            note_message: 'If you need anything, leave me a note. If it is urgent... leave two notes.',
            tea_message: 'The tea has gone cold. Let him sleep.',
        },
    ];

    const sceneForHour = function (scenes, hour) {
        return scenes.find(function (scene) {
            return Array.isArray(scene.hours)
                && scene.hours.includes(hour);
        }) || scenes[0];
    };

    const setText = function (root, selector, value) {
        const element = root.querySelector(selector);

        if (
            element instanceof HTMLElement
            && typeof value === 'string'
        ) {
            element.textContent = value;
        }
    };

    const applyScene = function (desk, scene) {
        if (!scene) {
            return;
        }

        const base = desk.dataset.aubySceneBase || '';

        desk.dataset.guildHallDaypart = '';
        desk.dataset.ambient = '';

        desk.dataset.guildHallDaypart =
            scene.daypart || scene.id;

        desk.dataset.ambient =
            (scene.ambient_effects || []).join(' ');

        desk.style.setProperty(
            '--gmrc-auby-desk-scene',
            'url("' + base + scene.image + '")'
        );

        setText(desk, '[data-auby-desk-title]', scene.heading);
        setText(desk, '[data-auby-desk-status]', scene.status);
        setText(desk, '[data-auby-note-title]', scene.note_title);
        setText(desk, '[data-auby-note-message]', scene.note_message);
        setText(desk, '[data-auby-tea-message]', scene.tea_message);

        const note = desk.querySelector(
            '[data-auby-sticky-note]'
        );

        if (
            note instanceof HTMLElement
            && typeof scene.note_title === 'string'
        ) {
            note.setAttribute(
                'aria-label',
                scene.note_title
            );
        }

        desk.dispatchEvent(
            new CustomEvent(
                'gmrc:guild-hall:daypart-changed',
                {
                    bubbles: true,
                    detail: {
                        scene: scene.id,
                        daypart:
                            scene.daypart || scene.id,
                    },
                }
            )
        );
    };

    const loadScenes = function (desk) {
        const base = desk.dataset.aubySceneBase || '';

        if (!('fetch' in window)) {
            return Promise.resolve(fallbackScenes);
        }

        return window
            .fetch(
                base + 'manifest.json',
                { credentials: 'same-origin' }
            )
            .then(function (response) {
                if (!response.ok) {
                    throw new Error(
                        'Auby desk manifest unavailable.'
                    );
                }

                return response.json();
            })
            .then(function (manifest) {
                if (
                    !manifest
                    || !Array.isArray(manifest.scenes)
                ) {
                    return fallbackScenes;
                }

                return manifest.scenes;
            })
            .catch(function () {
                return fallbackScenes;
            });
    };

    const initialise = function (desk) {
        if (!(desk instanceof HTMLElement)) {
            return;
        }

        loadScenes(desk).then(function (scenes) {
            applyScene(
                desk,
                sceneForHour(
                    scenes,
                    new Date().getHours()
                )
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
