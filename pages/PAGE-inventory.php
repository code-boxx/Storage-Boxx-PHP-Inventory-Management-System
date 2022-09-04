<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-inventory.js", "defer"],
  ["s", HOST_ASSETS."PAGE-checker.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) HEADER -->
<h3 class="mb-3">MANAGE INVENTORY</h3>

<!-- (B) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return inv.search()">
  <input type="text" id="inv-search" placeholder="Search" class="form-control form-control-sm">
  <button class="btn btn-primary mi mx-1">
    search
  </button>
  <button class="btn btn-primary mi" onclick="inv.addEdit()">
    add
  </button>
</form>

<!-- (C) INVENTORY LIST -->
<div id="inv-list" class="zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>