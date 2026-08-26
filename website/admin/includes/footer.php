        </main>
    </div>
</div>

<!-- Generic Confirm Modal -->
<div class="modal-overlay" id="confirmModal" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
        <div class="modal-header">
            <div class="modal-title" id="confirmTitle">Confirm Action</div>
        </div>
        <div class="modal-body">
            <p id="confirmMessage" style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">Are you sure?</p>
        </div>
        <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; padding: 16px 20px; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmActionBtn">Confirm</button>
        </div>
    </div>
</div>

<!-- Slide-over Drawer -->
<div class="drawer-overlay" id="adminDrawerOverlay" aria-hidden="true"></div>
<aside class="drawer" id="adminDrawer" aria-hidden="true">
    <div class="drawer-header">
        <div class="drawer-title" id="adminDrawerTitle">Edit</div>
        <button type="button" class="btn btn-secondary btn-icon" id="adminDrawerClose" title="Close" aria-label="Close drawer">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <iframe id="adminDrawerFrame" class="drawer-frame" title="Drawer content" src="about:blank"></iframe>
</aside>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="assets/js/admin.js?v=<?= time() ?>"></script>
</body>
</html>
