<?php
/**
 * Events & Announcements Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('events.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        crudDelete($db, 'events', (int)($_POST['id'] ?? 0), 'Item removed successfully.', $isDrawerMode, 'events.php');
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $badge_type  = trim($_POST['badge_type'] ?? 'confirmed');
        $description = trim($_POST['description'] ?? '');
        $event_type  = trim($_POST['event_type'] ?? 'event');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);

        if (empty($title)) {
            setFlash('danger', 'Title is required.');
            crudRedirect('events.php', $isDrawerMode);
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE events SET title = :t, badge = :b, badge_type = :bt, description = :d, event_type = :et, sort_order = :s WHERE id = :id');
            $stmt->execute([
                ':t'  => $title,
                ':b'  => $badge,
                ':bt' => $badge_type,
                ':d'  => $description,
                ':et' => $event_type,
                ':s'  => $sort_order,
                ':id' => $id
            ]);
            setFlash('success', 'Event / Notice updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO events (title, badge, badge_type, description, event_type, sort_order) VALUES (:t, :b, :bt, :d, :et, :s)');
            $stmt->execute([
                ':t'  => $title,
                ':b'  => $badge,
                ':bt' => $badge_type,
                ':d'  => $description,
                ':et' => $event_type,
                ':s'  => $sort_order
            ]);
            setFlash('success', 'New item created successfully.');
        }

        crudRedirect('events.php', $isDrawerMode);
    }
}

$pageTitle = 'Events & News Hub';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'events');
$isCreate = crudIsCreate();
$items = $db->query('SELECT * FROM events ORDER BY event_type ASC, sort_order ASC, id DESC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'events.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'title'       => fn($item) => $item ? 'Edit Event / Announcement' : 'Post New Event or Notice',
        'submitLabel' => 'Save Changes',
        'fields'      => [
            ['name' => 'title', 'label' => 'Title / Headline *', 'type' => 'text', 'full' => true, 'required' => true, 'placeholder' => 'e.g. Independence Day Celebration'],
            ['name' => 'event_type', 'label' => 'Category Section *', 'type' => 'select', 'options' => ['event' => 'Upcoming Event (Campus Life)', 'news' => 'Academic News &amp; Circular']],
            ['name' => 'badge', 'label' => 'Badge / Date Text', 'type' => 'text', 'default' => 'TBA', 'placeholder' => 'e.g. 15 Aug 2025 or TBA or Latest'],
            ['name' => 'badge_type', 'label' => 'Badge Visual Style', 'type' => 'select', 'default' => 'confirmed', 'options' => ['confirmed' => 'Confirmed / Highlighted (Green)', 'tba' => 'TBA / Neutral (Gray/Muted)', 'latest' => 'Latest / Notice (Blue/Purple)']],
            ['name' => 'sort_order', 'label' => 'Display Order Priority', 'type' => 'number', 'default' => '1'],
            ['name' => 'description', 'label' => 'Summary / Content', 'type' => 'textarea', 'full' => true, 'rows' => 3, 'placeholder' => 'Provide a brief summary of the event or notice...'],
        ],
    ]);
}

crudListPanel([
    'page'              => 'events.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'All Events &amp; Circulars (' . count($items) . ')',
    'rows'              => $items,
    'tableId'           => 'eventsTable',
    'reorder'           => 'events',
    'searchPlaceholder' => 'Search events...',
    'emptyText'         => 'No events or notices yet. Click <strong>Add New Item</strong> to post one.',
    'add'               => ['url' => 'events.php?action=create', 'drawerTitle' => 'Post New Event or Notice', 'label' => 'Add New Item'],
    'columns'           => [
        ['th' => 'Category', 'td' => fn($r) => badge($r['event_type'] === 'event' ? 'Upcoming Event' : 'Academic News', $r['event_type'] === 'event' ? 'primary' : 'info')],
        ['th' => 'Title', 'td' => fn($r) => '<strong style="color: var(--text-main);">' . htmlspecialchars($r['title']) . '</strong>'],
        ['th' => 'Date / Badge', 'td' => fn($r) => badge(htmlspecialchars($r['badge']), $r['badge_type'] === 'confirmed' ? 'success' : ($r['badge_type'] === 'latest' ? 'info' : 'secondary'))],
        ['th' => 'Summary', 'td' => fn($r) => '<div style="color: var(--text-muted); max-width: 300px; font-size: 12.5px;">' . htmlspecialchars(mb_strimwidth($r['description'], 0, 80, '...')) . '</div>'],
    ],
    'editUrl'           => fn($r) => 'events.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Event / Announcement'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this event or notice? This cannot be undone.',
    'formPrefix'        => 'event',
]);

require_once __DIR__ . '/includes/footer.php';
