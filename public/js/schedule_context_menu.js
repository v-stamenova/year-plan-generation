document.addEventListener('DOMContentLoaded', () => {
    const menu = document.getElementById('schedule-context-menu');
    const blockMenu = document.getElementById('schedule-context-menu-block');

    const dateInput = document.getElementById('blockDate');
    const multiDayDateInput = document.getElementById('multiBlockDate');

    const deleteForm = document.getElementById('deleteBlockForm');
    const deleteBlockId = document.getElementById('deleteBlockId');

    const cells = document.querySelectorAll('.schedule-day[data-date]');

    let selectedDate = null;
    let selectedBlockId = null;
    let activeMenu = null;

    function hideMenus() {
        if (menu) menu.hidden = true;
        if (blockMenu) blockMenu.hidden = true;
        activeMenu = null;
    }

    function showMenu(targetMenu, event, date, blockId = null) {
        if (!targetMenu) return;

        event.preventDefault();

        selectedDate = date;
        selectedBlockId = blockId;

        hideMenus();

        activeMenu = targetMenu;
        targetMenu.hidden = false;
        targetMenu.style.position = 'fixed';
        targetMenu.style.left = `${event.clientX}px`;
        targetMenu.style.top = `${event.clientY}px`;

        const rect = targetMenu.getBoundingClientRect();
        const margin = 8;

        if (rect.right > window.innerWidth - margin) {
            targetMenu.style.left =
                `${Math.max(margin, window.innerWidth - rect.width - margin)}px`;
        }

        if (rect.bottom > window.innerHeight - margin) {
            targetMenu.style.top =
                `${Math.max(margin, window.innerHeight - rect.height - margin)}px`;
        }
    }

    cells.forEach(cell => {
        cell.addEventListener('contextmenu', event => {
            // Get the schedule block ID if the right-click was on a block.
            const block = event.target.closest('.scheduled-block');
            const blockId = block ? block.id : null;

            const date = cell.dataset.date;
            const isBlockMenu = cell.dataset.menu === 'block';

            showMenu(
                isBlockMenu ? blockMenu : menu,
                event,
                date,
                blockId
            );
        });
    });

    [menu, blockMenu].filter(Boolean).forEach(targetMenu => {
        targetMenu.addEventListener('click', event => {
            const button = event.target.closest('[data-action]');

            if (!button || !targetMenu.contains(button)) return;

            const action = button.dataset.action;

            if (action === 'delete-block') {
                if (!selectedBlockId) {
                    console.warn('No schedule block ID selected.');
                    return;
                }

                if (deleteBlockId) {
                    deleteBlockId.value = selectedBlockId;
                }

                hideMenus();

                // Submit the form to delete the block.
                if (deleteForm) {
                    deleteForm.requestSubmit();
                }

                return;
            }

            if (!selectedDate) return;

            hideMenus();

            switch (action) {
                case 'add-single':
                    if (dateInput) {
                        dateInput.value = selectedDate;
                        dateInput.dispatchEvent(
                            new Event('change', { bubbles: true })
                        );
                    }
                    break;

                case 'add-multi':
                    if (multiDayDateInput) {
                        multiDayDateInput.value = selectedDate;
                        multiDayDateInput.dispatchEvent(
                            new Event('change', { bubbles: true })
                        );
                    }
                    break;
            }
        });
    });

    document.addEventListener('click', event => {
        if (activeMenu && !activeMenu.contains(event.target)) {
            hideMenus();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') hideMenus();
    });

    window.addEventListener('resize', hideMenus);
    window.addEventListener('scroll', hideMenus, true);
});