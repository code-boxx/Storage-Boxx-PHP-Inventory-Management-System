<?php
// (A) GET ITEM
$item = $_CORE->autoCall("Inventory", "get"); ?>
<!-- (B) NAVIGATION -->
<nav class="d-flex align-items-center bg-white border mb-3 p-3">
  <div class="flex-grow-1">
    <h3 class="text-uppercase"><?=$item["stock_name"]?></h3>
    <div><?=$item["stock_sku"]?></div>
  </div>
  <button class="btn btn-danger" onclick="cb.page(1)">
  <span class="mi">undo</span>
</nav>

<!-- (C) ITEM MOVEMENT HISTORY -->
<div id="i-history" class="bg-white border zebra my-4"></div>
