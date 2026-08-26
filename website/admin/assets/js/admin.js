/**
 * Admin Panel Interactive JavaScript
 * Shri V.J. Modha College Portal
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Theme Management
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const savedTheme = localStorage.getItem('vjm_admin_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('vjm_admin_theme', newTheme);
        });
    }

    // 2. Mobile Sidebar Toggle
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    if (mobileSidebarToggle && adminSidebar) {
        mobileSidebarToggle.addEventListener('click', () => {
            adminSidebar.classList.toggle('open');
        });
    }

    // 3. Auto-dismiss Flash Toasts
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    });

    // 4. Modal Triggers (existing modal system)
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-modal-target');
            const modal = document.getElementById(targetId);
            if (modal) modal.classList.add('active');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal-overlay');
            if (modal) modal.classList.remove('active');
        });
    });

    // Close modal on background click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });

    // 5. Live Image Preview
    document.querySelectorAll('.image-preview-input').forEach(input => {
        input.addEventListener('change', () => {
            const previewTargetId = input.getAttribute('data-preview-target');
            const previewEl = document.getElementById(previewTargetId);
            if (previewEl && input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewEl.src = e.target.result;
                    previewEl.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });

    // 6. Live Search Filtering for Tables
    document.querySelectorAll('[data-table-search]').forEach(input => {
        input.addEventListener('input', () => {
            const targetTableId = input.getAttribute('data-table-search');
            const table = document.getElementById(targetTableId);
            if (!table) return;

            const query = input.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // 7. Generic Confirmation Modal (replaces inline confirm())
    const confirmModal = document.getElementById('confirmModal');
    const confirmMessageEl = document.getElementById('confirmMessage');
    const confirmActionBtn = document.getElementById('confirmActionBtn');
    let pendingForm = null;

    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const message = btn.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            const formSelector = btn.getAttribute('data-confirm-form');
            if (!formSelector) return;
            const form = document.querySelector(formSelector);
            if (!form) return;
            pendingForm = form;
            confirmMessageEl.textContent = message;
            confirmModal.classList.add('active');
        });
    });

    if (confirmActionBtn) {
        confirmActionBtn.addEventListener('click', () => {
            if (pendingForm) {
                pendingForm.submit();
            }
            confirmModal.classList.remove('active');
            pendingForm = null;
        });
    }

    if (confirmModal) {
        confirmModal.addEventListener('click', (e) => {
            if (e.target === confirmModal) {
                confirmModal.classList.remove('active');
                pendingForm = null;
            }
        });
    }

    // 8. Slide-over Drawer for Create/Edit forms
    const drawerOverlay = document.getElementById('adminDrawerOverlay');
    const drawer = document.getElementById('adminDrawer');
    const drawerTitle = document.getElementById('adminDrawerTitle');
    const drawerFrame = document.getElementById('adminDrawerFrame');
    const drawerCloseBtn = document.getElementById('adminDrawerClose');

    function openDrawer(url, title) {
        drawerTitle.textContent = title || 'Edit';
        drawerFrame.src = url;
        drawerOverlay.classList.add('active');
        drawer.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer(reload = true) {
        drawerOverlay.classList.remove('active');
        drawer.classList.remove('active');
        document.body.style.overflow = '';
        // Reset iframe src to avoid caching issues
        setTimeout(() => {
            drawerFrame.src = 'about:blank';
        }, 200);
        if (reload) {
            window.location.reload();
        }
    }

    document.querySelectorAll('[data-drawer-url]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const url = link.getAttribute('data-drawer-url');
            const title = link.getAttribute('data-drawer-title') || 'Edit';
            openDrawer(url, title);
        });
    });

    if (drawerCloseBtn) {
        drawerCloseBtn.addEventListener('click', () => closeDrawer(true));
    }
    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', () => closeDrawer(true));
    }

    // 9. Drag-and-Drop Row Reordering (requires SortableJS)
    if (typeof Sortable !== 'undefined') {
        document.querySelectorAll('table.admin-table[data-reorder]').forEach(table => {
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            // Ensure each row has a data-reorder-id attribute
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                if (!row.dataset.reorderId) {
                    // Try to infer from the first cell if it's an ID badge
                    const firstCell = row.querySelector('td:first-child');
                    if (firstCell) {
                        const match = firstCell.textContent.match(/#(\d+)/);
                        if (match) row.dataset.reorderId = match[1];
                    }
                }
            });

            new Sortable(tbody, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function (evt) {
                    const order = Array.from(tbody.querySelectorAll('tr'))
                        .map(tr => tr.dataset.reorderId)
                        .filter(Boolean);
                    if (order.length === 0) return;

                    const tableName = table.getAttribute('data-reorder');
                    if (!tableName) return;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    fetch('reorder.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'csrf_token': csrfToken,
                            'table': tableName,
                            'order': JSON.stringify(order)
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Order saved', 'success');
                            // Update sort order badges if present
                            updateOrderBadges(tbody);
                        } else {
                            showToast(data.error || 'Failed to save order', 'danger');
                            // Revert visual change by reloading
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        console.error('Reorder error:', err);
                        showToast('Network error', 'danger');
                        window.location.reload();
                    });
                }
            });
        });
    }

    function updateOrderBadges(tbody) {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            const badge = row.querySelector('td:first-child .badge');
            if (badge) {
                badge.textContent = '#' + (index + 1);
            }
        });
    }

    // Helper to show toast messages
    function showToast(message, type = 'info') {
        const container = document.querySelector('.toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }
});

// Global Confirm Delete Helper (legacy)
function confirmAction(message, form) {
    if (confirm(message || 'Are you sure you want to delete this item?')) {
        form.submit();
    }
}