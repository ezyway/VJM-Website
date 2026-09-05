/**
 * Admin Panel Interactive JavaScript
 * Shri V.J. Modha College Portal
 */

document.addEventListener('DOMContentLoaded', () => {
    const inDrawer = document.body.classList.contains('drawer-mode');

    // 1. Theme Management
    const savedTheme = localStorage.getItem('vjm_admin_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);

    const themeToggleBtn = document.getElementById('themeToggleBtn');
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
    document.querySelectorAll('.toast').forEach(toast => {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    });

    // 4. Modal Triggers
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
            const table = document.getElementById(input.getAttribute('data-table-search'));
            if (!table) return;

            const query = input.value.toLowerCase().trim();
            table.querySelectorAll('tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
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
                // Native submit: the server redirect re-renders with its own flash toast,
                // so no stashing is needed here. (Drawer saves use postMessage instead.)
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

    // Esc closes modal, drawer, or dropdown
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (confirmModal && confirmModal.classList.contains('active')) {
            confirmModal.classList.remove('active');
            pendingForm = null;
        }
        if (drawer && drawer.classList.contains('active')) {
            closeDrawer(false);
        }
    });

    // Drawer auto-close: listen for postMessage from the iframe after a save.
    // The server-side form page (in drawer mode) fires window.parent.postMessage({ type: 'admin:saved', ... })
    // after a redirect with a flash. On success we close the drawer and refresh the parent list;
    // on failure we surface the toast here and keep the drawer open for corrections.
    if (!inDrawer) {
        window.addEventListener('message', (e) => {
            const data = e.data;
            if (!data || typeof data !== 'object' || data.type !== 'admin:saved') return;

            if (data.toastType === 'success') {
                sessionStorage.setItem('vjm_admin_pending_toast', JSON.stringify({
                    type: 'success',
                    message: data.message || 'Saved successfully.'
                }));
                closeDrawer(true);
            } else {
                showToast(data.message || 'Action failed. Please check your input.', data.toastType || 'danger', 6000);
            }
        });
    }

    // 8. Centered Modal for Create/Edit forms (iframe auto-sized to the form)
    const drawerOverlay = document.getElementById('adminDrawerOverlay');
    const drawer = document.getElementById('adminDrawer');
    const drawerTitle = document.getElementById('adminDrawerTitle');
    const drawerFrame = document.getElementById('adminDrawerFrame');
    const drawerCloseBtn = document.getElementById('adminDrawerClose');
    let frameResizeObserver = null;
    let frameResizeQueued = false;

    // Size the iframe to its content, clamped so the modal never forces a
    // scroll when the form fits on screen. Scroll only appears for genuinely
    // long forms on short viewports.
    function autosizeFormFrame() {
        if (frameResizeQueued) return;
        frameResizeQueued = true;
        requestAnimationFrame(() => {
            frameResizeQueued = false;
            const doc = drawerFrame.contentDocument;
            if (!doc || !doc.documentElement) return;
            const cap = Math.floor(window.innerHeight - 120); // modal header + viewport margins
            // Collapse the frame before measuring: scrollHeight is clamped to
            // the iframe's own viewport, so measuring while tall would never
            // shrink the modal to a short form. Setting 1px first (same task,
            // never painted) makes scrollHeight reflect the true content.
            drawerFrame.style.height = '1px';
            const natural = doc.documentElement.scrollHeight;
            const h = Math.min(Math.max(natural, 240), cap);
            drawerFrame.style.height = h + 'px';
        });
    }

    function openDrawer(url, title) {
        drawerTitle.textContent = title || 'Edit';
        drawerFrame.style.height = '480px';
        drawerFrame.src = url;
        drawerOverlay.classList.add('active');
        drawer.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    drawerFrame.addEventListener('load', () => {
        autosizeFormFrame();
        const doc = drawerFrame.contentDocument;
        if (!doc || !doc.body) return;

        // Keep sizing in sync while the form inside changes (flash toasts,
        // image previews, validation messages, etc.).
        if (frameResizeObserver) frameResizeObserver.disconnect();
        frameResizeObserver = new MutationObserver(autosizeFormFrame);
        frameResizeObserver.observe(doc.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style', 'hidden']
        });

        // Move focus into the first visible field of the form.
        const firstField = doc.querySelector('form input:not([type="hidden"]), form textarea, form select');
        if (firstField && typeof firstField.focus === 'function') firstField.focus();
    });

    window.addEventListener('resize', autosizeFormFrame);

    function closeDrawer(reload = false) {
        drawerOverlay.classList.remove('active');
        drawer.classList.remove('active');
        document.body.style.overflow = '';
        if (frameResizeObserver) {
            frameResizeObserver.disconnect();
            frameResizeObserver = null;
        }
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
            openDrawer(link.getAttribute('data-drawer-url'), link.getAttribute('data-drawer-title') || 'Edit');
        });
    });

    if (drawerCloseBtn) {
        drawerCloseBtn.addEventListener('click', () => closeDrawer(false));
    }
    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', () => closeDrawer(false));
    }

    // Pending toast: if a session-stashed flash exists (set by a drawer/confirm save),
    // show it in the parent page and clear the stash.
    const pendingToast = sessionStorage.getItem('vjm_admin_pending_toast');
    if (!inDrawer && pendingToast) {
        sessionStorage.removeItem('vjm_admin_pending_toast');
        try {
            const data = JSON.parse(pendingToast);
            showToast(data.message, data.type || 'success', 6000);
        } catch (_) { /* ignore malformed stash */ }
    }

    // 9. Drag-and-Drop Row Reordering (requires SortableJS)
    if (typeof Sortable !== 'undefined') {
        document.querySelectorAll('table.admin-table[data-reorder]').forEach(table => {
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            // Ensure each row has a data-reorder-id attribute
            tbody.querySelectorAll('tr').forEach(row => {
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
                            updateOrderBadges(tbody);
                        } else {
                            showToast(data.error || 'Failed to save order', 'danger');
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
        tbody.querySelectorAll('tr').forEach((row, index) => {
            const badge = row.querySelector('.order-badge');
            if (badge) {
                badge.textContent = '#' + (index + 1);
            }
        });
    }

    // Helper to show toast messages
    function showToast(message, type = 'info', duration = 4000) {
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
        }, duration);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }
});

// Global Confirm Delete Helper (legacy, kept for safety)
function confirmAction(message, form) {
    if (confirm(message || 'Are you sure you want to delete this item?')) {
        form.submit();
    }
}