<?php
/**
 * Shared Admin UI Components
 * Pure string-returning presenters so every module renders identical markup.
 */

$GLOBALS['ui_icons'] = [
    'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
    'book'      => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>',
    'cpu'       => '<rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line>',
    'calendar'  => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
    'award'     => '<circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>',
    'image'     => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>',
    'quote'     => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
    'file'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
    'dollar'    => '<line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>',
    'chart'     => '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>',
    'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line>',
    'upload'    => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line>',
    'folder'    => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>',
    'database'  => '<ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>',
    'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>',
    'globe'     => '<circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>',
    'plus'      => '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>',
    'clock'     => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
    'external'  => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line>',
    'user'      => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
    'trash'     => '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>',
    'settings'  => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
];

/**
 * Inline SVG icon (Lucide-style). Falls back to the "plus" glyph.
 */
function icon(string $name, int $size = 24): string
{
    $paths = $GLOBALS['ui_icons'][$name] ?? $GLOBALS['ui_icons']['plus'];
    return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
}

/**
 * Standard tinted pill badge. Tone: primary | secondary | success | danger | warning | info.
 */
function badge(string $text, string $tone = 'secondary'): string
{
    return '<span class="badge badge-' . $tone . '">' . $text . '</span>';
}

/**
 * Reorder handle button used as the first cell of sortable tables.
 */
function sortableHandle(int $rowId): string
{
    return '<td class="reorder-col">'
        . '<button type="button" class="sortable-handle" aria-label="Drag to reorder" title="Drag to reorder">'
        . '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1.5"></circle><circle cx="15" cy="6" r="1.5"></circle><circle cx="9" cy="12" r="1.5"></circle><circle cx="15" cy="12" r="1.5"></circle><circle cx="9" cy="18" r="1.5"></circle><circle cx="15" cy="18" r="1.5"></circle></svg>'
        . '</button>'
        . '</td>';
}

/**
 * Row "order" badge cell (#1, #2, ...) kept in sync after drag-reorders.
 */
function orderBadge(int $order): string
{
    return '<td><span class="badge badge-secondary order-badge">#' . (int)$order . '</span></td>';
}

/**
 * Table header cells for a sortable list: grip spacer + Order column.
 */
function reorderHeaderCells(): string
{
    return '<th class="reorder-col" style="width: 48px;"></th>'
        . '<th style="width: 50px;">Order</th>';
}

/**
 * Full-width empty-state row used when a list has no rows.
 */
function emptyCell(int $colspan, string $messageHtml): string
{
    return '<tr><td colspan="' . (int)$colspan . '" class="empty-cell">' . $messageHtml . '</td></tr>';
}

/**
 * Wrapper div for a row's action buttons (Edit / View / Delete ...).
 */
function actionsCell(array $actionButtons): string
{
    return '<td style="text-align: right;">'
        . '<div style="display: inline-flex; gap: 6px;">'
        . implode('', $actionButtons)
        . '</div>'
        . '</td>';
}

/**
 * Renders a single row action button.
 *
 * Link-style button:
 *   ['type' => 'link', 'label' => 'Edit', 'class' => 'btn-secondary',
 *    'href' => '...', 'drawerUrl' => '...', 'drawerTitle' => 'Edit Course',
 *    'target' => '_blank', 'title' => '...', 'iconHtml' => '...']
 * Delete button (submits a hidden inline form through the confirm modal):
 *   ['type' => 'delete', 'label' => 'Delete', 'formId' => 'delete-x-3',
 *    'formAction' => 'x.php', 'confirm' => 'Delete this record?']
 */
