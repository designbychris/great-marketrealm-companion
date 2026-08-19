(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-cunning-declare]');

        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        if (button.disabled) {
            return;
        }

        var scope = button.closest('[data-cunning-actions]');
        if (!(scope instanceof HTMLElement)) {
            return;
        }

        var status = scope.querySelector('[data-cunning-status]');
        var label = button.dataset.cunningLabel || 'Cunning Action';

        scope.querySelectorAll('[data-cunning-declare]').forEach(
            function (candidate) {
                candidate.removeAttribute('aria-pressed');
                candidate.classList.remove('is-selected');
            }
        );

        button.setAttribute('aria-pressed', 'true');
        button.classList.add('is-selected');

        if (status instanceof HTMLElement) {
            status.textContent =
                label
                + ' declared as this turn’s Cunning Action. '
                + 'No limited resource has been spent.';
        }
    });
}());
