// (A) FILES TO CACHE
const cName = "storageboxx",
cFiles = [
  // (A1) BOOTSTRAP
  "assets/bootstrap.bundle.min.js",
  "assets/bootstrap.bundle.min.js.map",
  "assets/bootstrap.min.css",
  "assets/bootstrap.min.css.map",
  // (A2) ICONS + IMAGES
  "assets/favicon.png",
  "assets/ico-512.png",
  "assets/book.jpg",
  // (A3) COMMON INTERFACE
  "assets/PAGE-cb.js",
  "assets/maticon.woff2",
  // (A4) QR CODE
  "assets/html5-qrcode.min.js",
  "assets/qrcode.min.js",
  // (A5) PAGES
  "assets/PAGE-check.js",
  "assets/PAGE-checker.js",
  "assets/PAGE-inventory.js",
  "assets/PAGE-login.js",
  "assets/PAGE-move.js",
  "assets/PAGE-settings.js",
  "assets/PAGE-users.js"
];

// (B) CREATE/INSTALL CACHE
self.addEventListener("install", evt => {
  evt.waitUntil(
    caches.open(cName)
    .then(cache => { return cache.addAll(cFiles); })
    .catch(err => { console.error(err) })
  );
});

// (C) LOAD FROM CACHE FIRST, FALLBACK TO NETWORK IF NOT FOUND
self.addEventListener("fetch", evt => {
  evt.respondWith(
    caches.match(evt.request).then(res => { return res || fetch(evt.request); })
  );
});