// (A) CREATE/INSTALL CACHE
self.addEventListener("install", evt => {
  self.skipWaiting();
  evt.waitUntil(
    caches.open("StorageBoxx")
    .then(cache => cache.addAll([
      "assets/CB-autocomplete.js",
      "assets/PAGE-batch-check.js",
      "assets/PAGE-batch.js",
      "assets/PAGE-cb.js",
      "assets/PAGE-check.js",
      "assets/PAGE-forgot.js",
      "assets/PAGE-home.js",
      "assets/PAGE-import.js",
      "assets/PAGE-items-check.js",
      "assets/PAGE-items.js",
      "assets/PAGE-login.js",
      "assets/PAGE-move.js",
      "assets/PAGE-nfc.js",
      "assets/PAGE-push.js",
      "assets/PAGE-settings.js",
      "assets/PAGE-sup-items.js",
      "assets/PAGE-sup.js",
      "assets/PAGE-users.js",
      "assets/PAGE-wa-helper.js",
      "assets/PAGE-wa.js",
      "assets/bootstrap.bundle.min.js",
      "assets/bootstrap.bundle.min.js.map",
      "assets/csv.min.js",
      "assets/html5-qrcode.min.js",
      "assets/qrcode.min.js",
      "assets/PAGE-qrscan.css",
      "assets/bootstrap.min.css",
      "assets/bootstrap.min.css.map",
      "assets/favicon.png",
      "assets/ico-512.png",
      "assets/head-storage-boxx.webp",
      "assets/users.webp",
      // @TODO - ADD MORE OF YOUR OWN TO CACHE
    ]))
    .catch(err => console.error(err))
  );
});

// (B) CLAIM CONTROL INSTANTLY
self.addEventListener("activate", evt => self.clients.claim());

// (C) LOAD FROM CACHE FIRST, FALLBACK TO NETWORK IF NOT FOUND
self.addEventListener("fetch", evt => evt.respondWith(
  caches.match(evt.request).then(res => res || fetch(evt.request))
));

// (D) LISTEN TO PUSH NOTIFICATIONS
self.addEventListener("push", evt => {
  const data = evt.data.json();
  self.registration.showNotification(data.title, {
    body: data.body,
    icon: data.icon,
    image: data.image
  });
});