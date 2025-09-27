const CACHE_NAME = 'globalfrut-cache-v1';
const urlsToCache = [
    '/',
    'index.php',
    'manifest.json',
    'icons/Global_Frut.png',
    'icons/logoglobal.png',
    'js/buscar_huertas.js',
    'js/buscar_municipio.js',
    'js/offline.js',
    'controller/db_gramaje_tipo.php',
    'controller/db_buscar_huertas.php',
    'controller/db_formulario.php',
    'controller/db_cargar_precios.php',
    'https://cdn.jsdelivr.net/npm/chart.js'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME).map(name => caches.delete(name))
            );
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