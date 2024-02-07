var move = {
  // (A) PROPERTIES
  hForm : null, // entire movement form
  hDir : null, hQty : null, hNote : null, hSKU : null, // direction, qty, notes, sku
  hlDir : null, hlQty : null, hlNote : null, hlSKU : null, // last entry

  // (B) INIT
  init : () => {
    // (B1) GET HTML FIELDS
    move.hForm = document.getElementById("mvt-form");
    move.hDir = document.getElementById("mvt-direction");
    move.hQty = document.getElementById("mvt-qty");
    move.hNote = document.getElementById("mvt-notes");
    move.hSKU = document.getElementById("mvt-sku");
    move.hlDir = document.getElementById("last-mvt");
    move.hlQty = document.getElementById("last-qty");
    move.hlNote = document.getElementById("last-notes");
    move.hlSKU = document.getElementById("last-sku");
    
    // (B2) INIT NFC
    if ("NDEFReader" in window) {
      document.getElementById("nfc-btn").disabled = false;
      nfc.init(sku => {
        move.hSKU.value = sku;
        if (move.hForm.checkValidity()) { move.save(); }
        else { move.hForm.reportValidity(); }
      });
    }

    // (B3) INIT AUTOCOMPLETE
    autocomplete.attach({
      target : document.getElementById("mvt-sku"),
      mod : "autocomplete", act : "sku",
      onpick : () => {
        if (move.hForm.checkValidity()) { move.save(); }
        else { move.hForm.reportValidity(); }
      }
    });
  },

  // (C) "SWITCH ON" QR SCANNER
  qron : () => {
    if (qrscan.scanner==null) {
      qrscan.init(txt => {
        move.hSKU.value = txt;
        if (move.hForm.checkValidity()) { move.save(); }
        else { move.hForm.reportValidity(); }
      });
    }
    qrscan.show();
  },

  // (D) SAVE MOVEMENT
  save : () => {
    cb.api({
      mod : "move", act : "saveM",
      data : {
        sku : move.hSKU.value,
        direction : move.hDir.value,
        qty : move.hQty.value,
        notes : move.hNote.value 
      },
      passmsg : "Stock Movement Saved",
      onpass : res => {
        move.hlQty.innerHTML = move.hQty.value;
        move.hlSKU.innerHTML = move.hSKU.value;
        move.hlDir.innerHTML = move.hDir.options[move.hDir.selectedIndex].text;
        move.hlNote.innerHTML = move.hNote.value;
        move.hForm.reset();
      }
    });
    return false;
  }
};

// (E) INIT MANAGE ITEM MOVEMENT
window.addEventListener("load", move.init);