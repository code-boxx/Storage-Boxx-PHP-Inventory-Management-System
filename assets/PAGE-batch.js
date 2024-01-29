var batch = {
  // (A) LIST () : SHOW ALL BATCHES
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(1); }
    cb.load({
      page : "batch/list", target : "batch-list",
      data : {
        page : batch.pg,
        search : batch.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=batch.pg) {
    batch.pg = pg;
    batch.list();
  }},

  // (C) SEARCH BATCHES
  search : () => {
    batch.find = document.getElementById("batch-search").value;
    batch.pg = 1;
    batch.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  //  sku : item SKU, for edit only
  //  name : batch name, for edit only
  addEdit : (sku, name) => cb.load({
    page : "batch/form", target : "cb-page-2",
    data : {
      sku : sku ? sku : "",
      name : name ? name : ""
    },
    onload : () => cb.page(2)
  }),

  // (E) USE CURRENT DATE & TIME AS BATCH NAME
  nowName : () =>  document.getElementById("batch-name").value = (new Date).toISOString(),

  // (F) SAVE BATCH
  save : () => {
    // (F1) GET DATA
    var data = {
      sku : document.getElementById("batch-sku").value,
      name : document.getElementById("batch-name").value,
      expire : document.getElementById("batch-expire").value,
      oname : document.getElementById("batch-oname").value
    };
    var qty = document.getElementById("batch-qty");
    if (qty != null) { data.qty = qty.value; }
    if (data.expire == "") { delete data.expire; }
    if (data.oname == "") { delete data.oname; }

    // (F2) CALL API
    cb.api({
      mod : "move", act : "saveB",
      data : data,
      passmsg : "Batch saved",
      onpass : batch.list
    });
    return false;
  },

  // (G) DELETE BATCH
  //  sku : item SKU
  //  name : batch name
  del : (sku, name) => cb.modal("Please confirm", `Delete ${sku} - ${name}? All movement history will be lost!`, () => cb.api({
    mod : "move", act : "delB",
    data : {
      sku : sku,
      name : name
    },
    passmsg : "Batch Deleted",
    onpass : batch.list
  })),

  // (H) GENERATE QR CODE
  qr : (sku, name) => {
    document.getElementById("qrsku").value = sku;
    document.getElementById("qrbatch").value = name;
    document.getElementById("qrform").submit();
  },

  // (I) SHOW WRITE NFC SCREEN
  nfcSKU : null, // current sku to write
  nfcBatch : null, // current batch to write
  hnBtn : null, // html write nfc button
  hnStat : null, // html write nfc button status
  nfcShow : (sku, name) => {
    cb.load({
      page : "batch/nfc", target : "cb-page-2",
      data : {
        sku : sku,
        batch : name
      },
      onload : () => {
        batch.nfcSKU = sku;
        batch.nfcBatch = name;
        batch.hnBtn = document.getElementById("nfc-btn");
        batch.hnStat = document.getElementById("nfc-stat");
        cb.page(2);
        if (("NDEFReader" in window)) { batch.nfcWrite(); }
        else { batch.hnStat.innerHTML = "Web NFC Not Supported"; }
      }
    });
  },

  // (J) START WRITE NFC TAG
  nfcWrite : () => {
    // (J1) ON SUCCESSFUL NFC WRITE
    nfc.onwrite = () => {
      nfc.standby();
      cb.modal("Successful", "Click on the button again if you want to write another tag.");
      batch.hnStat.innerHTML = "Done";
    };

    // (J2) ON FAILED NFC WRITE
    nfc.onerror = err => {
      nfc.stop();
      console.error(err);
      cb.modal("ERROR", err.msg);
      batch.hnStat.innerHTML = "ERROR!";
    };

    // (J3) START NFC WRITE
    nfc.write(JSON.stringify({S:batch.nfcSKU,B:batch.nfcBatch}));
    batch.hnBtn.disabled = false;
    batch.hnStat.innerHTML = "Ready - Tap NFC tag";
  },

  // (K) END WRITE NFC SESSION
  nfcBack : () => {
    nfc.stop();
    cb.page(1);
  },

  // (L) IMPORT BATCHES
  import : () => im.init({
    name : "BATCHES",
    at : 2, back : 1,
    eg : "dummy-batches.csv",
    api : { mod : "move", act : "saveB" },
    after : () => batch.list(true),
    cols : [
      ["SKU", "sku", true],
      ["Batch Name", "name", true],
      ["Expiry", "expire", false],
      ["Quantity", "qty", "N"]
    ]
  })
};

window.addEventListener("load", () => {
  batch.list();
  autocomplete.attach({
    target : document.getElementById("batch-search"),
    mod : "autocomplete", act : "item",
    onpick : res => batch.search()
  });
});