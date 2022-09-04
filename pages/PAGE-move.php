<?php
$_CORE->Settings->defineN("STOCK_MVT", true);
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."html5-qrcode.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-move.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">STOCK MOVEMENT</h3>
<div class="bg-white border p-4 mb-3">
  <!-- (A) QR SCANNER -->
  <div id="reader" class="mb-3"></div>

  <!-- (B) MOVEMENT FORM -->
  <form id="mvt-form" autocomplete="off" onsubmit="return move.save()">
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">compare_arrows</span>
      </div>
      <select class="form-control" id="mvt-direction"><?php
        foreach (STOCK_MVT as $c=>$m) {
          echo "<option value='$c'>$m</option>";
        }
      ?></select>
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">confirmation_number</span>
      </div>
      <input type="number" class="form-control" id="mvt-qty" step="0.01" value="1.00" required placeholder="Quantity">
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">speaker_notes</span>
      </div>
      <input type="text" class="form-control" id="mvt-notes" placeholder="Notes (if any)">
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">qr_code</span>
      </div>
      <input type="text" class="form-control" id="mvt-sku" required autofocus placeholder="Item SKU (enter or scan)">
    </div>

    <input type="submit" class="btn btn-primary" value="Save">
  </form>
</div>

<div class="fw-bold">LAST SAVED ENTRY</div>
<div class="d-flex align-items-center bg-white border p-4">
  <div class="w-50 text-center">
    <div id="last-mvt" class="fw-bold">DIRECTION</div>
    <div id="last-qty" class="display-6">QUANTITY</div>
  </div>
  <div class="w-50 text-secondary">
    <div id="last-sku">SKU</div>
    <div id="last-notes">NOTES</div>
  </div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>