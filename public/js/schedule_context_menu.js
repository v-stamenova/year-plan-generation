document.addEventListener('DOMContentLoaded', () => {
    const menu = document.getElementById('schedule-context-menu');
    const blockMenu = document.getElementById('schedule-context-menu-block');

    const dateInput = document.getElementById('blockDate');
    const multiDayDateInput = document.getElementById('multiBlockDate');

    const deleteForm = document.getElementById('deleteBlockForm');
    const deleteBlockId = document.getElementById('deleteBlockId');

    const editDialog = document.getElementById('editBlockDialog');

    function setGroup(group, active) {
        if (!group) return;
        group.hidden = !active;
        group.disabled = !active;
    }

    async function openEditDialog(blockId) {
        if (!editDialog || !blockId) return;

        let item;
        try {
            const response = await fetch(`/year-plan/item/${encodeURIComponent(blockId)}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            item = await response.json();
        } catch (error) {
            console.error('Could not load the schedule block.', error);
            return;
        }

        const field = selector => editDialog.querySelector(selector);
        const isMultiDay = Boolean(item.end_date);
        const isCourse = !isMultiDay && Boolean(item.course_id) && Boolean(field('#editCourseFields'));

        field('#editItemId').value = item.id;

        // Multi-day items use the multi-day inputs, everything else the single-day ones.
        setGroup(field('#editSingleFields'), !isMultiDay);
        setGroup(field('#editMultiFields'), isMultiDay);

        if (isMultiDay) {
            field('#editMultiName').value = item.name ?? '';
            field('#editMultiStartDate').value = item.start_date;
            field('#editMultiEndDate').value = item.end_date;
            field('#editMultiAllDay').checked = item.is_main;
            field('#editMultiColour').value = item.colour || '#3788d8';
            return;
        }

        field('#editBlockDate').value = item.start_date;
        field('#editIsOneTime').value = isCourse ? '0' : '1';

        // A course block takes its name/colour from the course, so only the course picker is shown.
        setGroup(field('#editCourseFields'), isCourse);
        setGroup(field('#editEventFields'), !isCourse);

        if (isCourse) {
            // Clicking the menu item makes the combobox select it and update its label and hidden input.
            field(`#editCourseFields .menu-item[data-bs-value="${CSS.escape(String(item.course_id))}"]`)?.click();
        } else {
            field('#editEventName').value = item.name ?? '';
            field('#editEventColour').value = item.colour || '#3788d8';
        }
    }

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

    document.addEventListener('contextmenu', event => {
        const cell = event.target.closest('.scheduled-block[data-date], .schedule-day[data-date]');
        if (!cell) return;

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

    document.addEventListener('htmx:afterRequest', event => {
        if (!event.detail.successful) return;

        event.target.closest('dialog')?.querySelector('[data-bs-dismiss="dialog"]')?.click();
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

                case 'edit':
                    openEditDialog(selectedBlockId);
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