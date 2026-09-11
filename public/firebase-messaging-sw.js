// No Firebase messaging in this app. A browser with a stale registration keeps
// requesting this path; serving a no-op worker stops the recursive 404s.
// ponytail: unregisters self; delete this file once no client has the old registration.
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (event) => {
    event.waitUntil(self.registration.unregister());
});
