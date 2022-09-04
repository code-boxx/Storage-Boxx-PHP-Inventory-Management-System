var check = {
  // (A) PROPERTIES
  hSKU : null, // html sku field
  scanner : null, // qr scanner
  sku : null, // current item
  pg : 1, // current page

  // (B) INIT
  init : () => {
    check.hSKU = document.getElementById("check-sku");
    check.scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    check.scanner.render((txt, res) => {
      let buttons = document.querySelectorAll("#reader button");
      buttons[1].click();
      check.hSKU.value = txt;
      check.verify();
    });
  },

  // (C) VERIFY VALID SKU BEFORE SHOW HISTORY
  verify : () => {
    cb.api({
      mod : "inventory", req : "get",
      data : { sku : check.hSKU.value },
      passmsg : false,
      onpass : res => {
        if (res.data===null) {
          cb.modal("Invalid Item", "SKU is not found in database.");
        } else {
          check.load(check.hSKU.value);
          check.hSKU.value = "";
        }
      }
    });
    return false;
  },

  // (CD) LOAD MOVEMENT HISTORY "MAIN PAGE"
  //  sku : string, item sku
  load : sku => {
    cb.load({
      page : "icheck", target : "cb-page-2",
      data : { sku : sku },
      onload : () => {
        check.sku = sku;
        check.pg = 1;
        cb.page(1);
        check.list();
      }
    });
  },

  // (E) SHOW ITEM MOVEMENT HISTORY
  list : () => {
    cb.load({
      page : "icheck/list", target : "i-history",
      data : {
        sku : check.sku,
        page : check.pg
      }
    });
  },

  // (F) GO TO PAGE
  //  pg : int, page number
  goToPage : pg => { if (pg!=check.pg) {
    check.pg = pg;
    check.list();
  }}
};
window.addEventListener("load", check.init);