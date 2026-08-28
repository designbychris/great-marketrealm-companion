(function () {
    'use strict';

    const clamp = (value, min, max) => Math.max(min, Math.min(max, Number(value)));

    document.querySelectorAll('[data-token-forge]').forEach((forge) => {
        const preview = forge.querySelector('[data-token-preview]');
        const frame = forge.querySelector('[data-token-frame]');
        const focusX = forge.querySelector('[data-token-focus-x]');
        const focusY = forge.querySelector('[data-token-focus-y]');
        const zoom = forge.querySelector('[data-token-zoom]');
        const focusXOutput = forge.querySelector('[data-token-focus-x-output]');
        const focusYOutput = forge.querySelector('[data-token-focus-y-output]');
        const zoomOutput = forge.querySelector('[data-token-zoom-output]');

        if (!preview || !frame || !focusX || !focusY || !zoom) return;

        const render = () => {
            const x = clamp(focusX.value, 0, 100);
            const y = clamp(focusY.value, 0, 100);
            const z = clamp(zoom.value, 100, 220);

            preview.style.setProperty('--gmrc-token-focus-x', `${x}%`);
            preview.style.setProperty('--gmrc-token-focus-y', `${y}%`);
            preview.style.setProperty('--gmrc-token-zoom', String(z / 100));

            Array.from(preview.classList)
                .filter((name) => name.startsWith('gmrc-token-forge__token--'))
                .forEach((name) => preview.classList.remove(name));
            preview.classList.add(`gmrc-token-forge__token--${frame.value}`);

            if (focusXOutput) focusXOutput.textContent = `${x}%`;
            if (focusYOutput) focusYOutput.textContent = `${y}%`;
            if (zoomOutput) zoomOutput.textContent = `${z}%`;

            // Keep the upload form aligned with the live design recipe.
            const upload = forge.querySelector('.gmrc-token-forge__upload');
            if (upload) {
                const values = {
                    token_frame: frame.value,
                    token_focus_x: x,
                    token_focus_y: y,
                    token_zoom: z,
                };
                Object.entries(values).forEach(([name, value]) => {
                    const input = upload.querySelector(`input[name="${name}"]`);
                    if (input) input.value = String(value);
                });
            }
        };

        [frame, focusX, focusY, zoom].forEach((control) => {
            control.addEventListener('input', render);
            control.addEventListener('change', render);
        });

        render();
    });
}());
