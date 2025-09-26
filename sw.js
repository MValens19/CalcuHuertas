const CACHE_NAME = 'globalfrut-cache-v1';
const urlsToCache = [
    '/',
    'index.php',
    'manifest.json',
    'icons/Global_Frut.png',
    'icons/logoglobal.png',
    'js/buscar_huertas.js',
    'js/buscar_municipio.js',
    'js/offline.js', // Agregar aquí
    'https://cdn.jsdelivr.net/npm/chart.js' // Para reportes
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.map(key => {
                if (key !== CACHE_NAME) {
                    return caches.delete(key);
                }
            })
        )).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request)
                .catch(() => {
                    if (event.request.destination === 'document') {
                        return caches.match('index.php');
                    }
                });
        })
    );
});

// Escuchar mensajes para notificaciones
self.addEventListener('message', (event) => {
    if (event.data === 'sync-complete') {
        self.registration.showNotification('Estimación sincronizada', {
            body: 'Datos enviados al servidor.',
            icon: 'icons/Global_Frut.png'
        });
    }
});