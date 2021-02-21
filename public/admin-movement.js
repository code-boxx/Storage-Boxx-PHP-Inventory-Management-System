var move = {
  // (A) SAVE MOVEMENT
  save : function () {
    common.ajax({
      url : urlapi + "Inventory",
      data : {
        reqA : "move",
        sku : document.getElementById("mvt-sku").value,
        direction : document.getElementById("mvt-direction").value,
        qty : document.getElementById("mvt-qty").value,
        notes : document.getElementById("mvt-notes").value
      },
      apass : "Quantity updated",
      onpass : function (res) {
        var results = document.getElementById("mvt-result"),
            qty = document.getElementById("mvt-qty"),
            notes = document.getElementById("mvt-notes"),
            sku = document.getElementById("mvt-sku"),
            row = document.createElement("div");
        row.innerHTML = `New quantity for ${sku.value} is ${res.data}.`;
        row.className = "bar";
        results.appendChild(row);
        qty.value = "1.00";
        notes.value = "";
        sku.value = "";
        console.log(res);
      }
    });
    return false;
  }
};

// (B) AUTOCOMPLETE SKU
window.addEventListener("load", function(){
  ac.attach({
    target : "mvt-sku",
    data : urlapi + "Inventory",
    post : { reqA : "findSKU" }
  });
});