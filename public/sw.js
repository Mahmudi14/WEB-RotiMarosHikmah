const CACHE_NAME = "romahkm-pos-v3";

const STATIC_ASSETS = [
    "/manifest.webmanifest",
    "/images/icons/icon-192.png",
    "/images/icons/icon-512.png",
];

self.addEventListener("install", (event) => {
    self.skipWaiting();

    event.waitUntil(
        caches.open(CACHE_NAME).then(async (cache) => {
            for (const asset of STATIC_ASSETS) {
                try {
                    const response = await fetch(asset, {
                        cache: "no-store",
                    });

                    if (!response.ok) {
                        throw new Error(
                            `${response.status} ${response.statusText}`,
                        );
                    }

                    await cache.put(asset, response);
                } catch (error) {
                    console.error(
                        `Service Worker gagal menyimpan ${asset}:`,
                        error,
                    );
                }
            }
        }),
    );
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((keys) => {
                return Promise.all(
                    keys
                        .filter((key) => key !== CACHE_NAME)
                        .map((key) => caches.delete(key)),
                );
            })
            .then(() => self.clients.claim()),
    );
});

self.addEventListener("fetch", (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== "GET") {
        return;
    }

    if (url.origin !== self.location.origin) {
        return;
    }

    if (
        url.pathname.startsWith("/api") ||
        url.pathname.startsWith("/admin") ||
        url.pathname.startsWith("/cashier") ||
        url.pathname.startsWith("/super-admin") ||
        url.pathname.startsWith("/login") ||
        url.pathname.startsWith("/logout")
    ) {
        event.respondWith(fetch(request));
        return;
    }

    if (
        url.pathname.startsWith("/build") ||
        url.pathname.startsWith("/images/icons") ||
        url.pathname === "/manifest.webmanifest"
    ) {
        event.respondWith(
            caches.match(request).then(async (cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                const networkResponse = await fetch(request);

                if (networkResponse.ok) {
                    const cache = await caches.open(CACHE_NAME);
                    await cache.put(request, networkResponse.clone());
                }

                return networkResponse;
            }),
        );
    }
});
