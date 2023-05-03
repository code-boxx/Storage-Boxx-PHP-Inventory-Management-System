var login = {
  // (A) PROCESS LOGIN FORM
  go : () => {
    cb.api({
      mod : "session", act : "login",
      data : {
        email : document.getElementById("login-email").value,
        password : document.getElementById("login-pass").value
      },
      passmsg : false,
      onpass : () => location.href = cbhost.base
    });
    return false;
  },

  // (B) INITIALIZE - CHECK NFC
  hna : null, // html "or click on nfc" message
  hnb : null, // html hfc button
  hnc : null, // html hfc button status
  init : () => { if ("NDEFReader" in window) {
    login.hna = document.getElementById("nfc-login-a");
    login.hnb = document.getElementById("nfc-login-b");
    login.hnc = document.getElementById("nfc-login-c");
    login.hna.classList.remove("d-none");
    login.hnb.classList.remove("d-none");
  }},

  // (C) NFC LOGIN
  nfc : () => {
    // (C1) ON NFC READ
    nfc.onread = evt => {
      // (C1-1) GET TOKEN
      nfc.standby();
      const decoder = new TextDecoder();
      let token = "";
      for (let record of evt.message.records) {
        token = decoder.decode(record.data);
      }

      // (C1-2) API LOGIN
      cb.api({
        mod : "session", act : "intoken",
        data : { token : token },
        passmsg : false,
        onpass : () => location.href = cbhost.base,
        onfail : () => login.nfc()
      });
    };

    // (C2) ON NFC ERROR
    nfc.onerror = err => {
      nfc.stop();
      console.error(err);
      cb.modal("ERROR", err.msg);
      login.hnc.innerHTML = "ERROR!";
    };

    // (C3) START SCAN
    login.hnc.innerHTML = "Scanning - Tap token";
    nfc.scan();
  }
};
window.addEventListener("load", login.init);