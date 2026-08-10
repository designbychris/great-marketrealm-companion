(function () {
    'use strict';
    const filter = function (select, parent) {
        if (!(select instanceof HTMLSelectElement)) { return; }
        let selectedStillVisible = select.value === '';
        Array.from(select.options).forEach(function (option) {
            if (option.value === '') { option.hidden = false; return; }
            const visible = option.dataset.parent === parent;
            option.hidden = !visible;
            option.disabled = !visible;
            if (visible && option.value === select.value) { selectedStillVisible = true; }
        });
        if (!selectedStillVisible) { select.value = ''; }
    };
    const init = function () {
        const heritage = document.querySelector('[data-catalogue-child="heritage"]');
        const subclass = document.querySelector('[data-catalogue-child="subclass"]');
        const portraitHeritage = document.querySelector('[name="portrait_heritage"]');
        const race = function () { return document.querySelector('input[name="race"]:checked')?.value || ''; };
        const characterClass = function () { return document.querySelector('input[name="class"]:checked')?.value || ''; };
        const refresh = function () { filter(heritage, race()); filter(subclass, characterClass()); };
        document.addEventListener('change', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) { return; }
            if (target.matches('input[name="race"], input[name="class"]')) { refresh(); }
            if (target === heritage && portraitHeritage instanceof HTMLInputElement) {
                portraitHeritage.value = heritage.value;
                portraitHeritage.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        refresh();
        if (heritage instanceof HTMLSelectElement && portraitHeritage instanceof HTMLInputElement && heritage.value) {
            portraitHeritage.value = heritage.value;
        }
    };
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
}());
