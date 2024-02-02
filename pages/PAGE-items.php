<?php
// (A) PAGE META
$_PMETA = ["load" => [
  ["l", HOST_ASSETS."PAGE-scanner.css"],
  ["s", HOST_ASSETS."csv.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."CB-autocomplete.js", "defer"],
  ["s", HOST_ASSETS."PAGE-import.js", "defer"],
  ["s", HOST_ASSETS."PAGE-items.js", "defer"],
  ["s", HOST_ASSETS."PAGE-items-check.js", "defer"]
]];

// (B) HTML PAGE
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (B1) HEADER -->
<h3 class="mb-3">MANAGE ITEMS</h3>

<!-- (B2) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return item.search()">
  <input type="text" id="item-search" placeholder="Item name or SKU" class="form-control form-control-sm">
  <button type="submit" class="btn btn-primary p-3 mx-1 ico-sm icon-search"></button>
  <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
  <ul class="dropdown-menu dropdown-menu-dark">
    <li class="dropdown-item" onclick="item.addEdit()">
      <i class="text-secondary ico-sm icon-plus"></i> Add Single
    </li>
    <li class="dropdown-item" onclick="item.import()">
      <i class="text-secondary ico-sm icon-upload3"></i> Import CSV
    </li>
  </ul>
</form>

<!-- (B3) HIDDEN GENERATE QR CODE -->
<form id="qrform" method="post" target="_blank" action="<?=HOST_BASE?>report/qr">
  <input type="hidden" name="for" value="item">
  <input type="hidden" id="qrsku" name="id">
</form>

<!-- (B4) INVENTORY LIST -->
<div id="item-list" class="zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>