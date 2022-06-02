<?php
// (A) GET ITEM
$item = $_CORE->autoCall("Inventory", "get"); ?>
<!-- (B) NAVIGATION -->
<nav class="d-flex align-items-center">
  <div class="flex-grow-1">
    <h3 class="text-uppercase">Stock Name: <?=$item["stock_name"]?></h3>
    <div>Item SKU: <?=$item["stock_sku"]?></div>
  </div>
  <button class="btn btn-danger mi" onclick="cb.page(1)">
    undo
  </button>
</nav>

<!-- (C) ITEM MOVEMENT HISTORY -->
<div id="i-history" class="bg-white zebra my-4"></div>
