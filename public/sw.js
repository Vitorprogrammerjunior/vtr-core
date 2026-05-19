const CACHE = 'vtr-v1';

self.addEventListener('install', e => {
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(clients.claim());
});

self.addEventListener('fetch', e => {
    // Passa tudo para a rede normalmente (sem cache offline)
    e.respondWith(fetch(e.request).catch(() => caches.match(e.request)));
});
