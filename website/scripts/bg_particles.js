/**
 * ====================================================================
 * BG PARTICLES & FLOATING ACADEMIC GLYPHS ENGINE
 * Shri V.J. Modha College Portal
 *
 * Interactive ambient floating particles, Sanskrit academic motto glyphs,
 * science & IT symbols, and mouse-reactive constellation physics.
 * ====================================================================
 */

(function () {
    // Respect reduced motion preferences
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const GLYPHS = [
        // Sanskrit & Academic Glyphs
        "॥", "ॐ", "विद्या", "ज्ञानम्", "सत्यम्", "ऋ",
        // Academic & STEM Icons / Symbols
        "🎓", "📚", "⚛", "⚗️", "π", "∑", "∞", "💡", "💻", "{ }", "A+", "✨", "✦", "★"
    ];

    const COLORS = [
        "rgba(21, 92, 79, 0.35)",    // Brand Deep Emerald
        "rgba(34, 130, 112, 0.3)",    // Brand Light Emerald
        "rgba(217, 119, 6, 0.32)",    // Brand Warm Gold
        "rgba(251, 191, 36, 0.35)",   // Brand Bright Amber
        "rgba(16, 185, 129, 0.25)"    // Mint Accent
    ];

    let canvas, ctx;
    let width = 0, height = 0;
    let particles = [];
    let animationFrameId = null;

    const mouse = {
        x: -9999,
        y: -9999,
        radius: 140,
        isActive: false
    };

    class Particle {
        constructor(isInitial = false) {
            this.reset(isInitial);
        }

        reset(isInitial = false) {
            this.x = Math.random() * (width || window.innerWidth);
            this.y = isInitial ? Math.random() * (height || window.innerHeight) : (height || window.innerHeight) + 20;
            
            // Subtle drift velocities
            this.vx = (Math.random() - 0.5) * 0.45;
            this.vy = -(Math.random() * 0.35 + 0.15); // Gently floats upwards

            this.type = Math.random() < 0.45 ? 'glyph' : 'node';
            
            if (this.type === 'glyph') {
                this.text = GLYPHS[Math.floor(Math.random() * GLYPHS.length)];
                this.fontSize = Math.floor(Math.random() * 8) + 13; // 13px - 20px
                this.fontFamily = this.text.charCodeAt(0) > 255 ? "'Montserrat', 'Noto Sans Devanagari', serif" : "'Montserrat', sans-serif";
            } else {
                this.radius = Math.random() * 2.5 + 1.2;
            }

            this.color = COLORS[Math.floor(Math.random() * COLORS.length)];
            this.alpha = Math.random() * 0.45 + 0.2; // 0.2 to 0.65 opacity
            this.baseAlpha = this.alpha;
            this.rotation = Math.random() * Math.PI * 2;
            this.rotSpeed = (Math.random() - 0.5) * 0.008;
            this.waveOffset = Math.random() * Math.PI * 2;
            this.waveSpeed = Math.random() * 0.02 + 0.01;
        }

        update(time) {
            // Natural drifting with gentle sinusoidal oscillation
            this.x += this.vx + Math.sin(time * this.waveSpeed + this.waveOffset) * 0.25;
            this.y += this.vy;
            this.rotation += this.rotSpeed;

            // Mouse Interactive Repel & Proximity Glow
            if (mouse.isActive) {
                const dx = mouse.x - this.x;
                const dy = mouse.y - this.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < mouse.radius && dist > 0) {
                    const force = (1 - dist / mouse.radius) * 3.5;
                    const angle = Math.atan2(dy, dx);
                    this.x -= Math.cos(angle) * force;
                    this.y -= Math.sin(angle) * force;
                    this.alpha = Math.min(1, this.baseAlpha + (1 - dist / mouse.radius) * 0.5);
                } else {
                    this.alpha += (this.baseAlpha - this.alpha) * 0.05;
                }
            } else {
                this.alpha += (this.baseAlpha - this.alpha) * 0.05;
            }

            // Recycle offscreen particles to top/bottom
            if (this.y < -40 || this.x < -40 || this.x > width + 40) {
                this.reset(false);
            }
        }

        draw(ctx) {
            ctx.save();
            ctx.globalAlpha = this.alpha;
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation);

            if (this.type === 'glyph') {
                ctx.font = `600 ${this.fontSize}px ${this.fontFamily}`;
                ctx.fillStyle = this.color;
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText(this.text, 0, 0);
            } else {
                ctx.beginPath();
                ctx.arc(0, 0, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.shadowColor = this.color;
                ctx.shadowBlur = 6;
                ctx.fill();
            }

            ctx.restore();
        }
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

        // Mouse tracking
        window.addEventListener("mousemove", (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
            mouse.isActive = true;
        }, { passive: true });

        window.addEventListener("mouseleave", () => {
            mouse.isActive = false;
        }, { passive: true });

        // Touch tracking
        window.addEventListener("touchstart", (e) => {
            if (e.touches && e.touches[0]) {
                mouse.x = e.touches[0].clientX;
                mouse.y = e.touches[0].clientY;
                mouse.isActive = true;
            }
        }, { passive: true });

        window.addEventListener("touchmove", (e) => {
            if (e.touches && e.touches[0]) {
                mouse.x = e.touches[0].clientX;
                mouse.y = e.touches[0].clientY;
                mouse.isActive = true;
            }
        }, { passive: true });

        window.addEventListener("touchend", () => {
            mouse.isActive = false;
        }, { passive: true });

        createParticles();
        animate(0);
    }

    function resize() {
        if (!canvas) return;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.scale(dpr, dpr);
    }

    function createParticles() {
        // Density based on screen area (smooth on mobile, rich on desktop)
        const count = Math.min(Math.max(Math.floor((window.innerWidth * window.innerHeight) / 28000), 22), 48);
        particles = [];
        for (let i = 0; i < count; i++) {
            particles.push(new Particle(true));
        }
    }

    function drawConstellationLines() {
        const maxDist = 95;
        const count = particles.length;

        for (let i = 0; i < count; i++) {
            const p1 = particles[i];
            for (let j = i + 1; j < count; j++) {
                const p2 = particles[j];
                const dx = p1.x - p2.x;
                const dy = p1.y - p2.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < maxDist) {
                    const lineAlpha = (1 - dist / maxDist) * 0.12 * Math.min(p1.alpha, p2.alpha);
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = "rgba(21, 92, 79, " + lineAlpha + ")";
                    ctx.lineWidth = 0.85;
                    ctx.stroke();
                    ctx.restore();
                }
            }

            // Connect to mouse cursor
            if (mouse.isActive) {
                const mdx = p1.x - mouse.x;
                const mdy = p1.y - mouse.y;
                const mdist = Math.sqrt(mdx * mdx + mdy * mdy);
                if (mdist < 130) {
                    const mAlpha = (1 - mdist / 130) * 0.22;
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.strokeStyle = "rgba(217, 119, 6, " + mAlpha + ")";
                    ctx.lineWidth = 1;
                    ctx.stroke();
                    ctx.restore();
                }
            }
        }
    }

    let lastTime = 0;
    function animate(now) {
        if (document.hidden) {
            animationFrameId = requestAnimationFrame(animate);
            return;
        }

        ctx.clearRect(0, 0, width, height);

        const time = now * 0.001;

        // Draw connections
        drawConstellationLines();

        // Update and draw particles
        for (let i = 0; i < particles.length; i++) {
            particles[i].update(time);
            particles[i].draw(ctx);
        }

        animationFrameId = requestAnimationFrame(animate);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initCanvas);
    } else {
        initCanvas();
    }
})();
