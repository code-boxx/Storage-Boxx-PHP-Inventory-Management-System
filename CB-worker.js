// (A) CREATE/INSTALL CACHE
self.addEventListener("install", evt => {
  self.skipWaiting();
  evt.waitUntil(
    caches.open("StorageBoxx")
    .then(cache => cache.addAll([
      // (A1) BOOTSTRAP
      "assets/bootstrap.bundle.min.js",
      "assets/bootstrap.bundle.min.js.map",
      "assets/bootstrap.min.css",
      "assets/bootstrap.min.css.map",
      // (A2) ICONS + IMAGES
      "assets/favicon.png",
      "assets/ico-512.png",
      "assets/login.webp",
      "assets/forgot.webp",
      // (A3) COMMON INTERFACE
      "assets/PAGE-cb.js",
      "assets/PAGE-nfc.js",
      "assets/maticon.woff2",
      "CB-manifest.json",
      // (A4) QR CODE + CSV
      "assets/csv.min.js",
      "assets/html5-qrcode.min.js",
      "assets/qrcode.min.js",
      // (A5) PAGES
      "assets/PAGE-check.js",
      "assets/PAGE-forgot.js",
      "assets/PAGE-home-inv.js",
      "assets/PAGE-home.js",
      "assets/PAGE-inv-check.js",
      "assets/PAGE-inv-import.js",
      "assets/PAGE-inventory.js",
      "assets/PAGE-login.js",
      "assets/PAGE-move.js",
      "assets/PAGE-push.js",
      "assets/PAGE-settings.js",
      "assets/PAGE-sup-import.js",
      "assets/PAGE-sup-items.js",
      "assets/PAGE-sup-items-import.js",
      "assets/PAGE-suppliers.js",
      "assets/PAGE-users.js"
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