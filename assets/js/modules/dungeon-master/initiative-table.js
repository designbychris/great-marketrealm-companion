(function (window, document) {
    'use strict';
    const secureD20 = function () {
        if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
            const range = 0x100000000;
            const limit = range - (range % 20);
            const values = new Uint32Array(1);
            let value = limit;
            while (value >= limit) { window.crypto.getRandomValues(values); value = values[0]; }
            return (value % 20) + 1;
        }
        return Math.floor(Math.random() * 20) + 1;
    };
    document.querySelectorAll('[data-initiative-table]').forEach(function (table) {
        const live = table.querySelector('[data-initiative-live]');
        table.querySelectorAll('[data-roll-initiative]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = button.parentElement ? button.parentElement.querySelector('input') : null;
                if (!(input instanceof HTMLInputElement)) { return; }
                const natural = secureD20();
                const modifier = Number(button.getAttribute('data-modifier') || 0);
                input.value = String(natural + modifier);
                if (live) { live.textContent = 'Initiative rolled: ' + natural + (modifier >= 0 ? ' + ' : ' - ') + Math.abs(modifier) + ' = ' + input.value + '.'; }
            });
        });
    });
}(window, document));
