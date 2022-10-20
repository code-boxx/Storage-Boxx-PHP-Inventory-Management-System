var check = {
  // (A) PROPERTIES
  hForm : null, // html check form
  hSKU : null, // html sku field
  hnBtn : null, // html nfc button
  hnStat : null, // html nfc status
  qrscan : null, // qr scanner
  sku : null, // current item
  pg : 1, // current page

  // (B) INIT
  init : () => {
    // (B1) GET HTML ELEMENTS
    check.hForm = document.getElementById("check-form");
    check.hSKU = document.getElementById("check-sku");
    check.hnBtn = document.getElementById("nfc-btn");
    check.hnStat = document.getElementById("nfc-stat");

    // (B2) QR CODE SCANNER
    check.qrscan = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    check.qrscan.render((txt, res) => {
      let buttons = document.querySelectorAll("#reader button");
      buttons[1].click();
      check.hSKU.value = txt;
      check.verify();
    });

    // (B3) NFC SCANNER
    if ("NDEFReader" in window) {
      // (B3-1) ON SUCCESSFUL NFC READ
      nfc.onread = evt => {
        nfc.standby();
        const decoder = new TextDecoder();
        for (let record of evt.message.records) {
          check.hSKU.value = decoder.decode(record.data);
        }
        check.verify();
        check.hnStat.innerHTML = "NFC";
      };
      
      // (B3-2) ON NFC READ ERROR
      nfc.onerror = err => {
        nfc.stop();
        console.error(err);
        cb.modal("ERROR", err.message);
        check.hnStat.innerHTML = "ERROR";
      };

      // (B3-3) ENABLE NFC BUTTON
      check.hnBtn.onclick = () => {
        check.hnStat.innerHTML = "Scanning - Tap token";
        nfc.scan();
      };
      check.hnBtn.classList.remove("d-none");
    }
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

  // (D) LOAD MOVEMENT HISTORY "MAIN PAGE"
  //  sku : string, item sku
  load : sku => cb.load({
    page : "icheck", target : "cb-page-2",
    data : { sku : sku },
    onload : () => {
      check.sku = sku;
      check.pg = 1;
      cb.page(1);
      check.list();
    }
  }),

  // (E) SHOW ITEM MOVEMENT HISTORY
  list : () => cb.load({
    page : "icheck/list", target : "i-history",
    data : {
      sku : check.sku,
      page : check.pg
    }
  }),

  // (F) GO TO PAGE
  //  pg : int, page number
  goToPage : pg => { if (pg!=check.pg) {
    check.pg = pg;
    check.list();
  }}
};
window.addEventListener("load", check.init);