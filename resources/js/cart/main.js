let debounceTimer = null;

document.querySelectorAll('#cart input[name=quantity]').forEach((input) => {
    input.addEventListener('input', (e) => {
        const form = e.target.closest('form');

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            form.submit();
        }, 1500);
    });
});
