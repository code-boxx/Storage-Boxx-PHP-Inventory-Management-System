<?php
// (A) PAGE META
$_PMETA = ["load" => [
  ["l", HOST_ASSETS."PAGE-scanner.css"],
  ["s", HOST_ASSETS."html5-qrcode.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."CB-autocomplete.js", "defer"],
  ["s", HOST_ASSETS."PAGE-qrscan.js", "defer"],
  ["s", HOST_ASSETS."PAGE-purchase.js", "defer"]
]];

// (B) HTML PAGE
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (B1) HEADER -->
<h3 class="mb-3">MANAGE PURCHASE ORDERS</h3>

<!-- (B2) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return pur.search()">
  <input type="text" id="pur-search" placeholder="Supplier Name" class="form-control form-control-sm">
  <button type="submit" class="btn btn-primary p-3 mx-1 ico-sm icon-search"></button>
  <button class="btn btn-primary p-3 ico-sm icon-plus" type="button" onclick="pur.addEdit()"></button>
</form>

<!-- (B3) HIDDEN PRINT ORDER -->
<form id="pur-print" method="post" target="_blank" action="<?=HOST_BASE?>report/purchase">
  <input type="hidden" id="pur-print-id" name="id">
</form>

<!-- (B4) PURCHASES LIST -->
<div id="pur-list" class="zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>