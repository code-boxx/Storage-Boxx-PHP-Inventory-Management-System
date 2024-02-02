var nfc = {
  // (A) PROPERTIES
  ndef : null, ctrl : null,      // ndef object
  ondone : null, onerror : null, // functions to call on complete/error

  // (B) INIT WEB NFC SCANNER/WRITER
  init : (ondone, onerror) => {
    // (B1) ATTACH HTML
    if (document.getElementById("nfc-wrapA") == null) {
      let nScan = document.createElement("div");
      nScan.id = "nfc-wrapA";
      nScan.className = "scannerA d-none tran-zoom bg-dark";
      nScan.innerHTML = `<div id="nfc-wrapB" class="scannerB">
        <div id="nfc-wrapC" class="bg-success">
          <div><i class="icon-feed"></i></div>
          <div>READY - SCAN YOUR NFC TAG</div>
        </div>
        <button type="button" class="mt-4 btn btn-danger d-flex-inline" onclick="nfc.hide()">
          <i class="ico-sm icon-cross"></i> Cancel
        </button>
      </div>`;
      document.body.appendChild(nScan);
    }

    // (B2) SET "POST ACTIONS"
    nfc.ondone = ondone;
    nfc.onerror = onerror;
  },

  // (C) START - MISSION START
  start : () => {
    nfc.ctrl = new AbortController();
    nfc.ndef = new NDEFReader();
  },

  // (D) STOP - MISSION ABORT
  stop : () => { if (nfc.ndef!=null) {
    nfc.ctrl.abort();
    nfc.ndef = null;
    nfc.ctrl = null;
  }},

  // (E) SHOW NFC WRITER/SCANNER
  show : () => cb.transit(() => {
    document.getElementById("nfc-wrapA").classList.remove("d-none");
    document.body.classList.add("overflow-hidden");
  }),

  // (F) HIDE NFC WRITER/SCANNER
  hide : () => {
    cb.transit(() => {
      document.getElementById("nfc-wrapA").classList.add("d-none");
      document.body.classList.remove("overflow-hidden");
    });
  },

  // (G) GENERAL ERROR HANDLER
  catcher : err => {
    nfc.stop(); nfc.hide();
    cb.modal("ERROR", err.msg);
    if (nfc.onerror) { nfc.onerror(err); }
  },

  // (H) WRITE NFC TAG
  write : data => {
    nfc.stop(); nfc.start(); nfc.show();
    nfc.ndef.write(data, { signal: nfc.ctrl.signal })
    .then(() => {
      nfc.stop(); nfc.hide();
      cb.toast(true, "Success", "NFC Tag Created");
      if (nfc.ondone) { nfc.ondone(); }
    })
    .catch(nfc.catcher);
  },

  // (I) SCAN NFC TAG
  scan : () => {
    nfc.stop(); nfc.start(); nfc.show();
    nfc.ndef.scan({ signal: nfc.ctrl.signal })
    .then(() => {
      nfc.ndef.onreading = evt => {
        nfc.stop(); nfc.hide();
        const decoder = new TextDecoder();
        nfc.ondone(decoder.decode(evt.message.records[0].data));
      };
      nfc.ndef.onreadingerror = nfc.catcher;
    })
    .catch(nfc.catcher);
  },

  // (J) CREATE READ-ONLY NFC TAG
  readonly : () => {
    nfc.stop(); nfc.start(); nfc.show();
    nfc.ndef.makeReadOnly({ signal: nfc.ctrl.signal })
    .then(() => {
      nfc.stop(); nfc.hide();
      cb.toast(true, "Success", "NFC Tag Locked");
      if (nfc.ondone) { nfc.ondone(); }
    })
    .catch(nfc.catcher);
  }
};