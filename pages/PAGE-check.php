<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."html5-qrcode.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-check.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">CHECK ITEM</h3>
<!-- (A) MANUAL ENTRY -->
<div class="fw-bold text-danger">MANUAL ENTRY / SCANNER</div>
<form id="check-form" class="bg-white border p-4 mb-3" autocomplete="off" onsubmit="return check.verify()">
  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="check-sku" required>
    <label>Item SKU (manual enter or scan)</label>
  </div>

  <div class="d-flex align-items-stretch">
    <input type="submit" class="btn btn-primary" value="Check">
    <button id="nfc-btn" type="button" class="btn btn-primary d-flex align-items-center ms-2 d-none">
      <i class="mi">nfc</i> <span id="nfc-stat" class="ms-2">NFC</span>
    </button>
  </div>
</form>

<!-- (B) WEBCAM SCANNER -->
<div class="fw-bold text-danger">SCAN QR CODE</div>
<div class="bg-white border p-4 mb-3">
  <div id="reader"></div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>