function rowActionButton(array $a): string
{
    if (($a['type'] ?? '') === 'delete') {
        $id = 'delete-' . ($a['formPrefix'] ?? 'row') . '-' . (int)$a['id'];
        $formId = $a['formId'] ?? $id;
        $confirm = $a['confirm'] ?? 'Delete this record? This cannot be undone.';
        return '<form method="POST" action="' . htmlspecialchars($a['formAction']) . '" style="display: inline;" id="' . htmlspecialchars($formId) . '">'
            . csrfField()
            . '<input type="hidden" name="action" value="' . htmlspecialchars($a['actionValue'] ?? 'delete') . '">'
            . '<input type="hidden" name="id" value="' . (int)$a['id'] . '">'
            . '<button type="button" class="btn btn-danger btn-sm" data-confirm="' . htmlspecialchars($confirm) . '"'
            . ' data-confirm-form="#' . htmlspecialchars($formId) . '">' . ($a['label'] ?? 'Delete') . '</button>'
            . '</form>';
    }

    $cls = $a['class'] ?? 'btn-secondary';
    $href = $a['href'] ?? '#';
    // Raw mode: the URL already contains entity-encoded markup (e.g. the
    // pre-encoded '&amp;drawer=1' suffix), so it must be echoed unescaped.
    if (empty($a['rawHref'])) {
        $href = htmlspecialchars($href);
    }
    $attrs = 'class="btn ' . $cls . ' btn-sm"';
    if (!empty($a['drawerUrl'])) {
        $drawerUrl = empty($a['rawHref']) ? htmlspecialchars($a['drawerUrl']) : $a['drawerUrl'];
        $attrs = 'data-drawer-url="' . $drawerUrl . '" ' . $attrs;
    }
    if (!empty($a['drawerTitle'])) {
        $attrs .= ' data-drawer-title="' . htmlspecialchars($a['drawerTitle']) . '"';
    }
    if (!empty($a['title'])) {
        $attrs .= ' title="' . htmlspecialchars($a['title']) . '"';
    }
    if (!empty($a['target'])) {
        $attrs .= ' target="' . htmlspecialchars($a['target']) . '"';
    }
    return '<a href="' . $href . '" ' . $attrs . '>'
        . ($a['iconHtml'] ?? '')
        . ($a['label'] ?? '')
        . '</a>';
}

/**
 * Renders one labelled form control group (.form-group).
 *
 * $f keys:
 *  - name, label, type: text|textarea|number|url|select|check|file
 *  - required, placeholder, maxlength, rows, full (full-width)
 *  - default: value used when creating (string) — may be a callable
 *  - load: fn($item) => mixed raw value for this field (overrides db value)
 *  - options: select only, map of value => label HTML
 *  - checkText: check only, label HTML shown after the checkbox
 *  - accept: file only, input accept attribute
 *  - preview: file only, ['id' => ..., 'img' => fn($item) => path without '../', 'hint' => text]
 *  - hintHtml: raw HTML rendered after the control (fn($item) => string or string)
 *  - raw: fn($item) => string — full custom form-group HTML, bypasses the builder
 */
