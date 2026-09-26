<?php
/**
 * Reusable Filter Tabs Component
 * 
 * Usage:
 *   $filterTabs = [
 *       'id'    => 'coursesFilter',
 *       'tabs'  => [
 *           ['label' => 'All Programs (8)', 'value' => 'all', 'active' => true],
 *           ['label' => 'Undergraduate (5)', 'value' => 'ug', 'active' => false],
 *       ],
 *   ];
 *   include('components/filter_tabs.php');
 */

$filter_defaults = [
    'id'   => 'filterTabs',
    'tabs' => [],
];

$filterTabs = array_merge($filter_defaults, $filterTabs ?? []);
?>

<div class="filter-tabs" role="tablist" id="<?= htmlspecialchars($filterTabs['id']) ?>">
    <?php foreach ($filterTabs['tabs'] as $tab): ?>
        <button
            type="button"
            class="filter-tabs__btn <?= !empty($tab['active']) ? 'is-active' : '' ?>"
            data-filter="<?= htmlspecialchars($tab['value']) ?>"
            role="tab"
            aria-selected="<?= !empty($tab['active']) ? 'true' : 'false' ?>"
        ><?= htmlspecialchars($tab['label']) ?></button>
    <?php endforeach; ?>
</div>
