var dlv = {
  // (A) SHOW ALL DELIVERIES
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(1); }
    cb.load({
      page : "deliver/list", target : "dlv-list",
      data : {
        page : dlv.pg,
        search : dlv.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=dlv.pg) {
    dlv.pg = pg;
    dlv.list();
  }},

  // (C) SEARCH DELIVERIES
  search : () => {
    dlv.find = document.getElementById("dlv-search").value;
    dlv.pg = 1;
    dlv.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  //  id : delivery id, for edit only
  iList : null, // current items for the order
  addEdit : id => cb.load({
    page : "deliver/form", target : "cb-page-2",
    data : { id : id ? id : "" },
    onload : () => {
      // (D1) RESET ITEMS LIST
      dlv.iList = {};

      // (D2) DRAW ITEMS
      if (id) {
        let hitems = document.getElementById("dlv-items-data"),
            items = JSON.parse(hitems.innerHTML);
        hitems.remove();
        items.forEach(i => dlv.addItem(...i));
      }

      // (D3) ATTACH CUSTOMER AUTOCOMPLETE
      autocomplete.attach({
        target : document.getElementById("d-name"),
        mod : "autocomplete", act : "cus",
        data : { more : 1 },
        onpick : cus => {
          document.getElementById("d-name").value = cus.n;
          cus.v = JSON.parse(cus.v);
          [
            ["d-tel", "t"],
            ["d-email", "e"],
            ["d-address", "a"],
          ].forEach(r => {
            let field = document.getElementById(r[0]);
            if (field.value=="") { field.value = cus.v[r[1]]; }
          });
        }
      });

      // (D4) ATTACH ADD ITEM AUTOCOMPLETE
      autocomplete.attach({
        target : document.getElementById("add-item"),
        mod : "autocomplete", act : "item",
        data : { more : 1 },
        onpick : item => {
          document.getElementById("add-item").value = "";
          item = JSON.parse(item.v);
          dlv.addItem(item.s, item.n, item.u, item.p, 1);
        }
      });

      // (D5) ATTACH NFC ADD ITEM
      if ("NDEFReader" in window) {
        document.getElementById("nfc-btn").disabled = false;
        nfc.init(dlv.addGet);
      }

      // (D6) SHOW DELIVERY ORDER PAGE
      cb.page(2);
    }
  }),

  // (E) ADD ITEM ROW
  addItem : (sku, name, unit, price, qty) => {
    // (E1) CHECK DUPLICATE ITEM
    if (dlv.iList[sku]) {
      cb.modal("Already Added", `[${sku}] ${name} is already added.`);
    }
    
    // (E2) ADD NEW ROW
    else {
      let row = document.createElement("div");
      row.innerHTML = `<div class="iRow d-flex align-items-center border p-2">
        <i class="text-danger icon-cross p-3" onclick="dlv.delItem(this, '${sku}')"></i>
        <div class="flex-grow-1">
          <strong class="iSKU">${sku}</strong>
          <div class="iName">${name}</div>
        </div>
        <div class="form-floating">
          <input class="form-control mx-1 iQty" type="number" step="0.01" min="0.01" required value="${qty}">
          <label class="iUnit">${unit}</label>
        </div>
        <div class="form-floating">
          <input class="form-control iPrice" type="number" step="0.01" min="0" required value="${price}">
          <label>PRICE</label>
        </div>
      </div>`;
      document.getElementById("dlv-items").appendChild(row);
      dlv.iList[sku] = 1;
    }
  },

  // (F) REMOVE ITEM ROW
  delItem : (row, sku) => {
    row.parentElement.remove();
    delete dlv.iList[sku];
  },

  // (G) GET ITEM FROM SERVER & ADD TO LIST
  addGet : sku => cb.api({
    mod : "items", act : "get",
    data : { sku : sku },
    passmsg : false,
    onpass : res => {
      // (G1) INVALID SKU
      if (res.data==null) {
        cb.modal("Invalid Item", `${sku} is not found in the database.`);
      }

      // (G2) OK - ADD ITEM
      else {
        let i = res.data;
        dlv.addItem(i.item_sku, i.item_name, i.item_unit, i.item_price, 1);
      }
    }
  }),

  // (H) ADD ITEM WITH QR CODE
  addQR : () => {
    if (qrscan.scanner==null) { qrscan.init(dlv.addGet); }
    qrscan.show();
  },

  // (I) SAVE DELIVERY
  save : () => {
    // (I1) GET DATA
    var data = {
      name : document.getElementById("d-name").value,
      tel : document.getElementById("d-tel").value,
      email : document.getElementById("d-email").value,
      address : document.getElementById("d-address").value,
      date : document.getElementById("d-date").value,
      notes : document.getElementById("d-notes").value
    };
    var id = document.getElementById("d-id").value;
    if (id!="") {
      data.id = id;
      data.stat = document.getElementById("d-stat").value;
    }

    // (I2) GET ITEMS
    let items = document.querySelectorAll("#dlv-items .iRow");
    if (items.length==0) {
      cb.modal("No Items", "Please add at least one item.");
      return false;
    }
    data.items = [];
    // sku | name | unit | price | qty
    for (let i of items) {
      data.items.push([
        i.querySelector(".iSKU").innerHTML,
        i.querySelector(".iName").innerHTML,
        i.querySelector(".iUnit").innerHTML,
        i.querySelector(".iPrice").value,
        i.querySelector(".iQty").value
      ]);
    }
    data.items = JSON.stringify(data.items);

    // (I3) AJAX
    cb.api({
      mod : "delivery", act : "save",
      data : data,
      passmsg : "Order saved",
      onpass : dlv.list
    });
    return false;
  },

  // (J) PRINT DELIVERY ORDER
  print : id => {
    document.getElementById("dlv-print-id").value = id;
    document.getElementById("dlv-print").submit();
  }
};

// (K) INIT MANAGE DELIVERIES
window.addEventListener("load", () => {
  // (K1) EXTRA STYLES FOR "ADD/EDIT ITEMS LIST"
  document.head.appendChild(document.createElement("style")).innerHTML=".iQty,.iPrice{width:80px}";

  // (K2) LIST DELIVERIES
  dlv.list();

  // (K3) ATTACH AUTOCOMPLETE
  autocomplete.attach({
    target : document.getElementById("dlv-search"),
    mod : "autocomplete", act : "deliver",
    onpick : res => dlv.search()
  });
});