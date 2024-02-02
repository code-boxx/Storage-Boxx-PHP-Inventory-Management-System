var check = {
  // (A) PROPERTIES
  hSKU : null, // html sku field
  sku : null, // current item
  pg : 1, // current page

  // (B) INIT
  init : () => {
    // (B1) GET HTML ELEMENTS
    check.hSKU = document.getElementById("check-sku");

    // (B2) INIT NFC
    if ("NDEFReader" in window) {
      document.getElementById("nfc-btn").disabled = false;
      nfc.init(sku => {
        check.hSKU.value = sku;
        check.pre();
      });
    }

    // (B3) INIT AUTOCOMPLETE
    autocomplete.attach({
      target : document.getElementById("check-sku"),
      mod : "autocomplete", act : "sku",
      onpick : check.pre
    });
  },

  // (C) "SWITCH ON" QR SCANNER
  qron : () => {
    qrscan.init(sku => {
      check.hSKU.value = sku;
      check.pre();
    });
    qrscan.show();
  },

  // (D) CHECK VALID SKU BEFORE LOADING HISTORY LIST
  pre : () => {
    cb.api({
      mod : "items", act : "check",
      data : { sku : check.hSKU.value },
      passmsg : false,
      onpass : res => {
        check.sku = check.hSKU.value;
        check.pg = 1;
        check.go();
      }
    });
    return false;
  },

  // (E) LOAD MOVEMENT HISTORY "MAIN PAGE"
  go : () => cb.load({
    page : "check-main", target : "cb-page-2",
    data : { sku : check.sku },
    onload : () => {
      cb.page(2);
      check.list();
    }
  }),

  // (F) SHOW ITEM MOVEMENT HISTORY
  list : () => cb.load({
    page : "check/list", target : "check-list",
    data : {
      sku : check.sku,
      page : check.pg
    }
  }),

  // (G) GO TO PAGE
  //  pg : int, page number
  goToPage : pg => { if (pg!=check.pg) {
    check.pg = pg;
    check.list();
  }}
};

// (H) INIT ITEM CHECK
window.addEventListener("load", check.init);