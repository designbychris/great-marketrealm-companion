(() => {
    'use strict';
    const input = document.querySelector('[data-gmrc-background-filter]');
    const list = document.querySelector('[data-gmrc-background-list]');
    if (! input || ! list) return;
    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        list.querySelectorAll('[data-gmrc-background-name]').forEach((entry) => {
            entry.hidden = query !== '' && ! entry.dataset.gmrcBackgroundName.includes(query);
        });
    });
})();
