(() => {
    'use strict';
    const input = document.querySelector('[data-gmrc-spell-filter]');
    const list = document.querySelector('[data-gmrc-spell-list]');
    if (! input || ! list) return;
    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        list.querySelectorAll('[data-gmrc-spell-name]').forEach((entry) => {
            entry.hidden = query !== '' && ! entry.dataset.gmrcSpellName.includes(query);
        });
    });
})();
