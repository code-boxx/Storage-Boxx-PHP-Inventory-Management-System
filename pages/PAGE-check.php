<?php
// (A) PAGE META
$_PMETA = ["load" => [
  ["l", HOST_ASSETS."PAGE-scanner.css"],
  ["s", HOST_ASSETS."html5-qrcode.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."CB-autocomplete.js", "defer"],
  ["s", HOST_ASSETS."PAGE-qrscan.js", "defer"],
  ["s", HOST_ASSETS."PAGE-check.js", "defer"]
]];

// (B) HTML PAGE
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">CHECK ITEM</h3>
<form id="check-form" class="bg-white border p-4 mb-3" autocomplete="off" onsubmit="return check.pre()">
  <!-- (B1) MANUAL ENTRY -->
  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="check-sku" required>
    <label>Item SKU</label>
  </div>

  <!-- (B2) BUTTONS -->
  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-search"></i> Check
  </button>
  <button type="button" class="my-1 btn btn-primary d-flex-inline" onclick="check.qron()">
    <i class="ico-sm icon-qrcode"></i> Scan
  </button>
  <button id="nfc-btn" type="button" disabled class="my-1 btn btn-primary d-flex-inline" onclick="nfc.scan()">
    <i class="ico-sm icon-feed"></i> Scan
  </button>
</form>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>