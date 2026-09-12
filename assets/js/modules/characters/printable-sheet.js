(function (window, document) {
    'use strict';

    const PRINT_EXCLUDED_CLASS = 'gmrc-print-excluded';
    const i18n = window.gmrcPrintableSheetI18n || {};
    const PRINT_MODE_CLASS = 'gmrc-print-mode';
    let originalTitle = document.title;
    let excludedNodes = [];
    let printPrepared = false;

    const sheet = function () {
        return document.querySelector('[data-printable-sheet]');
    };

    const suggestedTitle = function () {
        const root = sheet();
        const characterName = root ? (root.getAttribute('data-print-character-name') || '').trim() : '';
        if (characterName !== '') {
            return String(i18n.namedSheetTitle || '%s - Great MarketRealm Character Sheet')
                .replace('%s', characterName);
        }

        return i18n.sheetTitle || 'Great MarketRealm Character Sheet';
    };

    const preparePrint = function () {
        const root = sheet();
        if (!root || printPrepared) {
            return;
        }

        printPrepared = true;
        originalTitle = document.title;
        document.title = suggestedTitle();
        document.body.classList.add(PRINT_MODE_CLASS);
        excludedNodes = [];

        // Isolate the printable sheet from the WordPress/theme chrome without
        // assuming anything about the host theme's wrapper class names.
        let branch = root;
        while (branch && branch !== document.body) {
            const parent = branch.parentElement;
            if (!parent) {
                break;
            }

            Array.prototype.forEach.call(parent.children, function (sibling) {
                if (sibling === branch || sibling.classList.contains(PRINT_EXCLUDED_CLASS)) {
                    return;
                }
                sibling.classList.add(PRINT_EXCLUDED_CLASS);
                excludedNodes.push(sibling);
            });

            branch = parent;
        }
    };

    const finishPrint = function () {
        excludedNodes.forEach(function (node) {
            node.classList.remove(PRINT_EXCLUDED_CLASS);
        });
        excludedNodes = [];
        document.body.classList.remove(PRINT_MODE_CLASS);
        document.title = originalTitle;
        printPrepared = false;
    };

    const boot = function () {
        if (!sheet()) {
            return;
        }

        window.addEventListener('beforeprint', preparePrint);
        window.addEventListener('afterprint', finishPrint);

        document.querySelectorAll('[data-print-character-sheet]').forEach(function (button) {
            button.addEventListener('click', function () {
                preparePrint();
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
