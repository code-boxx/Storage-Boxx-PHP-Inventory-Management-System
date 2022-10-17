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
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">compare_arrows</span>
    </div>
    <select class="form-select" id="mvt-direction"><?php
      foreach (STOCK_MVT as $c=>$m) {
        echo "<option value='$c'>$m</option>";
      }
    ?></select>
  </div>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">confirmation_number</span>
    </div>
    <input type="number" class="form-control" id="mvt-qty" min="0.01" step="0.01" required placeholder="Quantity">
  </div>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">speaker_notes</span>
    </div>
    <input type="text" class="form-control" id="mvt-notes" placeholder="Notes (if any)">
  </div>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">inventory_2</span>
    </div>
    <input type="text" class="form-control" id="mvt-sku" required placeholder="Item SKU (manual enter or scan)">
  </div>

  <input type="submit" class="btn btn-primary" value="Save">
</form>

<!-- (B) NFC SCANNER -->
<div class="fw-bold text-danger">SCAN NFC</div>
<div class="bg-white border p-4 mb-4">
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">nfc</span>
    </div>
    <input type="text" class="form-control" id="nfc-stat" readonly>
  </div>
</div>

<!-- (C) WEBCAM SCANNER -->
<div class="fw-bold text-danger">SCAN QR</div>
<div class="bg-white border p-4 mb-4">
  <div id="reader"></div>
</div>

<!-- (D) LAST ENTRY -->
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