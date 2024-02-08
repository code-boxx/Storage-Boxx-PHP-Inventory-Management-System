if ("serviceWorker" in navigator) {
  // (A) REGISTER SERVICE WORKER
  navigator.serviceWorker.register(cbhost.base+"CB-worker.js", {scope: cbhost.basepath});

  // (B) UPDATE CACHE
  if (cbcache.s > cbcache.c) {
    // (B1) GET FILES LIST FROM SERVER
    fetch(cbhost.base+"CB-cache-files.json")
    .then(r => r.json())
    .then(async f => {
      // (B2) DELETE OLD CACHE
      if (await caches.has(cbcache.n)) {
        await caches.delete(cbcache.n);
      }

      // (B3) UPDATE CACHE
      (await caches.open(cbcache.n)).addAll(f);
      localStorage.setItem("CBCACHE", cbcache.s);
    });
  }
}