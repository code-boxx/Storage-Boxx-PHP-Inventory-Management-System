<?php
// (A) GET ITEM
$item = $_CORE->autoCall("Items", "get");
?>
<!-- (B) HEADER -->
<div class="d-flex align-items-center mb-3">
  <div class="flex-grow-1">
    <h3 class="mb-0">SUPPLIERS LIST</h3>
    <small class="fw-bold">[<?=$item["item_sku"]?>] <?=$item["item_name"]?></small>
  </div>
  <button type="button" class="btn btn-danger p-3 mx-1 ico-sm icon-undo2" onclick="cb.page(1)"></button>
</div>

<!-- (C) SUPPLIERS LIST -->
<div id="sup-list" class="zebra my-4"></div>