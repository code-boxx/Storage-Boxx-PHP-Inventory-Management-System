var inv = {
  // (A) LIST () : SHOW ALL ITEMS
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(0); }
    cb.load({
      page : "inventory/list", target : "inv-list",
      data : {
        page : inv.pg,
        search : inv.find
      },
      onload : () => { if (!("NDEFReader" in window)) {
        for (let r of document.querySelectorAll("#inv-list .nfcshow")) {
          r.classList.add("d-none");
        }
      }}
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
  //  sku : item SKU, for edit only
  addEdit : sku => cb.load({
    page : "inventory/form", target : "cb-page-2",
    data : { sku : sku ? sku : "" },
    onload : () => cb.page(1)
  }),

  // (E) RANDOM SKU
  // credits : https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
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
    else { data.stock = document.getElementById("inv-stock").value; }

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
  hnStat : null, // html write nfc button status
  nfcSKU : null, // current sku to write
  nfcShow : sku => {
    cb.load({
      page : "inventory/nfc", target : "cb-page-2",
      data : { sku : sku ? sku : "" },
      onload : () => {
        inv.nfcSKU = sku;
        inv.hnStat = document.getElementById("nfc-stat");
        cb.page(1);
        inv.nfcWrite();
      }
    });
  },

  // (J) START WRITE NFC TAG
  nfcWrite : () => {
    // (J1) ON SUCCESSFUL NFC WRITE
    nfc.onwrite = () => {
      nfc.standby();
      cb.modal("Successful", "Click on the button again if you want to write another tag.");
      inv.hnStat.innerHTML = "Done";
    };

    // (J2) ON FAILED NFC WRITE
    nfc.onerror = err => {
      nfc.stop();
      console.error(err);
      cb.modal("ERROR", err.msg);
      inv.hnStat.innerHTML = "ERROR!";
    };

    // (J3) START NFC WRITE
    nfc.write(inv.nfcSKU);
    inv.hnStat.innerHTML = "Ready - Tap NFC tag to write";
  },

  // (K) END WRITE NFC SESSION
  nfcBack : () => {
    nfc.stop();
    cb.page(0);
  }
};
window.addEventListener("load", inv.list);