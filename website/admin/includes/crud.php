<?php
/**
 * Shared Admin CRUD Engine
 * Server-side request helpers + generic list/form panel renderers used by the
 * standard list modules (courses, events, labs, rankers, testimonials,
 * magazines, pass_rates, faculties).
 */

require_once __DIR__ . '/ui.php';

/**
 * CSRF guard for POST handlers: flash + redirect to $page on failure.
 */
function crudCsrfGuard(string $page): void
{
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: ' . $page);
        exit;
    }
}

/**
 * Redirect after a successful POST. Inside the drawer we bounce back to the
 * same request URI (so the iframe re-renders the form context and the parent
 * receives the close message); on full pages we return to the list.
 */
function crudRedirect(string $page, bool $isDrawerMode): void
{
    header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : $page));
    exit;
}

/**
 * Loads the row being edited (?edit=N) for a module table, or null.
 */
function crudLoadItem(PDO $db, string $table): ?array
{
    $allowedTables = ['courses', 'faculties', 'labs', 'events', 'rankers', 'pass_rates', 'testimonials', 'magazines', 'scholarships', 'scholarship_portals', 'gallery_albums', 'gallery_photos'];
    if (!in_array($table, $allowedTables, true)) {
        throw new InvalidArgumentException("Invalid table name");
    }
    if (!isset($_GET['edit'])) return null;
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch();
    return $item ?: null;
}

/**
 * Whether the module should show the "create" form (?action=create).
 */
function crudIsCreate(): bool
{
    return isset($_GET['action']) && $_GET['action'] === 'create';
}

/**
 * Generic delete action: removes the row, flashes, redirects, exits.
 */
function crudDelete(PDO $db, string $table, int $id, string $flashMsg, bool $isDrawerMode, string $page): void
{
    $allowedTables = ['courses', 'faculties', 'labs', 'events', 'rankers', 'pass_rates', 'testimonials', 'magazines', 'scholarships', 'scholarship_portals', 'gallery_albums', 'gallery_photos'];
    if (!in_array($table, $allowedTables, true)) {
        throw new InvalidArgumentException("Invalid table name");
    }
    $stmt = $db->prepare("DELETE FROM {$table} WHERE id = :id");
    $stmt->execute([':id' => $id]);
    logAdminActivity('delete', $table, $id, "Deleted record #{$id} from {$table}");
    setFlash('success', $flashMsg);
    crudRedirect($page, $isDrawerMode);
}

/**
 * Add/Edit form panel.
 *
 * $cfg:
 *  - page: base page filename (cancel / back link)
 *  - pageParam: drawer link suffix from header ($drawerParam)
 *  - item: edit row or null
 *  - creating: bool
 *  - title: fn(?array $item) => string
 *  - submitLabel: string
 *  - multipart: bool
 *  - hiddenHtml: string | fn(?array $item) => string (extra hidden inputs)
 *  - fields: array of formField() configs
 */
function crudFormPanel(array $cfg): void
{
    $item = $cfg['item'] ?? null;
    $page = $cfg['page'];
    $param = $cfg['pageParam'] ?? '';
    $multipart = !empty($cfg['multipart']) ? ' enctype="multipart/form-data"' : '';
    $hiddenHtml = $cfg['hiddenHtml'] ?? '';
    if (is_callable($hiddenHtml)) $hiddenHtml = $hiddenHtml($item);

    $title = $cfg['title']($item);
    $previewType = $cfg['previewType'] ?? null;
    if ($previewType === null) {
        // Auto-detect preview type from base page filename
        $base = basename($page, '.php');
        $map = [
            'faculties'    => 'faculty',
            'courses'      => 'course',
            'events'       => 'event',
            'rankers'      => 'ranker',
            'testimonials' => 'testimonial',
            'labs'         => 'lab',
            'gallery'      => 'gallery',
            'magazines'    => 'magazine',
            'scholarships' => 'scholarship',
        ];
        $previewType = $map[$base] ?? '';
    }
    $hasPreview = !empty($previewType) && $previewType !== 'none';
    ?>
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><?= $title ?></div>
            <a href="<?= $page . $param ?>" class="btn btn-secondary btn-sm">← Back to List</a>
        </div>
        <div class="panel-body">
            <?php if ($hasPreview): ?>
            <div class="form-with-preview" data-live-preview="<?= htmlspecialchars($previewType) ?>">
                <div class="form-fields-col">
            <?php endif; ?>

            <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? $page) ?>"<?= $multipart ?> id="crudAdminForm">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= $item ? (int)$item['id'] : 0 ?>">
                <?= $hiddenHtml ?>
                <div class="form-grid">
                    <?php foreach ($cfg['fields'] as $field): ?>
                        <?= formField($field, $item) ?>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 22px; display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-primary"><?= $cfg['submitLabel'] ?></button>
                    <a href="<?= $page . $param ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            <?php if ($hasPreview): ?>
                </div>
                <div class="live-preview-col">
                    <div class="live-preview-sticky">
                        <div class="live-preview-header">
                            <div class="live-preview-title">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                Live Visual Preview
                            </div>
                        </div>
                        <div class="live-preview-stage" id="livePreviewContainer">
                            <div style="color: var(--text-muted); font-size: 13px;">Generating live preview...</div>
                        </div>
                        <div class="live-preview-caption">Instant live preview of changes before saving</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Reusable list panel: header (title + live search + Add), reorder support,
 * per-row columns and Edit/View/Delete actions.
 *
 * $cfg:
 *  - page: base page filename
 *  - listTitleHtml: already-escaped panel title incl. counts
 *  - rows: list of associative arrays
 *  - tableId, searchPlaceholder
 *  - reorder: table name for drag-reorder, or null
 *  - suffix: $drawerUrlSuffix from header (for drawer-targeted links)
 *  - add: ['url' => base url, 'drawerTitle' => ..., 'label' => ...] (optional)
 *  - columns: array of ['th' => html, 'td' => fn($row) => html]
 *  - editUrl: fn($row) => base url for the Edit drawer link (optional)
 *  - actions: ['edit' => bool + drawerTitle, 'view' => [...], 'delete' => bool]
 *  - deleteConfirm: fn($row) => string
 *  - extraButtons: fn($row) => html appended inside the actions cell (optional)
 */
