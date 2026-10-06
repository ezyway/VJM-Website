<?php
/**
 * Announcement Popup Manager
 * Uses the standard admin UI components, table structure, and modal drawer flow.
 * Popups are stored in site_settings under keys like announcement_popup_N.
 * The active popup is synced to the "announcement_popup" key for the public website.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/ui.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// ------------------------------------------------------------------ POST handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('popup_manager.php');
    $action = $_POST['action'] ?? '';

    // ---- Save / Update Popup ----
    if ($action === 'save_popup') {
        $popupId = isset($_POST['popup_id']) && ctype_digit((string)$_POST['popup_id'])
            ? (int)$_POST['popup_id'] : null;

        $enabled = !empty($_POST['enabled']) ? 1 : 0;
        $setAsHomepage = !empty($_POST['set_as_homepage']);
        $removeImage = !empty($_POST['remove_image']);
        $image = trim($_POST['current_image'] ?? '');

        if (!$removeImage && !empty($_FILES['image']['name'])) {
            $upload = handleAdminUpload($_FILES['image'], 'announcements', ['jpg', 'jpeg', 'png', 'webp']);
            if ($upload['success']) {
                $image = $upload['path'];
            } else {
                setFlash('danger', 'Popup image upload failed: ' . $upload['error']);
                crudRedirect('popup_manager.php', $isDrawerMode);
            }
        }
        if ($removeImage) $image = '';

        $badge      = trim($_POST['badge'] ?? '');
        $title      = trim($_POST['title'] ?? '');
        $message    = trim($_POST['message'] ?? '');
        $popupName  = trim($_POST['popup_name'] ?? '');

        $labels  = $_POST['btn_label'] ?? [];
        $urls    = $_POST['btn_url'] ?? [];
        $styles  = $_POST['btn_style'] ?? [];
        $buttons = [];
        foreach ($labels as $i => $label) {
            $label = trim($label ?? '');
            $url   = trim($urls[$i] ?? '');
            if ($label === '' && $url === '') continue;
            $style = in_array(($styles[$i] ?? ''), ['primary', 'secondary', 'outline'], true)
                ? $styles[$i] : 'primary';
            $buttons[] = ['label' => $label, 'url' => $url, 'style' => $style];
        }

        $popup = [
            'enabled'    => $enabled,
            'image'      => $image,
            'badge'      => $badge,
            'title'      => $title,
            'message'    => $message,
            'popup_name' => $popupName,
            'buttons'    => $buttons,
        ];

        // Allocate or update slot ID
        if ($popupId && $popupId > 0) {
            $savedId = $popupId;
        } else {
            $savedId = 1;
            while (getSetting("announcement_popup_{$savedId}", '') !== '') {
                $savedId++;
            }
        }

        $jsonPayload = json_encode($popup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        setSetting("announcement_popup_{$savedId}", $jsonPayload);

        // Check if this should be the active popup on the homepage
        $currentActiveId = (int)getSetting('announcement_popup_active_id', '1');
        if ($setAsHomepage || $currentActiveId === $savedId) {
            setSetting('announcement_popup_active_id', (string)$savedId);
            setSetting('announcement_popup', $jsonPayload);
        }

        logAdminActivity('save', 'popup', $savedId, "Saved Announcement Popup #{$savedId}" . (!empty($popupName) ? " ({$popupName})" : ''));
        setFlash('success', "Announcement Popup #{$savedId} saved successfully.");
        crudRedirect('popup_manager.php', $isDrawerMode);
    }

    // ---- Delete Popup ----
    if ($action === 'delete_popup' || $action === 'delete') {
        $popupId = isset($_POST['id']) && ctype_digit((string)$_POST['id'])
            ? (int)$_POST['id'] : 0;

        if ($popupId > 0) {
            $stmt = $db->prepare('DELETE FROM site_settings WHERE "key" = :k');
            $stmt->execute([':k' => "announcement_popup_{$popupId}"]);

            $activeId = (int)getSetting('announcement_popup_active_id', '1');
            if ($activeId === $popupId) {
                // Find next available popup or reset
                $next = $db->query('SELECT "key", "value" FROM site_settings WHERE "key" LIKE \'announcement_popup_%\' ORDER BY "key" ASC LIMIT 1')->fetch();
                if ($next) {
                    $nextId = (int)str_replace('announcement_popup_', '', $next['key']);
                    setSetting('announcement_popup_active_id', (string)$nextId);
                    setSetting('announcement_popup', $next['value']);
                } else {
                    setSetting('announcement_popup_active_id', '0');
                    setSetting('announcement_popup', json_encode(['enabled' => 0]));
                }
            }
            logAdminActivity('delete', 'popup', $popupId, "Deleted Announcement Popup #{$popupId}");
            setFlash('success', "Popup #{$popupId} deleted.");
        } else {
            setFlash('danger', 'Invalid popup ID.');
        }
        crudRedirect('popup_manager.php', $isDrawerMode);
    }

    // ---- Duplicate Popup ----
    if ($action === 'duplicate_popup') {
        $popupId = isset($_POST['id']) && ctype_digit((string)$_POST['id'])
            ? (int)$_POST['id'] : 0;

        if ($popupId > 0) {
            $source = getSetting("announcement_popup_{$popupId}", '');
            $data = json_decode($source, true);
            if (is_array($data) && !empty($data)) {
                $newId = $popupId + 1;
                while (getSetting("announcement_popup_{$newId}", '') !== '') {
                    $newId++;
                }
                if (!empty($data['popup_name'])) {
                    $data['popup_name'] .= ' (Copy)';
                }
                $data['enabled'] = 0; // default duplicates to disabled for safety
                setSetting("announcement_popup_{$newId}", json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                logAdminActivity('duplicate', 'popup', $newId, "Duplicated Popup #{$popupId} as #{$newId}");
                setFlash('success', "Popup #{$popupId} duplicated as #{$newId}.");
            } else {
                setFlash('danger', 'Could not load popup data to duplicate.');
            }
        }
        crudRedirect('popup_manager.php', $isDrawerMode);
    }

    // ---- Quick Set as Active on Homepage ----
    if ($action === 'set_active') {
        $popupId = isset($_POST['id']) && ctype_digit((string)$_POST['id'])
            ? (int)$_POST['id'] : 0;

        if ($popupId > 0) {
            $dataStr = getSetting("announcement_popup_{$popupId}", '');
            $data = json_decode($dataStr, true);
            if (is_array($data)) {
                $data['enabled'] = 1;
                $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                setSetting("announcement_popup_{$popupId}", $payload);
                setSetting('announcement_popup_active_id', (string)$popupId);
                setSetting('announcement_popup', $payload);
                logAdminActivity('set_active', 'popup', $popupId, "Activated Announcement Popup #{$popupId} on Homepage");
                setFlash('success', "Popup #{$popupId} is now live on the homepage.");
            } else {
                setFlash('danger', 'Could not find popup configuration.');
            }
        }
        crudRedirect('popup_manager.php', $isDrawerMode);
    }
}

// ------------------------------------------------------------------ Load Popups
$popups = [];
$defaultRaw = getSetting('announcement_popup', '');
$defaultPopup = json_decode($defaultRaw, true);
if (!is_array($defaultPopup)) $defaultPopup = [];

// Query all numbered popup entries from site_settings
$stmt = $db->query('SELECT "key", "value" FROM site_settings WHERE "key" LIKE \'announcement_popup_%\'');
$rows = $stmt->fetchAll();
foreach ($rows as $row) {
    $id = (int)str_replace('announcement_popup_', '', $row['key']);
    if ($id > 0) {
        $decoded = json_decode($row['value'], true);
        if (is_array($decoded)) {
            $popups[$id] = $decoded;
        }
    }
}
ksort($popups);

// If no numbered popups exist yet but a default announcement_popup exists, seed slot #1
if (empty($popups) && !empty($defaultPopup)) {
    $popups[1] = $defaultPopup;
    setSetting('announcement_popup_1', json_encode($defaultPopup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    setSetting('announcement_popup_active_id', '1');
}

$activeHomepageId = (int)getSetting('announcement_popup_active_id', '1');
if ($activeHomepageId === 0 && !empty($popups)) {
    $activeHomepageId = (int)array_key_first($popups);
}

// Check edit target
$editItem = null;
$editId = 0;
$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';

if (isset($_GET['edit'])) {
    if ($_GET['edit'] === 'new') {
        $isCreate = true;
    } elseif (ctype_digit((string)$_GET['edit'])) {
        $editId = (int)$_GET['edit'];
        $editItem = $popups[$editId] ?? null;
    }
}

$pageTitle = 'Popup Manager';
require_once __DIR__ . '/includes/header.php';

// Form field preparation when editing or creating
if ($editItem || $isCreate) {
    $itemEnabled   = $editItem ? !empty($editItem['enabled']) : true;
    $itemIsLive    = ($editId > 0 && $editId === $activeHomepageId) || $isCreate;
    $itemName      = $editItem['popup_name'] ?? '';
    $itemBadge     = $editItem['badge'] ?? '';
    $itemTitle     = $editItem['title'] ?? '';
    $itemMessage   = $editItem['message'] ?? '';
    $itemImage     = $editItem['image'] ?? '';
    $itemButtons   = (isset($editItem['buttons']) && is_array($editItem['buttons']))
        ? $editItem['buttons'] : [];
    if (empty($itemButtons)) {
        $itemButtons = [['label' => '', 'url' => '', 'style' => 'primary']];
    }
}
?>

<?php if ($editItem || $isCreate): ?>
<!-- Edit / Create Form Panel (Renders standalone or inside Modal Drawer) -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <?= $editItem ? 'Edit Popup #' . (int)$editId . ': ' . htmlspecialchars($itemName ?: ($itemTitle ?: 'Untitled')) : 'Create New Announcement Popup' ?>
        </div>
        <a href="popup_manager.php<?= $drawerParam ?>" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>

    <div class="panel-body">
        <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'popup_manager.php') ?>" enctype="multipart/form-data" id="popupForm">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save_popup">
            <input type="hidden" name="popup_id" value="<?= $editId > 0 ? (int)$editId : 0 ?>">
            <input type="hidden" name="current_image" id="currentImageField" value="<?= htmlspecialchars($itemImage) ?>">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px; background: var(--bg-input); padding: 14px 18px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <label class="form-switch">
                    <input type="checkbox" name="enabled" value="1" id="popupEnabled" <?= $itemEnabled ? 'checked' : '' ?>>
                    <span class="switch-label"><strong>Enable this popup</strong> (Mark active)</span>
                </label>
                <label class="form-switch">
                    <input type="checkbox" name="set_as_homepage" value="1" id="popupSetHomepage" <?= $itemIsLive ? 'checked' : '' ?>>
                    <span class="switch-label"><strong style="color: var(--primary);">Set as live homepage announcement</strong></span>
                </label>
            </div>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label" for="popup_name">Popup Internal Reference Name</label>
                    <input type="text" id="popup_name" name="popup_name" class="form-control" 
                           placeholder="e.g. Admission 2025-26 Announcement, Sports Day Alert" 
                           value="<?= htmlspecialchars($itemName) ?>">
                    <span class="form-hint">For your admin reference. This name appears in the list table.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="popup_badge">Eyebrow / Badge Text</label>
                    <input type="text" id="popup_badge" name="badge" class="form-control" 
                           placeholder="e.g. Admissions Open 2025-26" 
                           value="<?= htmlspecialchars($itemBadge) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="popup_title">Headline / Title *</label>
                    <input type="text" id="popup_title" name="title" class="form-control" required 
                           placeholder="e.g. Admissions Are Now Open for AY 2025-26" 
                           value="<?= htmlspecialchars($itemTitle) ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="popup_message">Message Body</label>
                    <textarea id="popup_message" name="message" class="form-control" rows="3" 
                              placeholder="e.g. Applications for BCA, B.Sc., BBA and all postgraduate programs are now open. Limited seats — early applicants get priority."><?= htmlspecialchars($itemMessage) ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Banner Image (Optional Header Graphic)</label>
                    <input type="file" name="image" class="form-control image-preview-input" 
                           data-preview-target="popupImagePreview" accept="image/jpeg,image/png,image/webp" id="imageUpload">
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                        <img id="popupImagePreview" class="preview-thumbnail" 
                             src="<?= $itemImage !== '' ? '../' . htmlspecialchars($itemImage) : '' ?>" 
                             alt="Popup banner" loading="lazy"
                             style="width: 140px; height: 60px; object-fit: cover; border-radius: var(--radius-md); <?= $itemImage === '' ? 'display: none;' : '' ?>">
                        <?php if ($itemImage !== ''): ?>
                            <label style="display: inline-flex; align-items: center; gap: 7px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" name="remove_image" value="1" id="removeImageCheck"> Remove current banner
                            </label>
                        <?php else: ?>
                            <span class="form-hint">Upload JPG, PNG or WebP. Recommended ~1600 × 430 px.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Action Buttons</label>
                    <div class="popup-btn-list" id="popupBtnRows">
                        <?php foreach ($itemButtons as $btn): ?>
                            <?php $bStyle = in_array(($btn['style'] ?? ''), ['primary', 'secondary', 'outline'], true) ? $btn['style'] : 'primary'; ?>
                            <div class="popup-btn-row">
                                <input type="text" name="btn_label[]" class="form-control" 
                                       placeholder="Button label (e.g. Apply Now)" 
                                       value="<?= htmlspecialchars($btn['label'] ?? '') ?>">
                                <input type="text" name="btn_url[]" class="form-control" 
                                       placeholder="https://example.com or /courses.php" 
                                       value="<?= htmlspecialchars($btn['url'] ?? '') ?>">
                                <select name="btn_style[]" class="form-control">
                                    <option value="primary" <?= $bStyle === 'primary' ? 'selected' : '' ?>>Primary (Teal)</option>
                                    <option value="secondary" <?= $bStyle === 'secondary' ? 'selected' : '' ?>>Secondary (Dark)</option>
                                    <option value="outline" <?= $bStyle === 'outline' ? 'selected' : '' ?>>Outline (Teal)</option>
                                </select>
                                <button type="button" class="btn btn-danger btn-icon" 
                                        title="Remove button" aria-label="Remove button" 
                                        data-popup-btn-remove>
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" id="popupBtnAdd" style="margin-top: 10px;">
                        <?= icon('plus', 14) ?> Add Another Button
                    </button>
                </div>
            </div>

            <!-- Integrated Live Preview -->
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div style="font-size: 13.5px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        Live Visual Preview
                    </div>
                    <span class="form-hint">Updates in real time as you edit</span>
                </div>

                <div id="popupPreviewContainer" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; min-height: 300px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                    <style>
                        .vjm-pop-preview-container{position:relative;display:flex;align-items:center;justify-content:center;padding:10px;margin:0 auto;width:100%;max-width:440px}
                    </style>
                    <div id="previewContent" style="width: 100%;"></div>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px; align-items: center;">
                <button type="submit" class="btn btn-primary">Save Popup</button>
                <a href="popup_manager.php<?= $drawerParam ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('popupForm');
    var previewContent = document.getElementById('previewContent');
    var btnRows = document.getElementById('popupBtnRows');
    var addBtn = document.getElementById('popupBtnAdd');
    var imageInput = document.getElementById('imageUpload');
    var imagePreview = document.getElementById('popupImagePreview');
    var removeImageCheckbox = document.getElementById('removeImageCheck');
    var currentImageField = document.getElementById('currentImageField');

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function createButtonRow(label, url, style) {
        label = label || '';
        url = url || '';
        style = style || 'primary';
        return '<div class="popup-btn-row">'
            + '<input type="text" name="btn_label[]" class="form-control" placeholder="Button label (e.g. Apply Now)" value="' + escapeHtml(label) + '">'
            + '<input type="text" name="btn_url[]" class="form-control" placeholder="https://example.com or /courses.php" value="' + escapeHtml(url) + '">'
            + '<select name="btn_style[]" class="form-control">'
            + '<option value="primary" ' + (style === 'primary' ? 'selected' : '') + '>Primary (Teal)</option>'
            + '<option value="secondary" ' + (style === 'secondary' ? 'selected' : '') + '>Secondary (Dark)</option>'
            + '<option value="outline" ' + (style === 'outline' ? 'selected' : '') + '>Outline (Teal)</option>'
            + '</select>'
            + '<button type="button" class="btn btn-danger btn-icon" title="Remove button" aria-label="Remove button" data-popup-btn-remove>'
            + '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
            + '</button>'
            + '</div>';
    }

    if (addBtn) {
        addBtn.addEventListener('click', function() {
            btnRows.insertAdjacentHTML('beforeend', createButtonRow());
            updatePreview();
        });
    }

    if (btnRows) {
        btnRows.addEventListener('click', function(e) {
            var rm = e.target.closest('[data-popup-btn-remove]');
            if (!rm) return;
            var row = rm.closest('.popup-btn-row');
            if (row) {
                if (btnRows.children.length > 1) {
                    row.remove();
                } else {
                    row.querySelectorAll('input').forEach(function(inp) { inp.value = ''; });
                }
                updatePreview();
            }
        });
    }

    function getFormData() {
        var data = {
            enabled: document.getElementById('popupEnabled') ? document.getElementById('popupEnabled').checked : true,
            image: (currentImageField && currentImageField.value) ? currentImageField.value : '',
            badge: document.getElementById('popup_badge') ? document.getElementById('popup_badge').value.trim() : '',
            title: document.getElementById('popup_title') ? document.getElementById('popup_title').value.trim() : '',
            message: document.getElementById('popup_message') ? document.getElementById('popup_message').value.trim() : '',
            buttons: []
        };

        if (btnRows) {
            btnRows.querySelectorAll('.popup-btn-row').forEach(function(row) {
                var lInput = row.querySelector('input[name="btn_label[]"]');
                var uInput = row.querySelector('input[name="btn_url[]"]');
                var sInput = row.querySelector('select[name="btn_style[]"]');
                var l = lInput ? lInput.value.trim() : '';
                var u = uInput ? uInput.value.trim() : '';
                var s = sInput ? sInput.value : 'primary';
                if (l || u) {
                    data.buttons.push({ label: l, url: u, style: s });
                }
            });
        }

        if (removeImageCheckbox && removeImageCheckbox.checked) {
            data.image = '';
        }

        return data;
    }

    function updatePreview() {
        if (!previewContent) return;
        var data = getFormData();
        var hasContent = data.image || data.badge || data.title || data.message || data.buttons.length > 0;

        if (!hasContent) {
            previewContent.innerHTML = '<div style="text-align: center; color: var(--text-muted); font-size: 13px; padding: 20px;">'
                + '<p>Start filling in the form to see a live preview of the popup card.</p>'
                + '</div>';
            return;
        }

        var html = '<div class="vjm-pop-preview-container"><div class="vjm-pop-card">';

        if (data.image) {
            var rawImg = data.image;
            var src = (rawImg.startsWith('data:') || rawImg.startsWith('http://') || rawImg.startsWith('https://'))
                ? rawImg
                : ('../' + escapeHtml(rawImg.replace(/^\/+/, '')));
            html += '<img class="vjm-pop-media" src="' + src + '" alt="" loading="lazy" onerror="this.style.display=\'none\'">';
        }

        html += '<button class="vjm-pop-close" type="button" aria-label="Close"></button>';
        html += '<div class="vjm-pop-body">';

        if (data.badge) {
            html += '<span class="vjm-pop-badge">' + escapeHtml(data.badge) + '</span>';
        }

        if (data.title) {
            html += '<h2 class="vjm-pop-title">' + escapeHtml(data.title) + '</h2>';
        }

        if (data.message) {
            html += '<p class="vjm-pop-message">' + escapeHtml(data.message).replace(/\n/g, '<br>') + '</p>';
        }

        if (data.buttons.length > 0) {
            html += '<div class="vjm-pop-actions">';
            data.buttons.forEach(function(b) {
                var btnStyle = b.style || 'primary';
                html += '<a href="' + (escapeHtml(b.url) || '#') + '" class="vjm-pop-btn vjm-pop-btn--' + escapeHtml(btnStyle) + '">' + escapeHtml(b.label || 'Learn More') + '</a>';
            });
            html += '</div>';
        }

        html += '</div></div></div>';
        previewContent.innerHTML = html;
    }

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            var file = e.target.files && e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(evt) {
                    if (currentImageField) currentImageField.value = evt.target.result;
                    if (imagePreview) {
                        imagePreview.src = evt.target.result;
                        imagePreview.style.display = 'block';
                    }
                    if (removeImageCheckbox) removeImageCheckbox.checked = false;
                    updatePreview();
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeImageCheckbox) {
        removeImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                if (imagePreview) imagePreview.style.display = 'none';
            } else {
                if (imagePreview && imagePreview.src) imagePreview.style.display = 'block';
            }
            updatePreview();
        });
    }

    if (form) {
        form.addEventListener('input', updatePreview);
        form.addEventListener('change', updatePreview);
    }

    updatePreview();
})();
</script>
<?php endif; ?>

<?php
// In drawer mode (inside modal iframe), we only render the create/edit form
if ($isDrawerMode) {
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<?php
// Calculate statistics
$totalPopups = count($popups);
$activePopupsCount = 0;
foreach ($popups as $p) {
    if (!empty($p['enabled'])) $activePopupsCount++;
}
$livePopupTitle = 'None';
if (isset($popups[$activeHomepageId])) {
    $lp = $popups[$activeHomepageId];
    $livePopupTitle = !empty($lp['popup_name']) ? $lp['popup_name'] : (!empty($lp['title']) ? $lp['title'] : "Popup #{$activeHomepageId}");
}
?>

<!-- Statistics Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Popups</div>
            <div class="stat-value"><?= $totalPopups ?></div>
        </div>
        <div class="stat-icon info">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Live Homepage Popup</div>
            <div class="stat-value" style="font-size: 16px; font-weight: 600; color: var(--primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                #<?= $activeHomepageId ?> &bull; <?= htmlspecialchars($livePopupTitle) ?>
            </div>
        </div>
        <div class="stat-icon success">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Enabled Status</div>
            <div class="stat-value"><?= $activePopupsCount ?> <span style="font-size: 14px; font-weight: 500; color: var(--text-muted);">/ <?= $totalPopups ?> Active</span></div>
        </div>
        <div class="stat-icon primary">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>
</div>

<?php
$popupRows = [];
foreach ($popups as $pid => $p) {
    $popupRows[] = array_merge(['id' => $pid], $p);
}
crudListPanel([
    'page'              => 'popup_manager.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Announcement Popups (' . count($popupRows) . ')',
    'rows'              => $popupRows,
    'tableId'           => 'popupsTable',
    'searchPlaceholder' => 'Search popups by title, badge, message...',
    'emptyText'         => 'No announcement popups configured yet. Click <strong>Add New Popup</strong> to create one.',
    'add'               => ['url' => 'popup_manager.php?action=create', 'drawerTitle' => 'Create Announcement Popup', 'label' => 'Add New Popup'],
    'columns'           => [
        ['th' => 'Slot', 'td' => fn($r) => '<span class="badge badge-secondary order-badge">#' . (int)$r['id'] . '</span>'],
        ['th' => 'Popup Name', 'td' => function ($r) use ($activeHomepageId) {
            $label = !empty($r['popup_name']) ? htmlspecialchars($r['popup_name']) : (!empty($r['title']) ? htmlspecialchars($r['title']) : 'Popup #' . (int)$r['id']);
            $live = ((int)$r['id'] === $activeHomepageId) ? ' <span class="badge badge-primary" title="Currently active on public website homepage">Homepage Live</span>' : '';
            return '<div style="font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 8px;"><span>' . $label . '</span>' . $live . '</div>';
        }],
        ['th' => 'Status', 'td' => fn($r) => !empty($r['enabled']) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Disabled</span>'],
        ['th' => 'Headline &amp; Eyebrow', 'td' => function ($r) {
            $t = !empty($r['title']) ? htmlspecialchars($r['title']) : '<span style="color: var(--text-muted); font-weight: 400;">(No headline)</span>';
            $b = !empty($r['badge']) ? '<div style="margin-top: 3px;"><span class="badge badge-info" style="font-size: 10px;">' . htmlspecialchars($r['badge']) . '</span></div>' : '';
            return '<div style="font-size: 13.5px; font-weight: 600; color: var(--text-main);">' . $t . '</div>' . $b;
        }],
        ['th' => 'Message Preview', 'td' => fn($r) => '<div style="color: var(--text-muted); font-size: 12.5px; max-width: 260px; line-height: 1.4;">' . (!empty($r['message']) ? htmlspecialchars(mb_strimwidth($r['message'], 0, 75, '…')) : '<span style="color: var(--text-muted); font-style: italic;">No message body</span>') . '</div>'],
        ['th' => 'Banner', 'td' => fn($r) => !empty($r['image']) ? '<img src="../' . htmlspecialchars($r['image']) . '" alt="Banner" loading="lazy" style="width: 48px; height: 28px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);" onerror="this.style.display=\'none\'">' : '<span style="color: var(--text-muted); font-size: 12px;">—</span>'],
        ['th' => 'Buttons', 'td' => function ($r) {
            $n = (isset($r['buttons']) && is_array($r['buttons'])) ? count($r['buttons']) : 0;
            return $n > 0 ? '<span class="badge badge-secondary">' . $n . ' btn' . ($n > 1 ? 's' : '') . '</span>' : '<span style="color: var(--text-muted); font-size: 12px;">None</span>';
        }],
    ],
    'editUrl'           => fn($r) => 'popup_manager.php?edit=' . (int)$r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Popup'], 'delete' => true],
    'deleteConfirm'     => fn($r) => 'Delete Popup #' . (int)$r['id'] . '? This cannot be undone.',
    'formPrefix'        => 'popup',
    'extraButtons'      => function ($r) use ($activeHomepageId, $drawerParam) {
        $btns = [];
        if ((int)$r['id'] !== $activeHomepageId) {
            $btns[] = '<form method="POST" action="popup_manager.php' . $drawerParam . '" style="display: inline;">' . csrfField()
                . '<input type="hidden" name="action" value="set_active">'
                . '<input type="hidden" name="id" value="' . (int)$r['id'] . '">'
                . '<button type="submit" class="btn btn-secondary btn-sm" title="Publish this popup to homepage">Make Live</button></form>';
        }
        $btns[] = '<form method="POST" action="popup_manager.php' . $drawerParam . '" style="display: inline;">' . csrfField()
            . '<input type="hidden" name="action" value="duplicate_popup">'
            . '<input type="hidden" name="id" value="' . (int)$r['id'] . '">'
            . '<button type="submit" class="btn btn-secondary btn-sm btn-icon" title="Duplicate popup"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></button></form>';
        return $btns;
    },
]);
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>