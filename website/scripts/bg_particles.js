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
        // Sanskrit & Gujarati Academic Mottos
        "॥", "ॐ", "विद्या", "ज्ञानम्", "सत्यम्", "ऋ", "धर्मः", "विनयः", "तપઃ", "શ્રી", "વિદ્યા", "સત્ય", "શ્રદ્ધા", "વિવેક", "પ્રગતિ", "શિક્ષા", "સાધના",
        // Academic, Degree & Graduation Icons
        "🎓", "📚", "📖", "📝", "🏆", "🎯", "A+", "📜", "🏛️", "🏅", "🎨", "🌍", "🌱",
        // Degree Acronyms & Heritage
        "B.C.A.", "B.Sc.", "B.B.A.", "B.Com.", "M.Sc.", "M.Com.", "VJM", "2007",
        // Science, Mathematics & Research
        "⚛", "⚗️", "🔬", "🧪", "🧬", "π", "∑", "∞", "∫", "√", "λ", "Ω", "∆", "📐", "📊", "📈",
        // Technology, Computing & Innovation
        "💻", "{ }", "</>", "💡", "⚡", "✨", "✦", "★", "⚖️", "🔍", "🔑", "⚙️"
    ];

    const COLORS = [
        "rgba(10, 55, 46, 0.75)",    // Deep Forest Emerald
        "rgba(21, 92, 79, 0.72)",    // Primary Institutional Emerald
        "rgba(217, 119, 6, 0.75)",   // Rich Warm Amber
        "rgba(180, 115, 10, 0.72)",  // Polished Heritage Gold
        "rgba(13, 148, 136, 0.68)"   // Deep Teal Accent
    ];

    let canvas, ctx;
    let width = 0, height = 0;
    let particles = [];
    let animationFrameId = null;

    const mouse = {
        x: -9999,
        y: -9999,
        radius: 170,
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
            this.vx = (Math.random() - 0.5) * 0.55;
            this.vy = -(Math.random() * 0.45 + 0.2); // Gently floats upwards

            // 72% Glyphs/Icons, 28% Glowing Constellation Orbs
            this.type = Math.random() < 0.72 ? 'glyph' : 'node';
            
            if (this.type === 'glyph') {
                this.text = GLYPHS[Math.floor(Math.random() * GLYPHS.length)];
                this.fontSize = Math.floor(Math.random() * 12) + 16; // 16px - 28px
                this.fontFamily = this.text.charCodeAt(0) > 255 ? "'Montserrat', 'Noto Sans Devanagari', 'Noto Sans Gujarati', serif" : "'Montserrat', sans-serif";
            } else {
                this.radius = Math.random() * 3 + 1.5;
            }

            this.color = COLORS[Math.floor(Math.random() * COLORS.length)];
            this.alpha = Math.random() * 0.45 + 0.4; // 0.4 to 0.85 high contrast opacity
            this.baseAlpha = this.alpha;
            this.rotation = Math.random() * Math.PI * 2;
            this.rotSpeed = (Math.random() - 0.5) * 0.01;
            this.waveOffset = Math.random() * Math.PI * 2;
            this.waveSpeed = Math.random() * 0.02 + 0.012;
        }

        update(time) {
            // Natural drifting with gentle sinusoidal oscillation
            this.x += this.vx + Math.sin(time * this.waveSpeed + this.waveOffset) * 0.3;
            this.y += this.vy;
            this.rotation += this.rotSpeed;

            // Mouse Interactive Repel & Proximity Glow
            if (mouse.isActive) {
                const dx = mouse.x - this.x;
                const dy = mouse.y - this.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < mouse.radius && dist > 0) {
                    const force = (1 - dist / mouse.radius) * 4.2;
                    const angle = Math.atan2(dy, dx);
                    this.x -= Math.cos(angle) * force;
                    this.y -= Math.sin(angle) * force;
                    this.alpha = Math.min(1, this.baseAlpha + (1 - dist / mouse.radius) * 0.45);
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
        const count = Math.min(Math.max(Math.floor((window.innerWidth * window.innerHeight) / 14000), 45), 90);
        particles = [];
        for (let i = 0; i < count; i++) {
            particles.push(new Particle(true));
        }
    }

    function drawConstellationLines() {
        const maxDist = 110;
        const count = particles.length;

        for (let i = 0; i < count; i++) {
            const p1 = particles[i];
            for (let j = i + 1; j < count; j++) {
                const p2 = particles[j];
                const dx = p1.x - p2.x;
                const dy = p1.y - p2.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < maxDist) {
                    const lineAlpha = (1 - dist / maxDist) * 0.22 * Math.min(p1.alpha, p2.alpha);
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = "rgba(21, 92, 79, " + lineAlpha + ")";
                    ctx.lineWidth = 1;
                    ctx.stroke();
                    ctx.restore();
                }
            }

            // Connect to mouse cursor
            if (mouse.isActive) {
                const mdx = p1.x - mouse.x;
                const mdy = p1.y - mouse.y;
                const mdist = Math.sqrt(mdx * mdx + mdy * mdy);
                if (mdist < 150) {
                    const mAlpha = (1 - mdist / 150) * 0.38;
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.strokeStyle = "rgba(217, 119, 6, " + mAlpha + ")";
                    ctx.lineWidth = 1.25;
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
