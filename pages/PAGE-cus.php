<?php
// (A) PAGE META
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."csv.min.js", "defer"],
  ["s", HOST_ASSETS."CB-autocomplete.js", "defer"],
  ["s", HOST_ASSETS."PAGE-import.js", "defer"],
  ["s", HOST_ASSETS."PAGE-cus.js", "defer"]
]];

// (B) HTML
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (B1) HEADER -->
<h3 class="mb-3">MANAGE CUSTOMERS</h3>

<!-- (B2) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return cus.search()">
  <input type="text" id="cus-search" placeholder="Search" class="form-control form-control-sm">
  <button type="submit" class="btn btn-primary p-3 mx-1 ico-sm icon-search"></button>
  <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
  <ul class="dropdown-menu dropdown-menu-dark">
    <li class="dropdown-item" onclick="cus.addEdit()">
      <i class="text-secondary ico-sm icon-plus"></i> Add Single
    </li>
    <li class="dropdown-item" onclick="cus.import()">
      <i class="text-secondary ico-sm icon-upload3"></i> Import CSV
    </li>
  </ul>
</form>

<!-- (B3) CUSTOMERS LIST -->
<div id="cus-list" class="zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>