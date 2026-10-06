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

    // Rating star pickers: clicking a star writes the value into its hidden input.
    document.querySelectorAll('.rating-pills').forEach(group => {
        const hidden = group.parentElement.querySelector('input[type="hidden"]');
        group.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', () => {
                const val = Number(star.getAttribute('data-rating')) || 0;
                group.querySelectorAll('.rating-star').forEach(s => {
                    s.classList.toggle('active', Number(s.getAttribute('data-rating')) <= val);
                });
                if (hidden) {
                    hidden.value = String(val);
                    hidden.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        });
    });

    // 7. Generic Confirmation Modal (replaces inline confirm())
    const confirmModal = document.getElementById('confirmModal');
    const confirmMessageEl = document.getElementById('confirmMessage');
    const confirmActionBtn = document.getElementById('confirmActionBtn');
    let pendingForm = null;
    let pendingCallback = null;

    // Unsaved-changes tracking: any "main" edit form is marked dirty on input.
    document.querySelectorAll('#crudAdminForm, #popupForm, form:has(.form-grid)').forEach(form => {
        form.dataset.dirty = '0';
        form.addEventListener('input', () => { form.dataset.dirty = '1'; });
        form.addEventListener('change', () => { form.dataset.dirty = '1'; });
        form.addEventListener('submit', () => { form.dataset.dirty = '0'; }, true);
    });

    // Inside the drawer iframe itself: warn on hard navigation while dirty.
    if (document.body.classList.contains('drawer-mode')) {
        window.addEventListener('beforeunload', (e) => {
            const dirty = document.querySelector('#crudAdminForm[data-dirty="1"], #popupForm[data-dirty="1"], form[data-dirty="1"]:has(.form-grid)');
            if (dirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

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
            if (pendingCallback) {
                const cb = pendingCallback;
                pendingCallback = null;
                cb();
            } else if (pendingForm) {
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
                pendingCallback = null;
            }
        });
    }

    // Ask for confirmation before discarding a dirty form inside the drawer.
    function requestDrawerClose(reload) {
        let dirty = false;
        try {
            const doc = drawerFrame && drawerFrame.contentDocument;
            if (doc) {
                dirty = !!doc.querySelector('form[data-dirty="1"]');
            }
        } catch (e) { /* cross-origin or about:blank */ }
        if (dirty && confirmModal) {
            confirmMessageEl.textContent = 'You have unsaved changes. Discard them?';
            pendingCallback = () => closeDrawer(reload);
            confirmModal.classList.add('active');
        } else {
            closeDrawer(reload);
        }
    }

    // Esc closes modal, drawer, or dropdown
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (confirmModal && confirmModal.classList.contains('active')) {
            confirmModal.classList.remove('active');
            pendingForm = null;
            pendingCallback = null;
        }
        if (drawer && drawer.classList.contains('active')) {
            requestDrawerClose(false);
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
            const cap = Math.floor(window.innerHeight - 160); // modal header + margins + footer breathing room
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

    if (drawerFrame) {
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
    }

    window.addEventListener('resize', () => {
        if (drawer && drawer.classList.contains('active')) {
            autosizeFormFrame();
        }
    });

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
        drawerCloseBtn.addEventListener('click', () => requestDrawerClose(false));
    }
    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', () => requestDrawerClose(false));
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
                    .catch(() => {
                        showToast('Network error while saving order. Please try again.', 'danger');
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

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatOrdinalHtml(str) {
        if (!str) return '';
        let val = String(str);
        // If already contains <sup> tags, strip any dangerous tags but preserve <sup>
        if (val.toLowerCase().includes('<sup>')) {
            return val.replace(/<(?!\/?sup\b)[^>]*>/gi, '');
        }
        // Escape HTML entities to prevent XSS
        let safe = escapeHtml(val);
        // Convert ordinal suffixes on numbers: 1st, 2nd, 3rd, 4th, 10th, 12th, etc.
        safe = safe.replace(/\b(\d+)(st|nd|rd|th)\b/gi, '$1<sup>$2</sup>');
        // Convert numbers before qualifiers: 12 Pass, 10 Pass, 12 Std
        safe = safe.replace(/\b(\d+)\s+(Pass|Std|Standard|Class|Stream)\b/gi, function(match, numStr, rest) {
            const num = parseInt(numStr, 10);
            let suffix = 'th';
            const mod100 = num % 100;
            if (mod100 < 11 || mod100 > 13) {
                const mod10 = num % 10;
                if (mod10 === 1) suffix = 'st';
                else if (mod10 === 2) suffix = 'nd';
                else if (mod10 === 3) suffix = 'rd';
            }
            return `${num}<sup>${suffix}</sup> ${rest}`;
        });
        return safe;
    }

    // 10. Real-time Live Preview Engine for Edit & Create Modals
    function initLivePreview() {
        const previewWrapper = document.querySelector('[data-live-preview]');
        const previewContainer = document.getElementById('livePreviewContainer');
        if (!previewWrapper || !previewContainer) return;

        const previewType = previewWrapper.getAttribute('data-live-preview');
        const form = previewWrapper.querySelector('form');
        if (!form) return;

        let liveImageDataUrl = null;

        // Image file reader for instantaneous image updates
        const fileInputs = form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(fileInput => {
            fileInput.addEventListener('change', () => {
                if (fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        liveImageDataUrl = e.target.result;
                        renderPreview();
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                } else {
                    liveImageDataUrl = null;
                    renderPreview();
                }
            });
        });

        function getVal(name, fallback = '') {
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return fallback;
            if (el.type === 'checkbox') return el.checked;
            return el.value !== undefined ? el.value.trim() : fallback;
        }

        function getCheckedLabels(name) {
            const checked = form.querySelectorAll(`input[name="${name}"]:checked`);
            return Array.from(checked).map(cb => {
                const label = cb.closest('label');
                return label ? label.textContent.trim() : cb.value;
            });
        }

        function resolveImgSrc(path, fallback = '../assets/logo.ico') {
            if (liveImageDataUrl) return liveImageDataUrl;
            if (!path) return fallback;
            if (path.startsWith('data:') || path.startsWith('blob:') || path.startsWith('http')) return path;
            if (path.startsWith('../') || path.startsWith('/')) return path;
            return '../' + path;
        }

        function renderPreview() {
            const currentImg = getVal('current_image');
            let html = '';

            switch (previewType) {
                case 'faculty': {
                    const name = getVal('name', 'Faculty Member Name');
                    const designation = getVal('designation', 'Designation / Role');
                    const badge = getVal('badge');
                    const featured = getVal('featured') === true;
                    const deptLabels = getCheckedLabels('depts[]');
                    const imgSrc = resolveImgSrc(currentImg, '../assets/photos/faculties/placeholder.jpg');

                    html = `
                    <div class="preview-faculty-card">
                        ${featured ? '<div class="preview-faculty-featured-tag">★ Featured</div>' : ''}
                        <img src="${escapeHtml(imgSrc)}" class="preview-faculty-avatar" alt="${escapeHtml(name)}" onerror="this.src='../assets/logo.ico'">
                        <h4 class="preview-faculty-name">${escapeHtml(name)}</h4>
                        <div class="preview-faculty-designation">${escapeHtml(designation)}</div>
                        ${badge ? `<div class="preview-faculty-badge">${escapeHtml(badge)}</div>` : ''}
                        ${deptLabels.length > 0 ? `
                        <div class="preview-faculty-depts">
                            ${deptLabels.slice(0, 4).map(d => `<span class="preview-dept-pill">${escapeHtml(d)}</span>`).join('')}
                        </div>` : ''}
                    </div>`;
                    break;
                }

                case 'course': {
                    const title = getVal('name') || getVal('title') || 'Program Title';
                    const code = (getVal('slug') || getVal('code') || 'COURSE').toUpperCase();
                    const degree = getVal('medium', 'English') + ' Medium';
                    const duration = getVal('duration', '4 Years');
                    const intake = getVal('seats_or_intake') || '8 Semesters';
                    const desc = getVal('quick_info') || getVal('description') || 'Comprehensive curriculum focused on academic excellence, hands-on labs, and industry readiness.';
                    const eligibility = getVal('eligibility') || '12th Pass';
                    const eligibilityFormatted = formatOrdinalHtml(eligibility);

                    html = `
                    <div class="preview-course-card">
                        <div class="preview-course-header">
                            <span class="preview-course-code">${escapeHtml(code)}</span>
                            <span class="preview-course-degree">${escapeHtml(degree)}</span>
                        </div>
                        <div class="preview-course-body">
                            <h4 class="preview-course-title">${escapeHtml(title)}</h4>
                            <p class="preview-course-desc">${escapeHtml(desc)}</p>
                            <div class="preview-course-meta">
                                <div class="preview-course-meta-item">
                                    <span>Duration</span>
                                    <strong>${escapeHtml(duration)}</strong>
                                </div>
                                <div class="preview-course-meta-item">
                                    <span>Semesters / Intake</span>
                                    <strong>${escapeHtml(intake)}</strong>
                                </div>
                                <div class="preview-course-meta-item full-span">
                                    <span>Eligibility Criteria</span>
                                    <strong>${eligibilityFormatted}</strong>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    break;
                }

                case 'event': {
                    const title = getVal('title', 'Event Headline');
                    const eventType = getVal('event_type', 'event');
                    const badge = getVal('badge', '15 Aug');
                    const badgeType = getVal('badge_type', 'confirmed');
                    const desc = getVal('description', 'Event highlights, campus circular information, and schedule announcement details.');

                    html = `
                    <div class="preview-event-card">
                        <div class="preview-event-top">
                            <span class="preview-event-category">${eventType === 'event' ? 'Upcoming Event' : 'Academic News'}</span>
                            <span class="preview-event-badge preview-event-badge--${escapeHtml(badgeType)}">${escapeHtml(badge)}</span>
                        </div>
                        <h4 class="preview-event-title">${escapeHtml(title)}</h4>
                        <p class="preview-event-desc">${escapeHtml(desc)}</p>
                    </div>`;
                    break;
                }

                case 'ranker': {
                    const name = getVal('name', 'Student Ranker Name');
                    let rankRaw = getVal('rank_text') || getVal('rank') || 'BKNMU Rank 1^st';
                    // Format ^st, ^nd, ^rd, ^th as <sup>
                    rankRaw = escapeHtml(rankRaw).replace(/\^([a-zA-Z]+)/g, '<sup>$1</sup>');
                    const course = getVal('course', 'B.C.A.');
                    const sem = getVal('semester') ? ('Sem-' + getVal('semester')) : '';
                    const lang = getVal('language');
                    const subInfo = [course, sem, lang ? ('(' + lang + ')') : ''].filter(Boolean).join(' • ');
                    const imgSrc = resolveImgSrc(currentImg, '../assets/logo.ico');

                    html = `
                    <div class="preview-ranker-card">
                        <img src="${escapeHtml(imgSrc)}" class="preview-ranker-avatar" alt="${escapeHtml(name)}" onerror="this.src='../assets/logo.ico'">
                        <span class="preview-ranker-rank">🏆 ${rankRaw}</span>
                        <h4 class="preview-ranker-name">${escapeHtml(name)}</h4>
                        <div class="preview-ranker-course">${escapeHtml(subInfo)}</div>
                    </div>`;
                    break;
                }

                case 'testimonial': {
                    const name = getVal('name', 'Alumni / Student Name');
                    const course = getVal('course', 'B.B.A. Graduate');
                    const quote = getVal('text') || getVal('message') || 'My time at Shri V.J. Modha College provided excellent academic rigor and mentorship that prepared me for my career.';
                    const avatarText = getVal('avatar_text') || (name ? name.substring(0, 2).toUpperCase() : 'SV');
                    const stars = parseInt(getVal('stars', '5'), 10) || 5;
                    const starsStr = '★'.repeat(stars) + '☆'.repeat(Math.max(0, 5 - stars));

                    html = `
                    <div class="preview-testimonial-card">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div class="preview-testimonial-quote-icon">“</div>
                            <div style="color: #f59e0b; font-size: 14px; letter-spacing: 2px;">${escapeHtml(starsStr)}</div>
                        </div>
                        <p class="preview-testimonial-quote">${escapeHtml(quote)}</p>
                        <div class="preview-testimonial-author">
                            <div class="user-avatar" style="width: 42px; height: 42px; font-size: 13px; font-weight: 700; flex-shrink: 0;">${escapeHtml(avatarText)}</div>
                            <div>
                                <h5 class="preview-testimonial-name">${escapeHtml(name)}</h5>
                                <div class="preview-testimonial-role">${escapeHtml(course)}</div>
                            </div>
                        </div>
                    </div>`;
                    break;
                }

                case 'lab': {
                    const name = getVal('name', 'Computer & Advanced IT Lab');
                    const code = getVal('code', 'Computer Lab');
                    const badge = getVal('badge', 'IT & Computer Applications');
                    const tagline = getVal('tagline', 'High-Speed Computing, Modern IDEs & Software Innovation');
                    const desc = getVal('description', 'Equipped with high-performance workstations, gigabit network switches, and latest development tools.');
                    const imgSrc = resolveImgSrc(currentImg, '../assets/photos/gallery/labs/Computer Lab.jpg');

                    html = `
                    <div class="preview-lab-card">
                        <img src="${escapeHtml(imgSrc)}" class="preview-lab-image" alt="${escapeHtml(name)}" onerror="this.src='../assets/logo.ico'">
                        <div class="preview-lab-body">
                            <span class="preview-lab-room">${escapeHtml(code)}</span>
                            <h4 class="preview-lab-name">${escapeHtml(name)}</h4>
                            <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 6px;">${escapeHtml(badge)}</div>
                            <div style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px;">${escapeHtml(tagline)}</div>
                            <p class="preview-lab-desc">${escapeHtml(desc)}</p>
                        </div>
                    </div>`;
                    break;
                }

                case 'gallery': {
                    const title = getVal('title', 'Campus Celebration / Event');
                    const category = getVal('category_label') || getVal('category', 'Campus Events');
                    const desc = getVal('description', 'Annual cultural festival celebration at Shri V.J. Modha College campus.');
                    const imgSrc = resolveImgSrc(currentImg, '../assets/photos/gallery/campus/A Building.jpg');

                    html = `
                    <div class="preview-gallery-card">
                        <img src="${escapeHtml(imgSrc)}" class="preview-gallery-img" alt="${escapeHtml(title)}" onerror="this.src='../assets/logo.ico'">
                        <div class="preview-gallery-overlay">
                            <span class="preview-gallery-category">${escapeHtml(category)}</span>
                            <h5 class="preview-gallery-title">${escapeHtml(title)}</h5>
                            ${desc ? `<p style="font-size: 11px; color: #94a3b8; margin: 4px 0 0;">${escapeHtml(desc)}</p>` : ''}
                        </div>
                    </div>`;
                    break;
                }

                case 'magazine': {
                    const title = getVal('title', 'College Annual E-Magazine');
                    const edition = getVal('edition', 'Edition 2025');
                    const year = getVal('year') || getVal('academic_year') || '2025';
                    const theme = getVal('theme');
                    const badge = getVal('badge', 'Latest Edition');
                    const imgSrc = resolveImgSrc(currentImg, '../assets/photos/gallery/campus/A Building.jpg');

                    html = `
                    <div class="preview-mag-card">
                        <img src="${escapeHtml(imgSrc)}" class="preview-mag-cover" alt="${escapeHtml(title)}" onerror="this.src='../assets/logo.ico'">
                        <div style="display: inline-block; font-size: 10.5px; font-weight: 700; color: var(--primary-light); background: rgba(225, 29, 72, 0.15); padding: 2px 8px; border-radius: 999px; margin-bottom: 6px;">${escapeHtml(badge)}</div>
                        <h5 class="preview-mag-title">${escapeHtml(title)}</h5>
                        ${theme ? `<div style="font-size: 11.5px; font-style: italic; color: #cbd5e1; margin-bottom: 6px;">${escapeHtml(theme)}</div>` : ''}
                        <div class="preview-mag-meta">${escapeHtml(edition)} • ${escapeHtml(year)}</div>
                    </div>`;
                    break;
                }

                case 'scholarship': {
                    const title = getVal('title', 'Merit Scholarship Program');
                    const provider = getVal('provider', 'State Govt. / Trust');
                    const amount = getVal('amount', 'Up to 100% Tuition');
                    const eligibility = getVal('eligibility', 'Meritorious students securing > 80% in qualifying university examinations.');

                    html = `
                    <div class="preview-course-card">
                        <div class="preview-course-header">
                            <span class="preview-course-code">AID / GRANT</span>
                            <span class="preview-course-degree">${escapeHtml(provider)}</span>
                        </div>
                        <div class="preview-course-body">
                            <h4 class="preview-course-title">${escapeHtml(title)}</h4>
                            <div style="font-size: 13.5px; font-weight: 700; color: #34d399; margin-bottom: 8px;">${escapeHtml(amount)}</div>
                            <p class="preview-course-desc">${escapeHtml(eligibility)}</p>
                        </div>
                    </div>`;
                    break;
                }

                default: {
                    const anyTitle = getVal('title') || getVal('name') || 'Item Preview';
                    const anyDesc = getVal('description') || getVal('content') || getVal('message') || 'Fill in the form to see updates in real time.';
                    html = `
                    <div class="preview-event-card">
                        <h4 class="preview-event-title">${escapeHtml(anyTitle)}</h4>
                        <p class="preview-event-desc">${escapeHtml(anyDesc)}</p>
                    </div>`;
                }
            }

            previewContainer.innerHTML = html;
        }

        form.addEventListener('input', renderPreview);
        form.addEventListener('change', renderPreview);
        renderPreview();
    }

    // 11. Interactive Duration & Semester Builder + Eligibility Ordinal Live Sync
    function initDurationSemesterBuilder() {
        const builder = document.getElementById('durationSemesterBuilder');
        const eligibilityInput = document.getElementById('eligibility');
        const eligibilityPreview = document.getElementById('eligibilityOrdinalPreview');

        // Ordinal preview live update for Eligibility Criteria input
        if (eligibilityInput && eligibilityPreview) {
            const updateOrdinalHelper = () => {
                const val = eligibilityInput.value.trim() || '12th Pass';
                eligibilityPreview.innerHTML = `Rendered: <strong>${formatOrdinalHtml(val)}</strong>`;
            };
            eligibilityInput.addEventListener('input', updateOrdinalHelper);
            eligibilityInput.addEventListener('change', updateOrdinalHelper);
            updateOrdinalHelper();
        }

        if (!builder) return;

        const durationInput = document.getElementById('duration');
        const semestersInput = document.getElementById('seats_or_intake');
        const badge = document.getElementById('dsbSummaryBadge');
        const yearBtns = builder.querySelectorAll('.dsb-year-btn');
        const patternBtns = builder.querySelectorAll('.dsb-pattern-btn');

        if (!durationInput || !semestersInput) return;

        function getActiveYear() {
            const activeBtn = builder.querySelector('.dsb-year-btn.active');
            if (activeBtn) return parseInt(activeBtn.dataset.years, 10);
            const m = durationInput.value.match(/(\d+)/);
            return m ? parseInt(m[1], 10) : 4;
        }

        function getActivePattern() {
            const activeBtn = builder.querySelector('.dsb-pattern-btn.active');
            return activeBtn ? activeBtn.dataset.pattern : 'semester';
        }

        function updateSummary() {
            const dur = durationInput.value.trim();
            const sem = semestersInput.value.trim();
            if (badge) {
                badge.textContent = `${dur || '—'} • ${sem || '—'}`;
            }
        }

        function syncBuilder(years, pattern) {
            // Update duration input
            durationInput.value = years === 1 ? '1 Year' : `${years} Years`;

            // Update semesters/pattern input
            if (pattern === 'semester') {
                const totalSems = years * 2;
                semestersInput.value = `${totalSems} Semesters`;
            } else {
                semestersInput.value = `Yearly (${years} ${years === 1 ? 'Year' : 'Years'})`;
            }

            updateSummary();

            // Trigger input and change events so the live preview updates in real-time
            durationInput.dispatchEvent(new Event('input', { bubbles: true }));
            semestersInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        yearBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                yearBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const years = parseInt(btn.dataset.years, 10);
                const pattern = getActivePattern();
                syncBuilder(years, pattern);
            });
        });

        patternBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                patternBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const pattern = btn.dataset.pattern;
                const years = getActiveYear();
                syncBuilder(years, pattern);
            });
        });

        // When typing directly into inputs, keep summary and buttons synced
        durationInput.addEventListener('input', () => {
            updateSummary();
            const m = durationInput.value.match(/(\d+)/);
            if (m) {
                const y = parseInt(m[1], 10);
                yearBtns.forEach(b => {
                    b.classList.toggle('active', parseInt(b.dataset.years, 10) === y);
                });
            }
        });

        semestersInput.addEventListener('input', () => {
            updateSummary();
            const val = semestersInput.value.toLowerCase();
            if (val.includes('year') || val.includes('annual')) {
                patternBtns.forEach(b => b.classList.toggle('active', b.dataset.pattern === 'yearly'));
            } else if (val.includes('sem')) {
                patternBtns.forEach(b => b.classList.toggle('active', b.dataset.pattern === 'semester'));
            }
        });

        updateSummary();
    }

    initLivePreview();
    initDurationSemesterBuilder();
});

// Global Confirm Delete Helper (legacy, kept for safety)
function confirmAction(message, form) {
    if (confirm(message || 'Are you sure you want to delete this item?')) {
        form.submit();
    }
}