var pur = {
  // (A) SHOW ALL PURCHASES
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(1); }
    cb.load({
      page : "purchase/list", target : "pur-list",
      data : {
        page : pur.pg,
        search : pur.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=pur.pg) {
    pur.pg = pg;
    pur.list();
  }},

  // (C) SEARCH PURCHASES
  search : () => {
    pur.find = document.getElementById("pur-search").value;
    pur.pg = 1;
    pur.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  //  id : purchase id, for edit only
  iList : null, // current items for the order
  addEdit : id => cb.load({
    page : "purchase/form", target : "cb-page-2",
    data : { id : id ? id : "" },
    onload : () => {
      // (D1) RESET ITEMS LIST
      pur.iList = {};

      // (D2) DRAW ITEMS
      if (id) {
        let hitems = document.getElementById("pur-items-data"),
            items = JSON.parse(hitems.innerHTML);
        hitems.remove();
        items.forEach(i => pur.addItem(...i));
      }

      // (D3) ATTACH SUPPLIER AUTOCOMPLETE
      var hsupname = document.getElementById("sup-name");
      if (!hsupname.disabled) {
        autocomplete.attach({
          target : hsupname,
          mod : "autocomplete", act : "sup",
          data : { more : 1 },
          onpick : sup => {
            hsupname.value = sup.n;
            sup.v = JSON.parse(sup.v);
            document.getElementById("sup-id").value = sup.v.i;
            pur.csup(false);
          }
        });
      }

      // (D4) ATTACH ADD ITEM AUTOCOMPLETE
      autocomplete.attach({
        target : document.getElementById("add-item"),
        mod : "autocomplete", act : "supitem",
        data : {
          more : 1,
          sid : document.getElementById("sup-id")
        },
        onpick : item => {
          document.getElementById("add-item").value = "";
          item = JSON.parse(item.v);
          pur.addItem(item.s, item.ss, item.n, item.u, item.p, 1);
        }
      });

      // (D5) ATTACH NFC ADD ITEM
      if ("NDEFReader" in window) {
        nfc.init(pur.addGet);
        if (id) { document.getElementById("nfc-btn").disabled = false; }
      }

      // (D6) SHOW PURCHASE ORDER PAGE
      cb.page(2);
    }
  }),

  // (E) TOGGLE SUPPLIER CHANGE
  csup : reset => {
    // (E1) GET HTML ELEMENTS
    let n = document.getElementById("sup-name"),
        i = document.getElementById("sup-id"),
        c = document.getElementById("sup-change"),
        it = document.getElementById("pur-items"),
        ait = document.getElementById("add-item"),
        aqr = document.getElementById("qr-btn"),
        anfc = document.getElementById("nfc-btn");

    // (E2) RESET SUPPLIER
    if (reset) {
      n.value = "";
      n.disabled = false;
      i.value = "";
      c.classList.add("d-none");
      pur.iList = {};
      it.innerHTML = "";
      ait.disabled = true;
      aqr.disabled = true;
      if ("NDEFReader" in window) { anfc.disabled = true; }
    }

    // (E3) SET SUPPLIER
    else {
      n.disabled = true;
      c.classList.remove("d-none");
      ait.disabled = false;
      aqr.disabled = false;
      if ("NDEFReader" in window) { anfc.disabled = false; }
    }
  },

  // (F) ADD ITEM ROW
  addItem : (sku, ssku, name, unit, price, qty) => {
    // (F1) CHECK DUPLICATE ITEM
    if (pur.iList[sku]) {
      cb.modal("Already Added", `[${sku}] ${name} is already added.`);
    }
    
    // (F2) ADD NEW ROW
    else {
      // (F2-1) ITEM ROW HTML
      let row = document.createElement("div");
      row.className = "iRow d-flex align-items-center border p-2";
      row.innerHTML = 
      `<i class="text-danger icon-cross p-3" onclick="pur.delItem(this, '${sku}')"></i>
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
      </div>`;

      // (F2-2) SORTABLE
      row.draggable = true;
      row.ondragstart = () => pur.ddfrom = row;
      row.ondragover = e => e.preventDefault();
      row.ondrop = pur.isort;

      // (F2-3) APPEND TO LIST
      document.getElementById("pur-items").appendChild(row);
      pur.iList[sku] = 1;
    }
  },

  // (G) DRAG-N-DROP SORT ITEM
  ddfrom : null, // current item being dragged
  ddto : null, // dropped at this item
  ddget : r => r.classList.contains("iRow") ? r : pur.ddget(r.parentElement), // get proper drop target
  isort : e => {
    // (G1) GET ELEMENTS
    e.preventDefault();
    let iList = document.getElementById("pur-items"),
        iAll = iList.querySelectorAll(".iRow");
    pur.ddto = pur.ddget(e.target);

    // (G2) REORDER ITEM
    if (iAll.length>1 && pur.ddfrom!=pur.ddto) {
      let currentpos = 0, droppedpos = 0;
      for (let i=0; i<iAll.length; i++) {
        if (pur.ddfrom == iAll[i]) { currentpos = i; }
        if (pur.ddto == iAll[i]) { droppedpos = i; }
      }
      if (currentpos < droppedpos) {
        iList.insertBefore(pur.ddfrom, pur.ddto.nextSibling);
      } else {
        iList.insertBefore(pur.ddfrom, pur.ddto);
      }
    }
  },

  // (H) REMOVE ITEM ROW
  delItem : (row, sku) => {
    row.parentElement.remove();
    delete pur.iList[sku];
  },

  // (I) GET ITEM FROM SERVER & ADD TO LIST
  addGet : sku => cb.api({
    mod : "suppliers", act : "getItem",
    data : {
      id : document.getElementById("sup-id").value,
      sku : sku
    },
    passmsg : false,
    onpass : res => {
      // (I1) INVALID SKU
      if (res.data==null) {
        cb.modal("Invalid Item", `${sku} is not found in the database, or supplier does not have this item.`);
      }

      // (I2) OK - ADD ITEM
      else {
        let i = res.data;
        pur.addItem(i.item_sku, i.sup_sku, i.item_name, i.item_unit, i.unit_price, 1);
      }
    }
  }),

  // (J) ADD ITEM WITH QR CODE
  addQR : () => {
    if (qrscan.scanner==null) { qrscan.init(pur.addGet); }
    qrscan.show();
  },

  // (K) SAVE PURCHASE
  save : () => {
    // (K1) GET DATA
    var data = {
      sid : document.getElementById("sup-id").value,
      name : document.getElementById("p-name").value,
      tel : document.getElementById("p-tel").value,
      email : document.getElementById("p-email").value,
      address : document.getElementById("p-address").value,
      date : document.getElementById("p-date").value,
      notes : document.getElementById("p-notes").value
    };
    if (data.sid=="") {
      cb.modal("No supplier specified", "Please select a supplier.");
      return false;
    }
    var id = document.getElementById("p-id").value;
    if (id!="") {
      data.id = id;
      data.stat = document.getElementById("p-stat").value;
    }

    // (K2) GET ITEMS
    let items = document.querySelectorAll("#pur-items .iRow");
    if (items.length==0) {
      cb.modal("No Items", "Please add at least one item.");
      return false;
    }
    data.items = [];
    // sku | price | qty
    for (let i of items) {
      data.items.push([
        i.querySelector(".iSKU").innerHTML,
        i.querySelector(".iPrice").value,
        i.querySelector(".iQty").value
      ]);
    }
    data.items = JSON.stringify(data.items);

    // (K3) AJAX
    cb.api({
      mod : "purchase", act : "save",
      data : data,
      passmsg : "Order saved",
      onpass : pur.list
    });
    return false;
  },

  // (L) PRINT PURCHASE ORDER
  print : id => {
    document.getElementById("pur-print-id").value = id;
    document.getElementById("pur-print").submit();
  }
};

// (M) INIT MANAGE PURCHASES
window.addEventListener("load", () => {
  // (M1) EXTRA STYLES FOR "ADD/EDIT ITEMS LIST"
  document.head.appendChild(document.createElement("style")).innerHTML=".iQty,.iPrice{width:80px}";

  // (M2) LIST PURCHASES
  pur.list();

  // (M3) ATTACH AUTOCOMPLETE
  autocomplete.attach({
    target : document.getElementById("pur-search"),
    mod : "autocomplete", act : "purchase",
    onpick : res => pur.search()
  });
});