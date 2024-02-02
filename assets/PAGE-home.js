var pusher = {
  // (A) PROPERTIES
  hStat : null, // html status
  worker : null, // registered service worker
  sub : null, // push notification subscription

  // (B) SHOW MESSAGE
  msg : (txt, good) => {
    pusher.hStat.classList.remove("bg-white");
    pusher.hStat.classList.add("text-white");
    if (good) {
      pusher.hStat.classList.add("bg-success");
    } else {
      pusher.hStat.classList.add("bg-danger");
    }
    pusher.hStat.innerHTML = `<i class="ico-sm icon-${good?"checkmark":"warning"}"></i> ${txt}`;
  },

  // (C) INIT
  init : async () => {
    // (C1) GET HTML STATUS
    pusher.hStat = document.getElementById("push-stat");

    // (C2) FEATURE CHECK
    if (!("serviceWorker" in navigator)) {
      pusher.msg("Service worker not supported.");
      return;
    }
    if (!("Notification" in window)) {
      pusher.msg("Push notifications not supported.");
      return;
    }

    // (C3) PUSH NOTIFICATIONS SETUP
    navigator.serviceWorker.ready.then(reg => {
      pusher.worker = reg;
      if (Notification.permission == "default") {
        Notification.requestPermission()
        .then(perm => {
          if (perm == "granted") { pusher.reg(); }
          else { pusher.msg("Notifications denied - Manually enable permissions to allow low stock warning."); }
        })
        .catch(err => pusher.msg("ERROR - " + err.message))
      } else if (Notification.permission == "granted") {
        pusher.reg();
      } else {
        pusher.msg("Notifications denied - Manually enable permissions to allow low stock warning.");
      }
    })
    .catch(err => {
      pusher.msg("ERROR - " + err.message);
      console.error(err);
    });
  },

  // (D) REGISTER PUSH NOTIFICATIONS
  reg : () => {
    pusher.worker.pushManager.getSubscription()
    .then(sub => {
      if (sub==null) {
        pusher.worker.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: cbvapid
        })
        .then(sub => { pusher.sub = sub; pusher.save(); })
        .catch(err => pusher.msg("ERROR - " + err.message));
      } else { pusher.sub = sub; pusher.save(); }
    })
    .catch(err => pusher.msg("ERROR - " + err.message));
  },

  // (E) UPDATE SERVER SUBSCRIPTION
  save : () => cb.api({
    mod : "push", act : "save",
    data : {
      endpoint : pusher.sub.endpoint,
      sub : JSON.stringify(pusher.sub)
    },
    passmsg : false,
    onpass : () => pusher.msg("Push notification successfully registered.", 1)
  })
};

// (F) INIT PUSH
window.addEventListener("load", pusher.init);