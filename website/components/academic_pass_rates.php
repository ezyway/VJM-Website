<?php
require_once dirname(__DIR__) . '/admin/includes/db.php';

$passRates = [];
try {
    $db = getDB();
    $passRates = $db->query("SELECT * FROM pass_rates ORDER BY sort_order ASC, year DESC")->fetchAll();
} catch (Exception $e) {
    $passRates = [];
}
?>
<!-- ===================================================
        Academic Pass Rates Table Section
        - Displays historical academic pass rates across all streams
        =================================================== -->
<section class="table-section" id="academic-performance">
    <div class="table-section__container">
        
        <div class="table-section__header">
            <span class="section__eyebrow">Excellence in Numbers</span>
            <h2 class="table-section__title">Academic Pass Rates</h2>
            <p class="table-section__subtitle">Consistently outperforming university benchmarks with stellar graduation success rates across all faculties.</p>
        </div>

        <!-- Table Container with Horizontal Scroll -->
        <div class="table-container">
            <div class="table-scroll-hint">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 16l-4-4m0 0l4-4m-4 4h18M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                <span>Scroll horizontally to view all courses</span>
            </div>

            <table class="achievements-table">
                <thead>
                    <tr>
                        <th class="th-year">Year</th>
                        <th>BCA</th>
                        <th>B.Sc.</th>
                        <th>BBA</th>
                        <th>B.Com.</th>
                        <th>BSW</th>
                        <th>PGDCA</th>
                        <th>M.Sc. (IT)</th>
                        <th>M.Com.</th>
                        <th>M.Sc. (Chem)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($passRates)): ?>
                        <tr class="tr-latest">
                            <td><strong>2026</strong> <span class="badge-latest">Latest</span></td>
                            <td>90.80%</td>
                            <td class="rate-perfect">100.00%</td>
                            <td>91.94%</td>
                            <td>95.59%</td>
                            <td>95.00%</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($passRates as $pr): 
                            $isLatest = !empty($pr['is_latest']);
                        ?>
                        <tr class="<?= $isLatest ? 'tr-latest' : '' ?>">
                            <td>
                                <strong><?= htmlspecialchars($pr['year']) ?></strong>
                                <?php if ($isLatest): ?>
                                    <span class="badge-latest">Latest</span>
                                <?php endif; ?>
                            </td>
                            <td class="<?= (strpos($pr['bca'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['bca']) ?></td>
                            <td class="<?= (strpos($pr['bsc'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['bsc']) ?></td>
                            <td class="<?= (strpos($pr['bba'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['bba']) ?></td>
                            <td class="<?= (strpos($pr['bcom'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['bcom']) ?></td>
                            <td class="<?= (strpos($pr['bsw'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['bsw']) ?></td>
                            <td class="<?= (strpos($pr['pgdca'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['pgdca']) ?></td>
                            <td class="<?= (strpos($pr['msc_it'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['msc_it']) ?></td>
                            <td class="<?= (strpos($pr['mcom'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['mcom']) ?></td>
                            <td class="<?= (strpos($pr['msc_chem'], '100') !== false) ? 'rate-perfect' : '' ?>"><?= htmlspecialchars($pr['msc_chem']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>
