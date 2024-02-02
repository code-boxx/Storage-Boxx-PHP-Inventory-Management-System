var wa = {
  // (A) INIT
  hReg : null,
  hUnreg : null,
  hTxt : null,
  init : () => {
    wa.hReg = document.getElementById("wa-reg");
    wa.hUnreg = document.getElementById("wa-unreg");
    wa.hTxt = document.getElementById("wa-txt");

    if ("credentials" in navigator) {
      wa.hReg.disabled = false;
    } else {
      wa.hTxt.innerHTML = "<i class='ico-sm icon-sad2'></i> Web Authentication not supported on your device.";
      wa.hTxt.classList.remove("d-none");
    }
  },

  // (B) REGISTER PART A
  regA : () => cb.api({
    mod : "session", act : "waregA",
    passmsg : false,
    onpass : async res => {
      let pk = JSON.parse(res.data);
      helper.bta(pk);
      wa.regB(await navigator.credentials.create(pk));
    }
  }),

  // (C) REGISTER PART B
  regB : cred => cb.api({
    mod : "session", act : "waregB",
    data : {
      transport : cred.response.getTransports ? cred.response.getTransports() : null,
      client : cred.response.clientDataJSON ? helper.atb(cred.response.clientDataJSON) : null,
      attest : cred.response.attestationObject ? helper.atb(cred.response.attestationObject) : null
    },
    passmsg : "Passwordless login registered",
    onpass : () => wa.hUnreg.disabled = false
  }),

  // (D) UNREGISTER
  unreg : () => cb.api({
    mod : "session", act : "waunreg",
    passmsg : "Passwordless login unregistered",
    onpass : () => wa.hUnreg.disabled = true
  })
};

// (E) INIT WEBAUTHN
window.addEventListener("load", wa.init);