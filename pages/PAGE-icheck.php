<?php
// (A) GET ITEM
$item = $_CORE->autoCall("Inventory", "get");
?>

<!-- (B) NAVIGATION -->
<nav class="navbar cb-grey">
<div class="container-fluid">
  <div>
    <div class="fw-bold text-uppercase"><?=$item["stock_name"]?></div>
    <div><?=$item["stock_sku"]?></div>
  </div>
  <div class="d-flex">
    <button class="btn btn-danger" onclick="cb.page(1)">
      <span class="mi">undo</span>
    </button>
  </div>
</div>
</nav>

<!-- (C) ITEM MOVEMENT HISTORY -->
<div id="i-history" class="container zebra my-4"></div>
