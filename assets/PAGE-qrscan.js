var qrscan = {
  // (A) PROPERTIES
  scanner : null, // scanner object
  
  // (B) INITIALIZE
  init : (hSKU, hBatch, after) => {
    // (B1) ATTACH HTML
    let hScan = document.createElement("div");
    hScan.id = "qr-wrapA";
    hScan.className = "d-none tran-zoom bg-dark";
    hScan.innerHTML = `<div id="qr-wrapB">
      <h3 class="mb-3 text-white">SCAN QR CODE</h3>
      <div id="qr-cam" class="bg-light"></div>
      <button type="button" class="mt-4 btn btn-danger d-flex-inline" onclick="qrscan.hide()">
        <i class="ico-sm icon-cross"></i> Cancel
      </button>
    </div>`;
    document.body.appendChild(hScan);

    // (B2) CREATE QR SCANNER OBJECT
    qrscan.scanner = new Html5QrcodeScanner("qr-cam", { fps: 10, qrbox: 250 });
    qrscan.scanner.render((txt, res) => {
      qrscan.hide();
      try {
        let item = JSON.parse(txt);
        hSKU.value = item.S;
        hBatch.value = item.B;
        after();
      } catch (e) {
        console.error(e);
        cb.modal("Invalid QR Code", "Failed to parse scanned QR code.");
      }
    });
  },

  // (C) SHOW SCANNER
  show : () => cb.transit(() => {
    document.getElementById("qr-wrapA").classList.remove("d-none");
    document.body.classList.add("overflow-hidden");
  }),

  // (D) HIDE QR SCANNER
  hide : () => {
    // (D1) SEEMINGLY NO SMART WAY TO "STOP SCANNING"
    let stop = document.getElementById("html5-qrcode-button-camera-stop"),
        wrap = document.getElementById("qr-wrapA");
    if (stop != null) {
      if (stop.style.display!="none") { stop.click(); }
    }

    // (D2) HIDE SCANNER
    cb.transit(() => {
      wrap.classList.add("d-none");
      document.body.classList.remove("overflow-hidden");
    });
  }
};