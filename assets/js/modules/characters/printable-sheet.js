(function (window, document) {
    'use strict';

    const boot = function () {
        document.querySelectorAll('[data-print-character-sheet]').forEach(function (button) {
            button.addEventListener('click', function () {
                window.print();
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(window, document);
