var pusher = {
  // (A) PROPERTIES
  hStat : null, // html status

  // (B) HTML HELPER - SHOW MESSAGE
  show : (status, msg) => {
    if (status==0) { pusher.hStat.className = "d-flex align-items-center p-3 my-3 text-white bg-danger"; }
    if (status==1) { pusher.hStat.className = "d-flex align-items-center p-3 my-3 text-white bg-success"; }
    pusher.hStat.innerHTML = (status==0 ? "<i class='mi me-1'>close</i>" : "<i class='mi me-1'>done</i>") + msg;
  },

  // (B) INIT - PERMISSION CHECK
  init : () => {
    // (B1) GET HTML WRAPPER
    pusher.hStat = document.getElementById("push-stat");

    // (B2) ASK FOR PERMISSION
    if (Notification.permission === "default") {
      Notification.requestPermission().then(perm => {
        if (Notification.permission === "granted") {
          pusher.reg().catch(e => { pusher.show(0, e.message); });
        } else { pusher.show(0, "Allow notifications to receive low stock warnings."); }
      });
    }

    // (B3) GRANTED
    else if (Notification.permission === "granted") {
      pusher.reg().catch(e => { pusher.show(0, e.message); });
    }

    // (B4) DENIED
    else { pusher.show(0, "Allow notifications to receive low stock warnings."); }
  },
  
  // (C) REGISTER SERVICE WORKER
  reg : async () => {
    // (C1) REGISTER SERVICE WORKER
    const reg = await navigator.serviceWorker.register(cbhost.base + "CB-push-worker.js", { scope: "/" });
    
    // (C2) SUBSCRIBE TO PUSH SERVER
    const sub = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: cbvapid
    });

    // (C3) UPDATE SERVER
    cb.api({
      mod : "push", req : "save",
      data : {
        endpoint : sub.endpoint,
        sub : JSON.stringify(sub)
      },
      passmsg : false,
      onpass : () => { pusher.show(1, "Push notifications ready."); }
    });
  }
};
window.addEventListener("load", pusher.init);