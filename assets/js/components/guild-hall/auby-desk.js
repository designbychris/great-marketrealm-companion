(function (window, document) {
    'use strict';

    const states = {
        morning: {
            message:
                'Auby is setting out fresh parchment and making the first tea of the day.',
        },
        day: {
            message:
                'Auby is cataloguing portraits, records and several suspicious crumbs.',
        },
        evening: {
            message:
                'The lamps are on. Auby is finishing the last Guild records of the day.',
        },
        night: {
            message:
                'The Guild Hall is quiet. Auby is still awake, although the tea may be winning.',
        },
    };

    const daypartForHour = function (hour) {
        if (hour >= 5 && hour < 11) {
            return 'morning';
        }

        if (hour >= 11 && hour < 17) {
            return 'day';
        }

        if (hour >= 17 && hour < 21) {
            return 'evening';
        }

        return 'night';
    };

    const imageFor = function (image, daypart) {
        if (!(image instanceof HTMLImageElement)) {
            return '';
        }

        return image.dataset[daypart] || '';
    };

    const update = function (desk) {
        const daypart = daypartForHour(
            new Date().getHours()
        );

        desk.dataset.guildHallDaypart = daypart;

        const image = desk.querySelector(
            '[data-auby-desk-image]'
        );

        const nextImage = imageFor(
            image,
            daypart
        );

        if (
            image instanceof HTMLImageElement
            && nextImage !== ''
            && image.src !== nextImage
        ) {
            image.src = nextImage;
        }

        const status = desk.querySelector(
            '[data-auby-desk-status]'
        );

        if (
            status instanceof HTMLElement
            && states[daypart]
        ) {
            status.textContent =
                states[daypart].message;
        }
    };

    const boot = function () {
        document
            .querySelectorAll('[data-auby-desk]')
            .forEach(function (desk) {
                if (desk instanceof HTMLElement) {
                    update(desk);
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
