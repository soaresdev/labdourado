const staticCacheName = "pwa-v" + new Date().getTime();
const filesToCache = [
    '/css/app.css',
    '/css/login.css',
    '/css/reset.css',
    '/fonts/vendor/@mdi/materialdesignicons-webfont.eot',
    '/fonts/vendor/@mdi/materialdesignicons-webfont.ttf',
    '/fonts/vendor/@mdi/materialdesignicons-webfont.woff',
    '/fonts/vendor/@mdi/materialdesignicons-webfont.woff2',
    '/js/app.js',
    '/js/login.js',
    '/images/logo.png',
    '/images/icon/icon-72x72.png',
    '/images/icon/icon-96x96.png',
    '/images/icon/icon-128x128.png',
    '/images/icon/icon-144x144.png',
    '/images/icon/icon-152x152.png',
    '/images/icon/icon-192x192.png',
    '/images/icon/icon-384x384.png',
    '/images/icon/icon-512x512.png',
];
// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
