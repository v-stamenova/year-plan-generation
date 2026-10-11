document.addEventListener('DOMContentLoaded', () => {
    const menu = document.getElementById('schedule-context-menu');
    const cells = document.querySelectorAll('.schedule-day[data-date]');

    let selectedDate = null;

    cells.forEach(cell => {
        cell.addEventListener('contextmenu', event => {
            event.preventDefault();

            selectedDate = cell.dataset.date;

            menu.hidden = false;
            menu.style.left = `${event.clientX}px`;
            menu.style.top = `${event.clientY}px`;

            const rect = menu.getBoundingClientRect();

            if (rect.right > window.innerWidth) {
                menu.style.left = `${window.innerWidth - rect.width - 8}px`;
            }

            if (rect.bottom > window.innerHeight) {
                menu.style.top = `${window.innerHeight - rect.height - 8}px`;
            }
        });
    });

    menu.addEventListener('click', event => {
        const button = event.target.closest('[data-action]');

        if (!button || !selectedDate) return;

        const action = button.dataset.action;

        menu.hidden = true;

        switch (action) {
            case 'add-single':
                console.log('Add item on:', selectedDate);
                break;
            case 'add-multi':
                console.log('Add multi-day item starting on:', selectedDate);
                break;
        }
    });

    document.addEventListener('click', () => {
        menu.hidden = true;
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') menu.hidden = true;
    });
});