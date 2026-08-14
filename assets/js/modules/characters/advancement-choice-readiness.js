(function (window, document) {
    'use strict';

    const pluralise = function (word, count) {
        return count === 1
            ? word
            : word + 's';
    };

    const nounFor = function (form, count) {
        const kind =
            form.dataset.choiceKind
            || 'option';

        return pluralise(
            kind,
            count
        );
    };

    const checkedCount = function (form) {
        return form.querySelectorAll(
            'input[name="choice[]"]:checked'
        ).length;
    };

    const update = function (form) {
        const mode =
            form.dataset.choiceMode
            || 'single';

        if (mode === 'single') {
            return;
        }

        const minimum = Math.max(
            1,
            parseInt(
                form.dataset.choiceMinimum
                || '1',
                10
            )
        );

        const maximum = Math.max(
            minimum,
            parseInt(
                form.dataset.choiceMaximum
                || String(minimum),
                10
            )
        );

        const count = checkedCount(form);

        const ready =
            count >= minimum
            && count <= maximum;

        const button = form.querySelector(
            '[data-choice-submit]'
        );

        if (
            button instanceof HTMLButtonElement
        ) {
            button.disabled = !ready;
            button.setAttribute(
                'aria-disabled',
                ready
                    ? 'false'
                    : 'true'
            );
        }

        const status = form.querySelector(
            '[data-choice-readiness-status]'
        );

        if (!(status instanceof HTMLElement)) {
            return;
        }

        const noun = nounFor(
            form,
            minimum
        );

        if (ready) {
            status.textContent =
                count
                + ' of '
                + minimum
                + ' '
                + noun
                + ' selected — ready to record.';

            status.dataset.choiceReadiness =
                'ready';

            return;
        }

        if (count > maximum) {
            const remove = count - maximum;

            status.textContent =
                count
                + ' selected — remove '
                + remove
                + ' '
                + pluralise(
                    form.dataset.choiceKind
                    || 'option',
                    remove
                )
                + '.';

            status.dataset.choiceReadiness =
                'attention';

            return;
        }

        const remaining =
            minimum - count;

        status.textContent =
            count
            + ' of '
            + minimum
            + ' '
            + noun
            + ' selected — choose '
            + remaining
            + ' more.';

        status.dataset.choiceReadiness =
            'attention';
    };

    const initialise = function (form) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        form
            .querySelectorAll(
                'input[name="choice[]"]'
            )
            .forEach(function (input) {
                input.addEventListener(
                    'change',
                    function () {
                        update(form);
                    }
                );
            });

        /*
         * UX readiness is never the validation boundary.
         * The server still validates every submitted choice.
         */
        update(form);
    };

    const boot = function () {
        document
            .querySelectorAll(
                '[data-advancement-choice]'
            )
            .forEach(initialise);
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
