var inv = {
  // (A) LIST () : SHOW ALL ITEMS
  pg : 1, // current page
  find : "", // current search
  list : () => {
    cb.page(0);
    cb.load({
      page : "inventory/list", target : "inv-list",
      data : {
        page : inv.pg,
        search : inv.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=inv.pg) {
    inv.pg = pg;
    inv.list();
  }},

  // (C) SEARCH INVENTORY
  search : () => {
    inv.find = document.getElementById("inv-search").value;
    inv.pg = 1;
    inv.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  // sku : item SKU, for edit only
  addEdit : sku => cb.load({
    page : "inventory/form", target : "cb-page-2",
    data : { sku : sku ? sku : "" },
    onload : () => cb.page(1)
  }),

  // (E) RANDOM SKU
  // Credits : https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
  randomSKU : () => {
    let length = 8, // set your own
        result = "",
        char = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",
        clength = char.length;
    for (let i=0; i<length; i++) {
      result += char.charAt(Math.floor(Math.random() * clength));
    }
    document.getElementById("inv-sku").value = result;
  },

  // (F) SAVE ITEM
  save : () => {
    // (F1) GET DATA
    var data = {
      sku : document.getElementById("inv-sku").value,
      name : document.getElementById("inv-name").value,
      unit : document.getElementById("inv-unit").value,
      desc : document.getElementById("inv-desc").value,
      low : document.getElementById("inv-low").value
    };
    var osku = document.getElementById("inv-osku").value;
    if (osku!="") { data.osku = osku; }

    // (F2) AJAX
    cb.api({
      mod : "inventory", req : "save",
      data : data,
      passmsg : "Item saved",
      onpass : inv.list
    });
    return false;
  },

  // (G) DELETE ITEM
  //  sku : item SKU
  //  confirm : boolean, confirmed delete
  del : sku => cb.modal("Please confirm", `Delete ${sku}? All movement history will be lost!`, () => cb.api({
    mod : "inventory", req : "del",
    data : { sku : sku },
    passmsg : "Item Deleted",
    onpass : inv.list
  })),

  // (H) GENERATE QR CODE
  qrcode : (sku, name) => window.open(cbhost.base + "qrcode/?sku="+sku+"&name="+name),

  // (I) SHOW WRITE NFC SCREEN
  nfcShow : sku => {
    if ("NDEFReader" in window) {
      nfc.standby();
      cb.load({
        page : "inventory/nfc", target : "cb-page-2",
        data : { sku : sku ? sku : "" },
        onload : () => {
          nfc.onwrite = () => {
            inv.nfcLog(1, `Done - SKU "${sku}" written to tag`);
            nfc.standby();
          };
          nfc.onerror = err => {
            console.error(err);
            inv.nfcLog(0, "ERROR - " + err.message);
            nfc.stop();
          };
          cb.page(1);
          nfc.write(sku);
          inv.nfcLog(1, `Ready - Tap NFC tag to write SKU "${sku}"`);
        }
      });
    } else {
      cb.modal("Error", "Web NFC is not supported in your browser/device.");
    }
  },

  // (J) SHOW "WRITE NFC TAG" STATUS ON SCREEN
  nfcLog : (status, msg) => {
    let hLog = document.getElementById("nfc-stat");
    if (status == 1) {
      hLog.classList.remove("bg-danger");
      hLog.classList.add("bg-success");
    } else {
      hLog.classList.remove("bg-success");
      hLog.classList.add("bg-danger");
    }
    hLog.innerHTML = msg;
  },

  // (K) END WRITE NFC SESSION
  nfcBack : () => {
    nfc.stop();
    cb.page(0);
  }
};
window.addEventListener("load", inv.list);