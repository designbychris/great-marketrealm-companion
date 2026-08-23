(function () {
    'use strict';
    var input = document.querySelector('[data-gmrc-calling-filter]');
    var list = document.querySelector('[data-gmrc-calling-list]');
    if (!input || !list) { return; }
    input.addEventListener('input', function () {
        var query = input.value.trim().toLowerCase();
        list.querySelectorAll('[data-gmrc-calling-name]').forEach(function (item) {
            item.hidden = query !== '' && item.getAttribute('data-gmrc-calling-name').indexOf(query) === -1;
        });
    });
}());
