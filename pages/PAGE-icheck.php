<?php
// (A) GET ITEM
$item = $_CORE->autoCall("Inventory", "get"); ?>
<!-- (B) NAVIGATION -->
<nav class="bg-white border p-2 d-flex align-items-center">
  <div class="flex-grow-1">
    <div class="fw-bold">[<?=$item["stock_sku"]?>] <?=$item["stock_name"]?></div>
    <div class="display-6">
      <?=$item["stock_qty"]?> <?=$item["stock_unit"]?>
    </div>
  </div>
  <button class="btn btn-danger mi" onclick="cb.page(0)">
    undo
  </button>
</nav>

<!-- (C) ITEM MOVEMENT HISTORY -->
<div id="i-history" class="zebra my-4"></div>