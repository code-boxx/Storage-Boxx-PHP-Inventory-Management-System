<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-inventory.js", "defer"],
  ["s", HOST_ASSETS."PAGE-checker.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) NAVIGATION -->
<nav class="navbar cb-grey"><div class="container-fluid">
  <h4>Manage Inventory</h4>
  <div class="d-flex">
    <button class="btn btn-primary" onclick="inv.addEdit()">
      <span class="mi">add</span>
    </button>
  </div>
</div></nav>

<!-- (B) SEARCH BAR -->
<div class="searchBar"><form class="d-flex" onsubmit="return inv.search()">
  <input type="text" id="inv-search" placeholder="Search" class="form-control form-control-sm"/>
  <button class="btn btn-primary">
    <span class="mi">search</span>
  </button>
</form></div>

<!-- (C) INVENTORY LIST -->
<div id="inv-list" class="container zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
