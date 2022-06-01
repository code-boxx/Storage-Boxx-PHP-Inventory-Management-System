var move = {
  // (A) SAVE MOVEMENT
  save: () => {
    // (A1) GET FORM FIELDS
    var sku = document.getElementById("mvt-sku"),
      direction = document.getElementById("mvt-direction"),
      qty = document.getElementById("mvt-qty"),
      notes = document.getElementById("mvt-notes");

    // (A2) SEND TO API
    cb.api({
      mod: "inventory",
      req: "move",
      data: {
        sku: sku.value,
        direction: direction.value,
        qty: qty.value,
        notes: notes.value,
      },
      passmsg: "Stock Movement Saved",
      onpass: (res) => {
        // ADD ENTRY TO HISTORY
        let d = { I: "In", O: "Out", T: "Take" };
        move.history(
          `Item name: ${sku.value}`,
          `Stock option: Stock ${d[direction.value]}`,
          `Quantity value: ${qty.value}`,
          `New quantity: ${res.data}`,
          `Notes: ${notes.value ? notes.value : "-"}`,
        );

        // RESET FORM
        qty.value = "1.00";
        notes.value = "";
        sku.value = "";
      },
    });
    return false;
  },

  // (B) ADD RECENT MOVEMENT HISTORY
  //  title : movement title
  //  txt : movement text
  entries: 7, // max movement history entries
  history: (item, stock, quantity, txt, notes) => {
    // (B1) REMOVE OLD ENTRIES
    var target = document.getElementById("mvt-result"),
      all = target.querySelectorAll("li");
    if (all.length == move.entries) {
      target.removeChild(all[move.entries - 1]);
    }

    // (B2) ADD NEW ENTRY
    var entry = document.createElement("li");
    entry.className = "list-group-item";
    entry.innerHTML = `<div class="text-primary fw-bold">${item}</div><div>${stock}</div><div>${quantity}</div><div>${txt}</div><div>${notes}</div>`;
    target.prepend(entry);
  },
};

// (C) WEBCAM SCANNER
window.addEventListener("DOMContentLoaded", () => {
  var scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
  scanner.render((txt, res) => {
    let buttons = document.querySelectorAll("#reader button");
    buttons[1].click();
    document.getElementById("mvt-sku").value = txt;
    move.save();
  });
});
