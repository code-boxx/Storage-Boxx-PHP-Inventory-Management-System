var move = {
  // (A) PROPERTIES
  // movement form
  hmForm : null, hmSKU : null, hmDir : null, hmQty : null, hmNote : null,
  // last entry
  hlQty : null, hlUnit : null, hlDir : null,  hlSKU : null, hlNote : null,
  // qr scanner
  qrscan : null,
  // nfc scanner
  hnStat : null,

  // (B) INIT
  init : () => {
    // (B1) GET HTML FIELDS
    move.hmForm = document.getElementById("mvt-form");
    move.hmSKU = document.getElementById("mvt-sku");
    move.hmDir = document.getElementById("mvt-direction");
    move.hmQty = document.getElementById("mvt-qty");
    move.hmNote = document.getElementById("mvt-notes");
    move.hlQty = document.getElementById("last-qty");
    move.hlUnit = document.getElementById("last-unit");
    move.hlDir = document.getElementById("last-mvt");
    move.hlSKU = document.getElementById("last-sku");
    move.hlNote = document.getElementById("last-notes");
    move.hnStat = document.getElementById("nfc-stat");

    // (B2) INIT WEBCAM SCANNER
    move.qrscan = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    move.qrscan.render((txt, res) => {
      let buttons = document.querySelectorAll("#reader button");
      buttons[1].click();
      move.hmSKU.value = txt;
      if (move.hmForm.checkValidity()) { window.scrollTo(0, 0); move.save(); }
      else { move.hmForm.reportValidity(); }
    });

    // (B3) INIT NFC
    if ("NDEFReader" in window) {
      // (B3-1) ON SUCCESSFUL NFC READ
      nfc.onread = evt => {
        const decoder = new TextDecoder();
        for (let record of evt.message.records) {
          move.hmSKU.value = decoder.decode(record.data);
        }
        if (move.hmForm.checkValidity()) { move.save(); }
        else { move.hmForm.reportValidity(); }
        nfc.standby();
        move.nfcLog(1, "Click here to scan another NFC tag.");
      };

      // (B3-2) ON NFC READ ERROR
      nfc.onerror = err => {
        nfc.stop();
        console.error(err);
        move.nfcLog(0, "ERROR - " + err.message);
      };

      // (B3-3) CLICK TO (RE)START NFC SCAN
      move.hnStat.onclick = () => {
        nfc.scan();
        move.nfcLog(1, "Ready - Tap NFC tag to scan.");
      };
      move.nfcLog(1, "Click here to start scanning.");
    } else {
      move.nfcLog(0, "Web NFC is not supported on this browser/device.");
    }
  },

  // (C) HELPER - SHOW NFC STATUS MESSAGE ON SCREEN
  nfcLog : (status, msg) => {
    if (status==1) {
      move.hnStat.className = "form-control text-white bg-success";
    } else {
      move.hnStat.className = "form-control text-white bg-danger";
    }
    move.hnStat.value = msg;
  },

  // (D) SAVE MOVEMENT
  save : () => {
    cb.api({
      mod : "inventory", req : "move",
      data : {
        sku : move.hmSKU.value,
        direction : move.hmDir.value,
        qty : move.hmQty.value,
        notes : move.hmNote.value
      },
      passmsg : "Stock Movement Saved",
      onpass : res => {
        move.hlQty.innerHTML = move.hmQty.value;
        move.hlUnit.innerHTML = res.data["stock_unit"];
        move.hlDir.innerHTML = move.hmDir.options[move.hmDir.selectedIndex].text;
        move.hlSKU.innerHTML = `[${move.hmSKU.value}] ${res.data["stock_name"]}`;
        move.hlNote.innerHTML = move.hmNote.value;
        move.hmForm.reset();
      }
    });
    return false;
  }
};
window.addEventListener("load", move.init);