function formField(array $f, ?array $item): string
{
    if (isset($f['raw'])) {
        return is_callable($f['raw']) ? $f['raw']($item) : $f['raw'];
    }

    $name = $f['name'] ?? '';
    $type = $f['type'] ?? 'text';
    $full = !empty($f['full']) ? ' full-width' : '';

    // Resolve the current value of this field.
    $raw = null;
    if (isset($f['load']) && is_callable($f['load'])) {
        $raw = $f['load']($item);
    } elseif ($item !== null && array_key_exists($name, $item)) {
        $raw = $item[$name];
    } elseif (array_key_exists('default', $f)) {
        $raw = is_callable($f['default']) ? $f['default']() : $f['default'];
    } elseif ($item === null && isset($f['createDefault'])) {
        $raw = is_callable($f['createDefault']) ? $f['createDefault']() : $f['createDefault'];
    }

    $html = '<div class="form-group' . $full . '">';

    if ($type === 'switch') {
        $checked = !empty($raw) ? ' checked' : '';
        $html .= '<label class="form-switch">'
            . '<input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" value="1"' . $checked . '>'
            . '<span class="switch-label">' . ($f['checkText'] ?? $f['label'] ?? '') . '</span>'
            . '</label>';
    } elseif ($type === 'check') {
        $checked = !empty($raw) ? ' checked' : '';
        $html .= '<label style="display: inline-flex; align-items: center; gap: 10px; font-size: 13.5px; cursor: pointer; user-select: none;">'
            . '<input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" value="1"' . $checked . '>'
            . '<span style="color: var(--text-main); font-weight: 600;">' . ($f['checkText'] ?? '') . '</span>'
            . '</label>';
    } else {
        $label = $f['label'] ?? '';
        $labelFor = ($type === 'file' || !empty($f['noFor'])) ? '' : ' for="' . htmlspecialchars($name) . '"';
        $html .= '<label class="form-label"' . $labelFor . '>' . $label . '</label>';

        $required = !empty($f['required']) ? ' required' : '';
        $classes = 'class="form-control"';
        $value = htmlspecialchars((string)($raw ?? ''));

        switch ($type) {
            case 'textarea':
                $rows = (int)($f['rows'] ?? 3);
                $html .= '<textarea id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" ' . $classes . ' rows="' . $rows . '"'
                    . $required
                    . (isset($f['placeholder']) ? ' placeholder="' . $f['placeholder'] . '"' : '')
                    . '>' . $value . '</textarea>';
                break;

            case 'rating':
                $cur = (int)($raw ?? ($f['createDefault'] ?? 5));
                $html .= '<input type="hidden" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" value="' . $cur . '">';
                $html .= '<div class="rating-pills" role="radiogroup" aria-label="Star rating">';
                for ($i = 1; $i <= 5; $i++) {
                    $sel = $i <= $cur ? ' active' : '';
                    $html .= '<button type="button" class="rating-star' . $sel . '" data-rating="' . $i . '" aria-label="' . $i . ' star' . ($i > 1 ? 's' : '') . '">★</button>';
                }
                $html .= '</div>';
                break;

            case 'select':
                $selectedVal = $raw ?? '';
                $html .= '<select id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" ' . $classes . '>';
                foreach (($f['options'] ?? []) as $optVal => $optLabel) {
                    $sel = ((string)$selectedVal === (string)$optVal) ? ' selected' : '';
                    $html .= '<option value="' . htmlspecialchars((string)$optVal) . '"' . $sel . '>' . $optLabel . '</option>';
                }
                $html .= '</select>';
                break;

            case 'file':
                $accept = isset($f['accept']) ? ' accept="' . htmlspecialchars($f['accept']) . '"' : '';
                $extraCls = !empty($f['preview']) ? ' image-preview-input' : '';
                $previewAttr = !empty($f['preview']) ? ' data-preview-target="' . htmlspecialchars($f['preview']['id'] ?? '') . '"' : '';
                $html .= '<input type="file" name="' . htmlspecialchars($name) . '" class="form-control' . $extraCls . '"' . $previewAttr . $accept . '>';
                break;

            default: // text / number / url
                $inputType = in_array($type, ['number', 'url'], true) ? $type : 'text';
                $html .= '<input type="' . $inputType . '" id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" ' . $classes . $required
                    . ' value="' . $value . '"'
                    . (isset($f['placeholder']) ? ' placeholder="' . $f['placeholder'] . '"' : '')
                    . (isset($f['maxlength']) ? ' maxlength="' . (int)$f['maxlength'] . '"' : '')
                    . '>';
        }
    }

    // Image preview block (file fields with image-preview-input).
    if ($type === 'file' && isset($f['preview']) && is_array($f['preview'])) {
        $p = $f['preview'];
        $imgPath = is_callable($p['img'] ?? null) ? $p['img']($item) : ($p['img'] ?? 'assets/logo.ico');
        $hint = $p['hint'] ?? 'Upload JPG, PNG or WebP image.';
        $html .= '<div style="margin-top: 8px; display: flex; align-items: center; gap: 12px;">'
            . '<img id="' . htmlspecialchars($p['id'] ?? '') . '" class="preview-avatar" src="../' . htmlspecialchars($imgPath) . '" loading="lazy" onerror="this.src=\'../assets/logo.ico\'">'
            . '<span class="form-hint">' . $hint . '</span>'
            . '</div>';
    }

    // Optional trailing hint/notes inside the group.
    if (isset($f['hintHtml'])) {
        $h = $f['hintHtml'];
        $html .= '<span class="form-hint">' . (is_callable($h) ? $h($item) : $h) . '</span>';
    }

    $html .= '</div>';
    return $html;
}
