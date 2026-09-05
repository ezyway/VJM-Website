<?php
/**
 * Announcement Popup (homepage modal)
 * Rendered from the "announcement_popup" setting managed in Admin → Settings.
 * Shows once per browser until dismissed; re-appears whenever the saved
 * content changes. Homepage-only: include this file only where desired.
 */

$vjmPopup = [];
try {
    require_once __DIR__ . '/../admin/includes/db.php';
    $rawPopup = getSetting('announcement_popup', '');
    if ($rawPopup !== '') {
        $decoded = json_decode($rawPopup, true);
        if (is_array($decoded)) $vjmPopup = $decoded;
    }
} catch (Exception $e) { $vjmPopup = []; }

$enabled  = !empty($vjmPopup['enabled']);
$hasBody  = !empty($vjmPopup['image']) || !empty($vjmPopup['badge'])
    || !empty($vjmPopup['title']) || !empty($vjmPopup['message'])
    || !empty($vjmPopup['buttons']);
if (!$enabled || !$hasBody) return; // nothing to announce — skip entirely

$payload = json_encode($vjmPopup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$signature = md5($payload);
$image = trim($vjmPopup['image'] ?? '');
?>
<!-- Announcement Popup (managed in Admin CMS → Settings → Announcement Popup) -->
<style>
    .vjm-pop-overlay[hidden]{display:none}
    .vjm-pop-overlay{position:fixed;inset:0;z-index:20000;display:flex;align-items:center;justify-content:center;
        background:rgba(4,12,10,.55);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);
        opacity:0;visibility:hidden;transition:opacity .28s ease,visibility .28s ease;padding:18px}
    .vjm-pop-overlay.is-open{opacity:1;visibility:visible}
    .vjm-pop-card{position:relative;width:min(560px,100%);max-height:calc(100vh - 40px);overflow:auto;
        background:#fff;border-radius:18px;box-shadow:0 30px 80px -12px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.06) inset;
        transform:translateY(14px) scale(.97);opacity:0;transition:transform .3s cubic-bezier(.16,1,.3,1),opacity .3s ease}
    .vjm-pop-overlay.is-open .vjm-pop-card{transform:translateY(0) scale(1);opacity:1}
    .vjm-pop-close{position:absolute;top:10px;right:10px;z-index:2;width:32px;height:32px;border-radius:50%;
        background:rgba(0,0,0,.28);border:0;color:#fff;font-size:18px;line-height:1;cursor:pointer;
        display:flex;align-items:center;justify-content:center;transition:background .2s}
    .vjm-pop-close:hover{background:rgba(0,0,0,.55)}
    .vjm-pop-close::before{content:"\00d7"}
    .vjm-pop-media{width:100%;height:150px;object-fit:cover;border-radius:18px 18px 0 0;display:block}
    .vjm-pop-body{padding:26px 28px 28px;color:#16302b;font-family:'Montserrat',sans-serif}
    .vjm-pop-badge{display:inline-block;background:linear-gradient(90deg,#155C4F,#1c8a72);color:#fff;font-size:11px;
        font-weight:700;letter-spacing:1.4px;text-transform:uppercase;padding:5px 12px;border-radius:999px;margin-bottom:12px}
    .vjm-pop-title{font-size:23px;font-weight:800;line-height:1.25;margin:0 0 10px;color:#0e2a24}
    .vjm-pop-message{font-size:14px;line-height:1.7;color:#42534e;margin:0 0 22px;white-space:pre-line}
    .vjm-pop-actions{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
    .vjm-pop-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;
        font-family:'Montserrat',sans-serif;font-size:13.5px;font-weight:700;padding:11px 22px;border-radius:10px;
        transition:transform .15s ease,box-shadow .15s ease,background .2s ease}
    .vjm-pop-btn:hover{transform:translateY(-1px)}
    .vjm-pop-btn--primary{background:linear-gradient(90deg,#155C4F,#1c8a72);color:#fff;box-shadow:0 6px 16px -4px rgba(21,92,79,.5)}
    .vjm-pop-btn--primary:hover{box-shadow:0 9px 20px -4px rgba(21,92,79,.6)}
    .vjm-pop-btn--secondary{background:#0e2a24;color:#fff}
    .vjm-pop-btn--outline{background:transparent;color:#155C4F;border:1.5px solid #155C4F}
    @media (prefers-reduced-motion:reduce){
        .vjm-pop-overlay,.vjm-pop-card,.vjm-pop-btn{transition:none}
    }
</style>
<div class="vjm-pop-overlay" id="vjmPopOverlay" role="dialog" aria-modal="true" aria-labelledby="vjmPopTitle" hidden>
    <div class="vjm-pop-card" id="vjmPopCard">
        <?php if ($image !== ''): ?>
            <img class="vjm-pop-media" id="vjmPopMedia" src="<?= htmlspecialchars($image) ?>" alt="">
        <?php endif; ?>
        <button class="vjm-pop-close" id="vjmPopClose" type="button" aria-label="Close announcement"></button>
        <div class="vjm-pop-body">
            <?php if (!empty($vjmPopup['badge'])): ?>
                <span class="vjm-pop-badge" id="vjmPopBadge"><?= htmlspecialchars($vjmPopup['badge']) ?></span>
            <?php endif; ?>
            <h2 class="vjm-pop-title" id="vjmPopTitle"><?= htmlspecialchars($vjmPopup['title'] ?? '') ?></h2>
            <?php if (!empty($vjmPopup['message'])): ?>
                <p class="vjm-pop-message" id="vjmPopMessage"><?= htmlspecialchars($vjmPopup['message']) ?></p>
            <?php endif; ?>
            <div class="vjm-pop-actions" id="vjmPopActions"></div>
        </div>
    </div>
</div>
<script>
(function () {
    var data = <?= $payload ?>;
    var sig = '<?= $signature ?>';
    var KEY = 'vjm_announcement_dismissed';
    var dismissed = null;
    try { dismissed = localStorage.getItem(KEY); } catch (e) {}
    if (dismissed === sig) return; // already seen this exact announcement

    var overlay = document.getElementById('vjmPopOverlay');
    var closeBtn = document.getElementById('vjmPopClose');

    function buildActions() {
        var wrap = document.getElementById('vjmPopActions');
        (data.buttons || []).forEach(function (b) {
            if (!b || (!b.label && !b.url)) return;
            var a = document.createElement('a');
            a.className = 'vjm-pop-btn vjm-pop-btn--' + (b.style || 'primary');
            a.href = b.url || '#';
            if (b.url && /^(https?:)?\/\//i.test(b.url)) a.target = '_blank';
            if (!b.url) a.setAttribute('aria-disabled', 'true');
            a.textContent = b.label || 'Learn More';
            wrap.appendChild(a);
        });
    }

    function dismiss() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('hidden', '');
        document.removeEventListener('keydown', onKey);
        try { localStorage.setItem(KEY, sig); } catch (e) {}
    }

    function onKey(e) { if (e.key === 'Escape') dismiss(); }

    function open() {
        buildActions();
        overlay.removeAttribute('hidden');
        // Force a reflow so the entrance transition plays.
        void overlay.offsetWidth;
        overlay.classList.add('is-open');
        document.addEventListener('keydown', onKey);
        if (closeBtn) closeBtn.focus();
    }

    if (closeBtn) closeBtn.addEventListener('click', dismiss);
    if (document.body) { open(); } else { document.addEventListener('DOMContentLoaded', open); }
})();
</script>
