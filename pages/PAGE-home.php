<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."html5-qrcode.min.js"],
  ["s", HOST_ASSETS."PAGE-move.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">STOCK MOVEMENT</h3>
<div class="bg-white border p-4 mb-3">
  <!-- (A) QR SCANNER -->
  <div class="center mb-3">
    <div id="reader" style="width: 500px"></div>
  </div>

  <!-- (B) MOVEMENT FORM -->
  <form autocomplete="off" onsubmit="return move.save()">
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">compare_arrows</span>
      </div>
      <select class="form-control" id="mvt-direction">
        <option value="I">Stock In (Receive)</option>
        <option value="O">Stock Out (Release)</option>
        <option value="T">Stock Take (Audit)</option>
      </select>
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">confirmation_number</span>
      </div>
      <input type="number" class="form-control" id="mvt-qty" step="0.01" value="1.00" required placeholder="Quantity"/>
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">speaker_notes</span>
      </div>
      <input type="text" class="form-control" id="mvt-notes" placeholder="Notes (if any)"/>
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">qr_code</span>
      </div>
      <input type="text" class="form-control" id="mvt-sku" required autofocus placeholder="Item SKU (enter or scan)"/>
    </div>

    <div style="display: flex; justify-Content: flex-end; align-Items: flex-end;">
      <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
  </form>
</div>

<div class="col bg-light border mt-4" style="max-height:200px;overflow:auto">
  <ul class="list-group list-group-flush" id="mvt-result"></ul>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
