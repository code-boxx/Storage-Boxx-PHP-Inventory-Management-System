<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."html5-qrcode.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-check.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">CHECK ITEM</h3>
<!-- (A) MANUAL ENTRY -->
<div class="fw-bold text-danger">MANUAL ENTRY/BARCODE SCANNER</div>
<form id="check-form" class="bg-white border p-4 mb-3" autocomplete="off" onsubmit="return check.verify()">
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">inventory_2</span>
    </div>
    <input type="text" class="form-control" id="check-sku" required placeholder="Item SKU (manual enter or scan)">
  </div>
  <input type="submit" class="btn btn-primary" value="Check">
</form>
  
<!-- (B) NFC SCANNER -->
<div class="fw-bold text-danger">NFC SCANNER</div>
<div class="bg-white border p-4 mb-3">
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">nfc</span>
    </div>
    <input type="text" class="form-control" id="nfc-stat" readonly>
  </div>
</div>
  
<!-- (C) WEBCAM SCANNER -->
<div class="fw-bold text-danger">QR SCANNER</div>
<div class="bg-white border p-4 mb-3">
  <div id="reader"></div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>