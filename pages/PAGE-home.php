<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."html5-qrcode.min.js"],
  ["s", HOST_ASSETS."PAGE-move.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="bg-white border p-4">
  <!-- (A) QR SCANNER -->
  <h4 class="mb-4">STOCK MOVEMENT</h4>
  <div id="reader" class="mb-4"></div>

  <!-- (B) MOVEMENT FORM -->
  <form autocomplete="off" onsubmit="return move.save()">
    <div class="input-group mb-4">
      <div class="input-group-prepend">
        <span class="input-group-text mi">compare_arrows</span>
      </div>
      <select class="form-control" id="mvt-direction">
        <option value="I">Stock In (Receive)</option>
        <option value="O">Stock Out (Release)</option>
        <option value="T">Stock Take (Audit)</option>
      </select>
    </div>

    <div class="input-group mb-4">
      <div class="input-group-prepend">
        <span class="input-group-text mi">confirmation_number</span>
      </div>
      <input type="number" class="form-control" id="mvt-qty" step="0.01" value="1.00" required placeholder="Quantity"/>
    </div>

    <div class="input-group mb-4">
      <div class="input-group-prepend">
        <span class="input-group-text mi">speaker_notes</span>
      </div>
      <input type="text" class="form-control" id="mvt-notes" placeholder="Notes (if any)"/>
    </div>

    <div class="input-group mb-4">
      <div class="input-group-prepend">
        <span class="input-group-text mi">qr_code</span>
      </div>
      <input type="text" class="form-control" id="mvt-sku" required autofocus placeholder="Item SKU (enter or scan)"/>
    </div>

    <input type="submit" class="btn btn-primary" value="Save"/>
  </form>

  <div class="col bg-light border mt-4" style="max-height:200px;overflow:auto">
    <ul class="list-group list-group-flush" id="mvt-result"></ul>
  </div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
