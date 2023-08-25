<?php
// (A) GET ITEM
$item = $_CORE->autoCall("Items", "get"); ?>
<!-- (B) NAVIGATION -->
<nav class="d-flex align-items-center">
  <div class="flex-grow-1">
    <div class="display-6">
      [<?=$item["item_sku"]?>] <?=$item["item_name"] ?>
    </div>
    <div class="fw-bold">STOCK : <?=$item["item_qty"]?> <?=$item["item_unit"]?></div>
  </div>
  <button type="button" class="btn btn-danger p-3 mx-1 ico-sm icon-undo2" onclick="cb.page(1)"></button>
</nav>

<!-- (C) ITEM MOVEMENT HISTORY -->
<div id="check-list" class="zebra my-4"></div>