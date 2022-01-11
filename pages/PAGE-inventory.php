<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-inventory.js", "defer"],
  ["s", HOST_ASSETS."PAGE-checker.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) NAVIGATION -->
<nav class="bg-white border mb-3">
  <!-- (A1) HEADER -->
  <div class="d-flex align-items-center p-3 pb-0">
    <h3 class="flex-grow-1">MANAGE INVENTORY</h3>
    <button class="btn btn-primary" onclick="inv.addEdit()">
      <span class="mi">add</span>
    </button>
  </div>

  <!-- (A2) SEARCH BAR -->
  <form class="d-flex align-items-stretch p-3" onsubmit="return inv.search()">
    <input type="text" id="inv-search" placeholder="Search" class="form-control form-control-sm"/>
    <button class="btn btn-primary">
      <span class="mi">search</span>
    </button>
  </form>
</nav>

<!-- (B) INVENTORY LIST -->
<div id="inv-list" class="bg-white border zebra my-4"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
