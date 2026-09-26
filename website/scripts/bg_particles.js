/**
 * ====================================================================
 * AMBIENT BACKGROUND FIELD
 * Shri V.J. Modha College Portal
 *
 * A fixed field of short dashes covering the viewport. The pointer opens a
 * hollow gap in the field and a slow ripple travels outward from it, bounded
 * by a noise-morphed blob outline rather than a perfect circle. Every dash
 * points at the pointer, so the patch reads as soft radial spokes.
 *
 * Tuned deliberately quiet for an institutional site: sparse grid, thin
 * strokes, low opacity and a shallow ripple, in the college emerald/amber
 * palette. Replaces the earlier floating-glyph particle engine.
 * ====================================================================
 */

(function () {
    // Respect reduced motion preferences
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const SPACING = 62;         // grid pitch of the fixed dash field, in px
    const JITTER = 0.42;        // grid offset, as a fraction of the pitch
    const MORPH_SPEED = 0.16;   // how fast the noise field scrolls, in units/sec
    const WOBBLE = 0.26;        // noise displacement as a fraction of the radius
    const BLOB_EASE = 2.2;      // how fast the reveal catches the pointer (per second)
    const BASE_LEN = 11;        // dash length at the inner edge of the band, in px
    const CORE = 0.10;          // hollow middle, as a fraction of the reveal radius
    const RIPPLE_SPEED = 0.75;  // radians/sec the rings travel outward
    const RING_COUNT = 2.0;     // rings from the hollow to the blob edge
    const RIPPLE_HEIGHT = 9;    // px a full crest lifts the field upward
    const SLOPE_PX = 1.1;       // px of sideways drag per unit of radial slope
    const CREST_STRETCH = 0.30; // dash length boost at a full crest
    const CREST_BRIGHT = 0.28;  // dash brightness boost at a full crest
    const LINE_WIDTH = 1.7;     // stroke weight of a dash, in px
    const OPACITY_LIGHT = 1.7;  // light bg is pale, so push the strokes to read clearly
    const OPACITY_DARK = 1.25;  // dark surfaces swallow thin strokes, so lift a little

    // The institutional palette, walked around the form so it shifts
    // emerald -> teal -> amber -> gold and wraps back with no seam.
    const STOPS = [
        [21, 92, 79],     // primary institutional emerald
        [13, 148, 136],   // deep teal accent
        [16, 185, 129],   // light emerald
        [217, 119, 6],    // rich warm amber
        [180, 115, 10]    // polished heritage gold
    ];

    let canvas, ctx;
    let width = 0, height = 0;
    let radius = 160;           // recomputed on resize
    let dpr = 1;
    let last = 0;
    let morph = 0;              // scroll position through the noise field
    let shards = [];

    // Target is where the pointer is; blobPos is the eased follower. Both start
    // at the centre of the viewport so the page looks composed before any input.
    const target = { x: 0, y: 0 };
    const blobPos = { x: 0, y: 0 };

    // --- Value-noise field -------------------------------------------------
    // A hashed lattice with smoothstep interpolation. Not true simplex, but it
    // is a fraction of the code and indistinguishable at this scale.
    const LATTICE = new Float32Array(256);
    for (let i = 0; i < LATTICE.length; i++) LATTICE[i] = Math.random();

    function fade(k) { return k * k * (3 - 2 * k); }

    function noise2D(x, y) {
        const xi = Math.floor(x);
        const yi = Math.floor(y);
        const xf = fade(x - xi);
        const yf = fade(y - yi);
        // Two primes keep the row/column hashes from aliasing into each other
        const at = (a, b) => LATTICE[(a * 57 + b * 131) & 255];
        const top = at(xi, yi) + (at(xi + 1, yi) - at(xi, yi)) * xf;
        const bot = at(xi, yi + 1) + (at(xi + 1, yi + 1) - at(xi, yi + 1)) * xf;
        return (top + (bot - top) * yf) * 2 - 1;   // remapped to -1..1
    }

    // Samples the wrapping palette at t (0..1); wraps so there is no seam
    function paletteAt(t) {
        const scaled = ((t % 1) + 1) % 1 * STOPS.length;
        const i = Math.floor(scaled);
        const k = scaled - i;
        const a = STOPS[i];
        const b = STOPS[(i + 1) % STOPS.length];
        return `rgb(${Math.round(a[0] + (b[0] - a[0]) * k)},${Math.round(a[1] + (b[1] - a[1]) * k)},${Math.round(a[2] + (b[2] - a[2]) * k)})`;
    }

    // Colour is looked up per dash per frame, so the ramp is baked once into a
    // table rather than rebuilding a string every time
    const RAMP = [];
    for (let i = 0; i < 96; i++) RAMP.push(paletteAt(i / 96));

    function themeOpacity() {
        return document.documentElement.getAttribute("data-theme") === "dark"
            ? OPACITY_DARK
            : OPACITY_LIGHT;
    }

    // The dashes live on a jittered grid covering the viewport and are built
    // once per resize. Their positions are absolute and never change, which is
    // the whole point: the pointer moves the reveal, not the field.
    function buildField() {
        shards = [];
        const cols = Math.ceil(width / SPACING) + 1;
        const rows = Math.ceil(height / SPACING) + 1;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                shards.push({
                    // Jitter breaks up the grid so it doesn't read as a lattice
                    x: c * SPACING + (Math.random() - 0.5) * SPACING * JITTER,
                    y: r * SPACING + (Math.random() - 0.5) * SPACING * JITTER,
                    alpha: 0.16 + Math.random() * 0.18
                });
            }
        }
    }

    function resize() {
        if (!canvas) return;
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        // Blob radius sized off the longer edge so the ripple covers most of
        // the screen from anywhere the pointer can sit.
        radius = Math.max(width, height) * 0.62;
        buildField();
    }

    // The fixed canvas uses viewport coordinates, matching PointerEvent values.
    function onPointer(e) {
        target.x = e.clientX;
        target.y = e.clientY;
    }

    // Exponential easing: frame-rate independent, unlike a raw `+= diff * k`
    function ease(current, goal, rate, dt) {
        return current + (goal - current) * (1 - Math.exp(-rate * dt));
    }

    // The blob outline is never painted — it only says how far the reveal may
    // reach in a given direction, so the shape reads purely from which dashes
    // light up. Sampling the noise on a circle (rather than by index) keeps the
    // seam at angle 0 continuous, so there is no crease.
    function shapeRadius(angle) {
        const n = noise2D(Math.cos(angle) * 1.6 + morph, Math.sin(angle) * 1.6 + morph);
        return radius * (1 + n * WOBBLE);
    }

    function drawShard(p, t, opacity) {
        // Everything is derived from where this fixed dash sits relative to the
        // hollow centre; the field drags it around, it never leaves the grid.
        const dx = p.x - blobPos.x;
        const dy = p.y - blobPos.y;
        const a = Math.atan2(dy, dx);
        const f = Math.hypot(dx, dy) / shapeRadius(a);

        // Inside the hollow middle: the lines part around the pointer
        if (f < CORE) return;

        // Soft only on the inner edge, so the hollow has no hard rim. There is
        // no outer boundary, so every dash on screen reads as a full-screen
        // field with a gap wherever the cursor sits.
        const edge = Math.min(1, (f - CORE) / 0.14);

        // The height field: rings travel outward from the hollow and die
        // smoothly at the blob outline, so the ripple keeps the noise-morphed
        // shape instead of a perfect circle. The envelope is clamped so the
        // field goes flat beyond it — with a raw (1 - f) envelope the squared
        // term would grow again far from the pointer and break near the edges.
        const m = Math.max(0, 1 - f);
        const env = m * m;
        const phase = f * RING_COUNT * (Math.PI * 2) - t * RIPPLE_SPEED;
        const h = Math.cos(phase) * env;
        // Radial slope of the height field; dashes are dragged along it so the
        // field visibly bunches toward each passing crest
        const slope = -Math.sin(phase) * (Math.PI * 2) * RING_COUNT * env
                    - Math.cos(phase) * 2 * m;

        // Longest at the inner edge, tapering down to a steady length out beyond
        // the ring; crests stretch further and troughs shorten, so the height
        // reads in the lines themselves
        const norm = Math.min(1, (f - CORE) / (1 - CORE));
        const len = BASE_LEN * (1 - norm * 0.62) * (1 + h * CREST_STRETCH);
        // Length is the single driver of how strongly a dash reads: brightness
        // follows it, so the crest lights up simply by being longer there and
        // the outer taper dims itself on the way out
        const intensity = len / BASE_LEN;

        ctx.globalAlpha = Math.min(1, p.alpha * intensity * (1 + h * CREST_BRIGHT) * opacity) * edge;
        // Banded around the hollow, so the palette always reads from the pointer
        // outward no matter which dashes are currently uncovered
        ctx.strokeStyle = RAMP[(Math.floor(a / (Math.PI * 2) * 96) % 96 + 96) % 96];
        // The slope drags the dash sideways with the field, and the height lifts
        // it up, so the ripple reads as a bump in space rather than a flat shift
        const shiftX = Math.cos(a) * slope * SLOPE_PX;
        const shiftY = Math.sin(a) * slope * SLOPE_PX - h * RIPPLE_HEIGHT;
        ctx.translate(p.x + shiftX, p.y + shiftY);
        // Aligned with the radius and held there: every dash points straight at
        // the pointer, so the patch reads as clean spokes
        ctx.rotate(a);
        ctx.beginPath();
        ctx.moveTo(-len / 2, 0);
        ctx.lineTo(len / 2, 0);
        ctx.stroke();
        // Cheaper than save()/restore() for a transform we rebuild every dash
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    let rafId = null;

    function cleanup() {
        window.removeEventListener("resize", resize);
        window.removeEventListener("pointermove", onPointer);
        document.removeEventListener("visibilitychange", onVisibilityChange);
        if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    function onVisibilityChange() {
        if (document.hidden) {
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        } else {
            if (!rafId) {
                rafId = requestAnimationFrame(draw);
            }
        }
    }

    function draw(time) {
        rafId = requestAnimationFrame(draw);
        if (document.hidden) return;

        const t = time / 1000;
        // Clamped so a backgrounded tab doesn't snap everything on return
        const dt = last ? Math.min(t - last, 0.05) : 0;
        last = t;

        morph += MORPH_SPEED * dt;
        blobPos.x = ease(blobPos.x, target.x, BLOB_EASE, dt);
        blobPos.y = ease(blobPos.y, target.y, BLOB_EASE, dt);

        ctx.clearRect(0, 0, width, height);
        // Stroke settings are shared by every dash, so they are set once here
        // rather than per shard; setTransform does not disturb them
        ctx.lineWidth = LINE_WIDTH;
        ctx.lineCap = 'round';
        const opacity = themeOpacity();
        for (let i = 0; i < shards.length; i++) drawShard(shards[i], t, opacity);
        ctx.globalAlpha = 1;
    }

    function initCanvas() {
        if (canvas) return;

        canvas = document.createElement("canvas");
        canvas.id = "bgParticlesCanvas";
        canvas.setAttribute("aria-hidden", "true");
        canvas.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            pointer-events: none;
            z-index: 0;
            display: block;
        `;
        document.body.prepend(canvas);
        ctx = canvas.getContext("2d", { alpha: true });

        resize();
        window.addEventListener("resize", resize, { passive: true });
        // pointermove covers mouse, pen and touch-drag in one listener
        window.addEventListener("pointermove", onPointer, { passive: true });
        document.addEventListener("visibilitychange", onVisibilityChange);

        // Rest at the centre of the viewport until the pointer first moves
        target.x = blobPos.x = width / 2;
        target.y = blobPos.y = height / 2;
        rafId = requestAnimationFrame(draw);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initCanvas);
    } else {
        initCanvas();
    }
})();
