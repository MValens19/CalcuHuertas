const CACHE_NAME = 'globalfrut-cache-v1';
const urlsToCache = [
  '/',                  // raíz, si usas index.html
  'index.php',        // archivo HTML principal
  'manifest.json',
  'icons/Global_Frut.png',
  'icons/logoglobal.png',
  // CDN de Tailwind (opcional, mejor descargar local)
  'https://cdn.tailwindcss.com',

  // Si tienes otros scripts o CSS locales, agregarlos aquí
];

// Instalación: guardar los recursos en caché
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
      .then(() => self.skipWaiting())
  );
});

// Activación: borrar cachés antiguas si existen
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

// Interceptar solicitudes y responder con caché o red
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request)
        .catch(() => {
          // Opcional: respuesta por defecto offline si falla la red y no hay caché
          if (event.request.destination === 'document') {
            return caches.match('index.php');
          }
        });
    })
  );
});
