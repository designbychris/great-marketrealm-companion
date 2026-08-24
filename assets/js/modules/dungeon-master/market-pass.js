(() => {
    const buttons = document.querySelectorAll('[data-market-pass-copy]');

    buttons.forEach((button) => {
        button.addEventListener('click', async () => {
            const code = button.getAttribute('data-market-pass-copy') || '';
            if (!code || !navigator.clipboard) return;
            try {
                await navigator.clipboard.writeText(code);
                const original = button.textContent;
                button.textContent = 'Copied';
                window.setTimeout(() => { button.textContent = original; }, 1600);
            } catch (error) {
                // The visible code remains selectable when clipboard access is unavailable.
            }
        });
    });
})();
