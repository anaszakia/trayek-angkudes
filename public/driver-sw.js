const CACHE_NAME = 'trayek-driver-v1';
const STATIC_ASSETS = [
    '/driver-manifest.json',
    '/images/brand/logo/logo-icon.svg'
];
const GPS_QUEUE = 'trayek-gps-queue';

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method === 'POST' && url.pathname.startsWith('/driver/trips/') && url.pathname.endsWith('/location')) {
        event.respondWith(networkOrQueue(request));
        return;
    }

    if (request.method === 'GET' && (request.destination === 'script' || request.destination === 'style' || request.destination === 'image')) {
        event.respondWith(caches.match(request).then((cached) => cached || fetch(request).then((response) => {
            const copy = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
            return response;
        })));
    }
});

async function networkOrQueue(request) {
    try {
        return await fetch(request.clone());
    } catch (error) {
        await enqueue(request);
        return new Response(JSON.stringify({ queued: true, message: 'Lokasi disimpan dan akan dikirim saat online.' }), {
            status: 202,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

async function enqueue(request) {
    const body = await request.clone().text();
    const headers = {};
    request.headers.forEach((value, key) => { headers[key] = value; });
    const database = await openDatabase();
    await new Promise((resolve, reject) => {
        const transaction = database.transaction(GPS_QUEUE, 'readwrite');
        transaction.objectStore(GPS_QUEUE).add({ url: request.url, method: request.method, headers, body });
        transaction.oncomplete = resolve;
        transaction.onerror = () => reject(transaction.error);
    });
    if ('sync' in self.registration) await self.registration.sync.register('sync-gps');
}

self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-gps') event.waitUntil(syncQueue());
});

async function syncQueue() {
    const database = await openDatabase();
    const items = await new Promise((resolve, reject) => {
        const transaction = database.transaction(GPS_QUEUE, 'readonly');
        const request = transaction.objectStore(GPS_QUEUE).getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
    for (const item of items) {
        try {
            const response = await fetch(item.url, { method: item.method, headers: item.headers, body: item.body, credentials: 'include' });
            if (response.ok || response.status === 409) await removeQueueItem(database, item.id);
        } catch (error) {
            break;
        }
    }
}

function removeQueueItem(database, id) {
    return new Promise((resolve, reject) => {
        const transaction = database.transaction(GPS_QUEUE, 'readwrite');
        transaction.objectStore(GPS_QUEUE).delete(id);
        transaction.oncomplete = resolve;
        transaction.onerror = () => reject(transaction.error);
    });
}

function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('trayek-driver', 1);
        request.onupgradeneeded = () => request.result.createObjectStore(GPS_QUEUE, { keyPath: 'id', autoIncrement: true });
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}
