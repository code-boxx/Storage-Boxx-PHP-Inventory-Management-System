var move = {
  // (A) PROPERTIES
  // movement form
  hmForm : null, hmSKU : null, hmDir : null, hmQty : null, hmNote : null,
  // last entry
  hlDir : null, hlQty : null, hlSKU : null, hlNote : null,
  // qr scanner
  scanner : null,

  // (B) INIT
  init : () => {
    // (B1) GET HTML FIELDS
    move.hmForm = document.getElementById("mvt-form");
    move.hmSKU = document.getElementById("mvt-sku");
    move.hmDir = document.getElementById("mvt-direction");
    move.hmQty = document.getElementById("mvt-qty");
    move.hmNote = document.getElementById("mvt-notes");
    move.hlDir = document.getElementById("last-mvt");
    move.hlQty = document.getElementById("last-qty");
    move.hlSKU = document.getElementById("last-sku");
    move.hlNote = document.getElementById("last-notes");

    // (B2) INIT SCANNER
    move.scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    move.scanner.render((txt, res) => {
      let buttons = document.querySelectorAll("#reader button");
      buttons[1].click();
      move.hmSKU.value = txt;
      move.save();
    });
  },

  // (C) SAVE MOVEMENT
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
        move.hlDir.innerHTML = move.hmDir.options[move.hmDir.selectedIndex].text;
        move.hlQty.innerHTML = move.hmQty.value;
        move.hlSKU.innerHTML = move.hmSKU.value;
        move.hlNote.innerHTML = move.hmNote.value;
        move.hmForm.reset();
      }
    });
    return false;
  }
};
window.addEventListener("load", move.init);