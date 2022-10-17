var nfc = {
  // (A) INITIALIZE WEB NFC
  ndef : null, ctrl : null, // ndef object
  onread : null, onwrite : null, onerror : null, // functions to run on read, write, error
  init : () => {
    nfc.stop();
    nfc.ctrl = new AbortController();
    nfc.ndef = new NDEFReader();
  },

  // (B) STOP - MISSION ABORT
  stop : () => { if (nfc.ndef!=null) {
    nfc.ctrl.abort();
    nfc.ndef = null;
    nfc.ctrl = null;
  }},

  // (C) STANDBY - SCAN & DO NOTHING
  standby : () => {
    nfc.init();
    nfc.ndef.onreading = null;
    nfc.ndef.onreadingerror = null;
    nfc.ndef.scan({ signal: nfc.ctrl.signal });
  },

  // (D) SCAN NFC TAG
  scan : () => {
    nfc.init();
    nfc.ndef.scan({ signal: nfc.ctrl.signal })
    .then(() => {
      if (nfc.onread!=null) { nfc.ndef.onreading = nfc.onread; }
      if (nfc.onerror!=null) { nfc.ndef.onreadingerror = nfc.onerror; }
    })
    .catch(err => { if (nfc.onerror!=null) { nfc.onerrorerr(err); }  });
  },

  // (E) WRITE NFC TAG
  write : data => {
    nfc.init();
    nfc.ndef.write(data, { signal: nfc.ctrl.signal })
    .then(() => { if (nfc.onwrite!=null) { nfc.onwrite(); } })
    .catch(err => { if (nfc.onerror!=null) { nfc.onerrorerr(err); }  });
  },

  // (F) CREATE READ-ONLY NFC TAG
  readonly : () => {
    nfc.init();
    nfc.ndef.makeReadOnly({ signal: nfc.ctrl.signal })
    .then(() => { if (nfc.onwrite!=null) { nfc.onwrite(); } })
    .catch(err => { if (nfc.onerror!=null) { nfc.onerrorerr(err); }  });
  }
};