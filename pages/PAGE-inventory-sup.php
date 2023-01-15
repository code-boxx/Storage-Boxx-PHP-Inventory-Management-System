<?php
// (A) GET ITEM
$item = $_CORE->autoCall("Inventory", "get");
?>
<!-- (B) HEADER -->
<div class="d-flex align-items-center mb-3">
  <div class="flex-grow-1">
    <h3 class="mb-0">SUPPLIERS LIST</h3>
    <small class="fw-bold">[<?=$item["stock_sku"]?>] <?=$item["stock_name"]?></small>
  </div>
  <button class="btn btn-danger mi me-1" onclick="cb.page(0)">
    reply
  </button>
</div>

<!-- (C) SUPPLIERS LIST -->
<div id="sup-list" class="zebra my-4"></div>