var login = {
  // (A) PROCESS LOGIN FORM
  go : () => {
    cb.api({
      mod : "session", req : "login",
      data : {
        email : document.getElementById("login-email").value,
        password : document.getElementById("login-pass").value
      },
      passmsg : false,
      onpass : () => location.href = cbhost.base
    });
    return false;
  },

  // (B) NFC LOGIN
  nfc : () => {
    // (B1) NFC SUPPORTED
    let stat = document.getElementById("nfc-stat");
    if ("NDEFReader" in window) {
      // (B1-1) ON NFC READ
      nfc.onread = evt => {
        // GET TOKEN
        const decoder = new TextDecoder();
        let token = "";
        for (let record of evt.message.records) {
          token = decoder.decode(record.data);
        }
        nfc.standby();

        // API LOGIN
        cb.api({
          mod : "session", req : "intoken",
          data : { token : token },
          passmsg : false,
          onpass : () => location.href = cbhost.base,
          onfail : () => login.nfc()
        });
      };

      // (B1-2) ON NFC READ ERROR
      nfc.onerror = err => {
        nfc.stop();
        console.error(err);
        cb.modal("ERROR", err.msg);
      };

      // (B1-3) START SCAN
      stat.className = "form-control text-white bg-success";
      stat.value = "Ready - Scan token to login";
      nfc.scan();
    }

    // (B2) NFC NOT SUPPORTED
    else {
      stat.className = "form-control text-white bg-danger";
      stat.value = "NFC not supported";
    }
  }
};
window.addEventListener("load", login.nfc);