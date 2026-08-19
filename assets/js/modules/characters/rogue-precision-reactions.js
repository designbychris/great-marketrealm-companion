(function () {
    'use strict';

    const scope = document.querySelector('[data-rogue-precision]');

    if (!(scope instanceof HTMLElement)) {
        return;
    }

    const status = scope.querySelector(
        '[data-rogue-precision-status]'
    );
    const sneakUsed = scope.querySelector(
        '[data-sneak-attack-used]'
    );
    const sneakRoll = scope.querySelector(
        '[data-sneak-attack-roll]'
    );
    const uncanny = scope.querySelector(
        '[data-uncanny-dodge-used]'
    );
    const newTurn = scope.querySelector(
        '[data-rogue-new-turn]'
    );

    const announce = function (message) {
        if (status instanceof HTMLElement) {
            status.textContent = message;
        }
    };

    const setSneakUsed = function (used) {
        if (sneakUsed instanceof HTMLButtonElement) {
            sneakUsed.disabled = used;
            sneakUsed.setAttribute(
                'aria-pressed',
                used ? 'true' : 'false'
            );
            sneakUsed.textContent = used
                ? 'Sneak Attack Used This Turn'
                : 'Mark Sneak Attack Used';
        }

        if (sneakRoll instanceof HTMLButtonElement) {
            sneakRoll.disabled = used;
        }
    };

    if (sneakUsed instanceof HTMLButtonElement) {
        sneakUsed.addEventListener('click', function () {
            setSneakUsed(true);
            announce(
                'Sneak Attack marked used for this turn. '
                + 'Start a new turn to make it ready again.'
            );
        });
    }

    if (uncanny instanceof HTMLButtonElement) {
        uncanny.addEventListener('click', function () {
            uncanny.disabled = true;
            uncanny.setAttribute('aria-pressed', 'true');
            uncanny.textContent = 'Reaction Declared';

            announce(
                'Uncanny Dodge declared. Resolve the qualifying '
                + 'attack at the table, then start a new turn '
                + 'when the Rogue’s reaction is available again.'
            );
        });
    }

    if (newTurn instanceof HTMLButtonElement) {
        newTurn.addEventListener('click', function () {
            setSneakUsed(false);

            if (
                uncanny instanceof HTMLButtonElement
                && uncanny.hasAttribute('aria-pressed')
            ) {
                uncanny.disabled = false;
                uncanny.setAttribute(
                    'aria-pressed',
                    'false'
                );
                uncanny.textContent =
                    'Declare Uncanny Dodge';
            }

            announce(
                'New turn started. Sneak Attack is ready and '
                + 'the Rogue’s reaction record has been reset.'
            );
        });
    }
}());
