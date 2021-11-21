var move = {
  // (A) SAVE MOVEMENT
  save : () => {
    // (A1) GET FORM FIELDS
    var sku = document.getElementById("mvt-sku"),
        direction = document.getElementById("mvt-direction"),
        qty = document.getElementById("mvt-qty"),
        notes = document.getElementById("mvt-notes");

    // (A2) SEND TO API
    sb.api({
      mod : "inventory",
      req : "move",
      data : {
        sku : sku.value,
        direction : direction.value,
        qty : qty.value,
        notes : notes.value
      },
      passmsg : "Stock Movement Saved",
      onpass : (res) => {
        // ADD ENTRY TO HISTORY
        let d = {I:"In", O:"Out", T:"Take"};
        move.history(
          `${sku.value} | ${d[direction.value]} ${qty.value}`,
          `New quantity ${res.data}`
        );

        // RESET FORM
        qty.value = "1.00";
        notes.value = "";
        sku.value = "";
      }
    });
    return false;
  },

  // (B) ADD RECENT MOVEMENT HISTORY
  //  title : movement title
  //  txt : movement text
  entries : 7, // max movement history entries
  history : (title, txt) => {
    // (B1) REMOVE OLD ENTRIES
    var target = document.getElementById("mvt-result"),
        all = target.querySelectorAll("li");
    if (all.length == move.entries) {
      target.removeChild(all[move.entries-1]);
    }

    // (B2) ADD NEW ENTRY
    var entry = document.createElement("li");
    entry.className = "list-group-item";
    entry.innerHTML = `<div class="text-primary fw-bold">${title}</div><div>${txt}</div>`;
    target.prepend(entry);
  }
};
