<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."csv.min.js", "defer"],
  ["s", HOST_ASSETS."PAGE-suppliers.js", "defer"],
  ["s", HOST_ASSETS."PAGE-sup-import.js", "defer"],
  ["s", HOST_ASSETS."PAGE-sup-items.js", "defer"],
  ["s", HOST_ASSETS."PAGE-sup-items-import.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) HEADER -->
<h3 class="mb-3">MANAGE SUPPLIERS</h3>

<!-- (B) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return sup.search()">
  <input type="text" id="sup-search" placeholder="Search" class="form-control form-control-sm">
  <button type="submit" class="btn btn-primary mi mx-1">
    search
  </button>
  <button class="btn btn-primary mi" type="button" data-bs-toggle="dropdown">
    add
  </button>
  <ul class="dropdown-menu dropdown-menu-dark">
    <li class="dropdown-item" onclick="sup.addEdit()">
      <i class="mi mi-smil">add</i> Add Single
    </li>
    <li class="dropdown-item" onclick="simport.init()">
      <i class="mi mi-smil">upload</i> Import CSV
    </li>
  </ul>
</form>

<!-- (C) SUPPLIERS LIST -->
<div id="sup-list" class="zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>