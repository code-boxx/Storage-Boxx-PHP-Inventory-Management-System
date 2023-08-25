<?php
// (A) PAGE META
$_CORE->Settings->defineN("STOCK_MVT", true);
$_PMETA = ["load" => [
  ["l", HOST_ASSETS."PAGE-qrscan.css", "defer"],
  ["s", HOST_ASSETS."html5-qrcode.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."PAGE-move.js", "defer"]
]];

// (B) HTML PAGE
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">STOCK MOVEMENT</h3>
<form id="mvt-form" autocomplete="off" onsubmit="return move.save()">
  <!-- (B1) DIRECTION & QUANTITY -->
  <div class="fw-bold text-danger mb-2">DIRECTION &amp; QUANTITY</div>
  <div class="bg-white border p-4 mb-4">
    <div class="form-floating mb-4">
      <select class="form-select" id="mvt-direction"><?php
        foreach (STOCK_MVT as $c=>$m) {
          echo "<option value='$c'>$m</option>";
        }
      ?></select>
      <label>Direction</label>
    </div>
  
    <div class="form-floating mb-4">
      <input type="number" class="form-control" id="mvt-qty" min="0.01" step="0.01" required>
      <label>Quantity</label>
    </div>
  
    <div class="form-floating mb-4">
      <input type="text" class="form-control" id="mvt-notes">
      <label>Notes (if any)</label>
    </div>
  </div>

  <!-- (B2) SKU & BATCH -->
  <div class="fw-bold text-danger mb-2">SKU &amp; BATCH</div>
  <div class="bg-white border p-4 mb-4">
    <!-- (B2-1) MANUAL ENTRY -->
    <div class="form-floating mb-4">
      <input type="text" class="form-control" id="mvt-sku" required>
      <label>Item SKU</label>
    </div>
    <div class="form-floating mb-4">
      <input type="text" class="form-control" id="mvt-batch" required>
      <label>Batch</label>
    </div>

    <!-- (B2-2) BUTTONS & NFC SCANNER -->
    <button type="submit" class="my-1 btn btn-primary d-flex-inline">
      <i class="ico-sm icon-checkmark"></i> Save
    </button>
    <button type="button" class="my-1 btn btn-primary d-flex-inline" onclick="move.qron()">
      <i class="ico-sm icon-qrcode"></i> Scan
    </button>
    <button id="nfc-btn" type="button" disabled class="my-1 btn btn-primary d-flex-inline" onclick="">
      <i class="ico-sm icon-feed"></i> <span id="nfc-stat">NFC</span>
    </button>
  </div>
</form>

<!-- (C) LAST ENTRY -->
<div class="fw-bold text-danger mb-2">LAST SAVED ENTRY</div>
<div class="d-flex align-items-center bg-white border p-4">
  <div class="me-4 text-center display-6" id="last-qty">QTY</div>
  <div class="text-secondary">
    <div id="last-sku" class="fw-bold">ITEM</div>
    <div id="last-mvt">DIRECTION</div>
    <div id="last-notes">NOTES</div>
  </div>
</div>

<!-- (D) FLOATING QR SCANNER -->
<div id="qr-wrapA" class="d-none"><div id="qr-wrapB">
  <h3 class="mb-3">SCAN QR CODE</h3>
  <div id="qr-cam"></div>
  <button type="button" class="mt-4 btn btn-danger d-flex-inline" onclick="move.qroff()">
    <i class="ico-sm icon-cross"></i> Cancel
  </button>
</div></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>