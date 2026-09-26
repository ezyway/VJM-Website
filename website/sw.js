/**
 * ====================================================
 * SERVICE WORKER - PWA OFFLINE CACHING & FAST SHELL
 * Shri V.J. Modha College Portal
 * ====================================================
 */

const CACHE_NAME = "vjm-portal-v2";
const CACHE_TTL_MS = 7 * 24 * 60 * 60 * 1000; // 7 days TTL for cached pages
const MAX_CACHED_PAGES = 20;

const CORE_ASSETS = [
    "./",
    "index.php",
    "styles/global.css",
    "styles/nav.css",
    "styles/footer.css",
    "styles/index.css",
    "scripts/bg_particles.js",
    "scripts/nav.js",
    "assets/nav_logo.png",
    "assets/logo.ico",
    "manifest.json"
];

// Install Event: Cache essential shell
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.allSettled(
                CORE_ASSETS.map((asset) => cache.add(asset))
            );
        }).then(() => self.skipWaiting())
    );
});

// Activate Event: Clean up outdated caches
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Trim cache: enforce TTL expiration and max page limit
async function trimCache(cache) {
    const keys = await cache.keys();
    const now = Date.now();

    // Remove expired entries (TTL check via response headers where possible)
    await Promise.all(keys.map(async (req) => {
        const res = await cache.match(req);
        const dateHeader = res?.headers.get('date');
        if (dateHeader) {
            const age = now - new Date(dateHeader).getTime();
            if (age > CACHE_TTL_MS) await cache.delete(req);
        }
    }));

    // Trim oldest entries if exceeding MAX_CACHED_PAGES
    const remaining = await cache.keys();
    if (remaining.length > MAX_CACHED_PAGES) {
        const excess = remaining.length - MAX_CACHED_PAGES;
        // Delete oldest entries first (keys() returns in insertion order)
        await Promise.all(remaining.slice(0, excess).map(req => cache.delete(req)));
    }
}

// Fetch Event: Network-First for Navigation (HTML/PHP), Cache-First for static assets
self.addEventListener("fetch", (event) => {
    const request = event.request;

    // Ignore non-GET requests and external analytics
    if (request.method !== "GET") return;
    if (request.url.includes("google-analytics.com") || request.url.includes("googletagmanager.com")) return;

    // Never intercept or cache Admin CMS panel requests
    if (request.url.includes("/admin/")) return;

    // HTML / Page Navigation: Network First -> Cache Fallback
    if (request.mode === "navigate" || request.headers.get("accept")?.includes("text/html")) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.ok && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                            return trimCache(cache);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    return caches.match(request).then((cachedResponse) => {
                        if (cachedResponse) return cachedResponse;
                        return caches.match("index.php");
                    });
                })
        );
        return;
    }

    // Static Assets (Images, CSS, JS, Fonts): Cache First with background revalidation -> Network Fallback
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                // Fetch in background to revalidate cache (Stale-While-Revalidate)
                fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.ok && networkResponse.status === 200) {
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                    }
                }).catch(() => {});
                return cachedResponse;
            }

            return fetch(request).then((networkResponse) => {
                if (networkResponse && networkResponse.ok && networkResponse.status === 200) {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, responseToCache));
                }
                return networkResponse;
            }).catch(() => {
                return new Response("", { status: 408, statusText: "Offline" });
            });
        })
    );
});