function crudListPanel(array $cfg): void
{
    $rows = $cfg['rows'];
    $hasActions = isset($cfg['actions']);
    $hasReorder = !empty($cfg['reorder']);
    $extraTh = $hasReorder ? reorderHeaderCells() : '';
    $headerCount = ($hasReorder ? 2 : 0) + count($cfg['columns']) + ($hasActions ? 1 : 0);
    $add = $cfg['add'] ?? null;
    $suffix = $cfg['suffix'] ?? '';

    if ($add) {
        $addHref = $add['url'] . $suffix;
        $addBtn = '<a href="' . $addHref . '"'
            . ' data-drawer-url="' . $addHref . '"'
            . ' data-drawer-title="' . htmlspecialchars($add['drawerTitle']) . '"'
            . ' class="btn btn-primary btn-sm">' . icon('plus', 14) . ' ' . $add['label'] . '</a>';
    } else {
        $addBtn = '';
    }
    ?>
    <div class="panel">
        <div class="panel-header">
            <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                <div class="panel-title"><?= $cfg['listTitleHtml'] ?></div>
                <input type="text" class="form-control" data-table-search="<?= htmlspecialchars($cfg['tableId']) ?>" placeholder="<?= $cfg['searchPlaceholder'] ?>" style="max-width: 320px; font-size: 13px;">
            </div>
            <?= $addBtn ?>
        </div>

        <div class="panel-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="admin-table" id="<?= htmlspecialchars($cfg['tableId']) ?>"<?= $hasReorder ? ' data-reorder="' . htmlspecialchars($cfg['reorder']) . '"' : '' ?>>
                    <thead>
                        <tr>
                            <?= $extraTh ?>
                            <?php foreach ($cfg['columns'] as $col): ?>
                                <?php if (isset($col['align'])): ?>
                                    <th style="text-align: <?= $col['align'] ?>;"><?= $col['th'] ?></th>
                                <?php else: ?>
                                    <th><?= $col['th'] ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if ($hasActions): ?>
                                <th style="text-align: right;">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <?= emptyCell($headerCount, $cfg['emptyText']) ?>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                            <tr<?= $hasReorder ? ' data-reorder-id="' . (int)$row['id'] . '"' : '' ?>>
                                <?php if ($hasReorder): ?>
                                    <?= sortableHandle((int)$row['id']) ?>
                                    <?= orderBadge((int)$row['sort_order']) ?>
                                <?php endif; ?>
                                <?php foreach ($cfg['columns'] as $col): ?>
                                    <td><?= $col['td']($row) ?></td>
                                <?php endforeach; ?>
                                <?php if ($hasActions): ?>
                                    <?= actionsCell(crudRowActions($cfg, $row)) ?>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Builds the button list for one row's actions cell from a list-panel config.
 */
function crudRowActions(array $cfg, array $row): array
{
    $buttons = [];
    $a = $cfg['actions'];

    if (!empty($a['edit'])) {
        $editUrl = $cfg['editUrl']($row);
        $editHref = $editUrl . $cfg['suffix'];
        $buttons[] = rowActionButton([
            'type' => 'link',
            'label' => 'Edit',
            'class' => 'btn-secondary',
            'rawHref' => true,
            'href' => $editHref,
            'drawerUrl' => $editHref,
            'drawerTitle' => is_callable($a['edit']['drawerTitle'] ?? null) ? $a['edit']['drawerTitle']($row) : ($a['edit']['drawerTitle'] ?? 'Edit'),
        ]);
    }

    if (!empty($a['view'])) {
        $v = $a['view'];
        $buttons[] = rowActionButton([
            'type' => 'link',
            'label' => $v['label'] ?? 'View',
            'class' => $v['class'] ?? 'btn-secondary',
            'href' => is_callable($v['url'] ?? null) ? $v['url']($row) : ($v['url'] ?? '#'),
            'target' => $v['blank'] ?? false ? '_blank' : '',
            'title' => $v['title'] ?? ($v['label'] ?? 'View'),
            'iconHtml' => $v['iconHtml'] ?? '',
        ]);
    }

    if (!empty($a['delete'])) {
        $confirm = isset($cfg['deleteConfirm'])
            ? (is_callable($cfg['deleteConfirm']) ? $cfg['deleteConfirm']($row) : $cfg['deleteConfirm'])
            : 'Delete this record? This cannot be undone.';
        $buttons[] = rowActionButton([
            'type' => 'delete',
            'label' => 'Delete',
            'id' => (int)$row['id'],
            'formPrefix' => $cfg['formPrefix'] ?? 'row',
            'formAction' => $cfg['page'] . $cfg['pageParam'],
            'confirm' => $confirm,
        ]);
    }

    if (isset($cfg['extraButtons'])) {
        $extra = $cfg['extraButtons']($row);
        if (is_array($extra)) {
            $buttons = array_merge($buttons, $extra);
        } elseif (is_string($extra) && $extra !== '') {
            $buttons[] = $extra;
        }
    }

    return $buttons;
}
