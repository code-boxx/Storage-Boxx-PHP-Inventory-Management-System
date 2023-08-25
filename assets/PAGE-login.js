// (I) REGULAR LOGIN
function login () {
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
}

// (II) WEB AUTHN LOGIN
var wa = {
  // (A) INIT
  init : () => { if ("credentials" in navigator) {
    document.getElementById("wa-in").disabled = false;
  }},

  // (B) WEBAUTH LOGIN PART A
  go : () => {
    const email = document.getElementById("login-email");
    if (email.validity.valid) {
      cb.api({
        mod : "session", act : "waloginA",
        data : { email: email.value },
        passmsg : false,
        onpass : async res => {
          let pk = JSON.parse(res.data);
          helper.bta(pk);
          console.log(pk);
          wa.login(await navigator.credentials.get(pk));
        }
      });
    } else {
      cb.modal("ERROR", "Please enter a valid email address.")
    }
  },

  // (C) WEBAUTH LOGIN PART B
  login : cred => {
    const email = document.getElementById("login-email");
    cb.api({
      mod : "session", act : "waloginB",
      data : {
        email: email.value,
        id : cred.rawId ? helper.atb(cred.rawId) : null,
        client : cred.response.clientDataJSON  ? helper.atb(cred.response.clientDataJSON) : null,
        auth : cred.response.authenticatorData ? helper.atb(cred.response.authenticatorData) : null,
        sig : cred.response.signature ? helper.atb(cred.response.signature) : null,
        user : cred.response.userHandle ? helper.atb(cred.response.userHandle) : null
      },
      passmsg : false,
      onpass : res => location.href = cbhost.base
    });
  }
};

// (III) NFC LOGIN
var nin = {
  // (A) INITIALIZE - CHECK NFC
  hStatus : null, // html hfc login button text
  init : () => { if ("NDEFReader" in window) {
    nin.hStatus = document.getElementById("nfc-b");
    document.getElementById("nfc-a").disabled = false;
  }},

  // (B) NFC LOGIN
  go : () => {
    // (B1) ON NFC READ
    nfc.onread = evt => {
      // (B1-1) GET TOKEN
      nfc.standby();
      const decoder = new TextDecoder();
      let token = "";
      for (let record of evt.message.records) {
        token = decoder.decode(record.data);
      }

      // (B1-2) API LOGIN
      cb.api({
        mod : "session", act : "nfclogin",
        data : { token : token },
        passmsg : false,
        onpass : () => location.href = cbhost.base,
        onfail : () => nin.go()
      });
    };

    // (B2) ON NFC ERROR
    nfc.onerror = err => {
      nfc.stop();
      console.error(err);
      cb.modal("ERROR", err.msg);
      nin.hStatus.innerHTML = "ERROR!";
    };

    // (B3) START SCAN
    nin.hStatus.innerHTML = "Scanning - Tap Token";
    nfc.scan();
  }
};

// (IV) INIT
window.addEventListener("load", wa.init);
window.addEventListener("load", nin.init);