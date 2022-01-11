<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."html5-qrcode.min.js"],
  ["s", HOST_ASSETS."PAGE-check.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="bg-white border p-4">
  <h3 class="mb-4">CHECK ITEM</h3>

  <!-- (A) SCANNER -->
  <div class="mb-4" id="reader"></div>

  <!-- (B) MANUAL FORM -->
  <form autocomplete="off" onsubmit="return check.verify()">
    <div class="input-group mb-4">
      <div class="input-group-prepend">
        <span class="input-group-text mi">qr_code</span>
      </div>
      <input type="text" class="form-control" id="check-sku" required autofocus placeholder="Item SKU (enter or scan)"/>
    </div>
    <input type="submit" class="btn btn-primary" value="Check"/>
  </form>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
