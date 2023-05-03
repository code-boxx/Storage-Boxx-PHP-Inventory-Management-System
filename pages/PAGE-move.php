<?php
$_CORE->Settings->defineN("STOCK_MVT", true);
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."html5-qrcode.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-move.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">STOCK MOVEMENT</h3>

<!-- (A) ITEM MOVEMENT -->
<div class="fw-bold text-danger">DIRECTION &amp; QUANTITY</div>
<form id="mvt-form" class="bg-white border p-4 mb-4" autocomplete="off" onsubmit="return move.save()">
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

  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="mvt-sku" required>
    <label>Item SKU (manual enter or scan)</label>
  </div>

  <div class="d-flex align-items-stretch">
    <input type="submit" class="btn btn-primary" value="Save">
    <button id="nfc-btn" type="button" class="btn btn-primary d-flex align-items-center ms-2 d-none">
      <i class="mi">nfc</i> <span id="nfc-stat" class="ms-2">NFC</span>
    </button>
  </div>
</form>

<!-- (B) WEBCAM SCANNER -->
<div class="fw-bold text-danger">SCAN QR CODE</div>
<div class="bg-white border p-4 mb-4">
  <div id="reader"></div>
</div>

<!-- (C) LAST ENTRY -->
<div class="fw-bold text-danger">LAST SAVED ENTRY</div>
<div class="d-flex align-items-center bg-white border p-4">
  <div class="me-4 text-center">
    <div id="last-qty" class="display-6">QTY</div>
    <div id="last-unit">UNIT</div>
  </div>
  <div class="text-secondary">
    <div id="last-sku" class="fw-bold">ITEM</div>
    <div id="last-mvt">DIRECTION</div>
    <div id="last-notes">NOTES</div>
  </div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>