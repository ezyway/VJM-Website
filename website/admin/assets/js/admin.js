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
});

// Global Confirm Delete Helper
function confirmAction(message, form) {
    if (confirm(message || 'Are you sure you want to delete this item?')) {
        form.submit();
    }
}
