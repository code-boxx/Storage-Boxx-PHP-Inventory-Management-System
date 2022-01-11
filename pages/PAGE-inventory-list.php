<?php
// (A) GET ITEMS
$items = $_CORE->autoCall("Inventory", "getAll");

// (B) DRAW ITEMS LIST
if (is_array($items["data"])) { foreach ($items["data"] as $sku=>$i) { ?>
<div class="d-flex align-items-center p-2">
  <div class="flex-grow-1">
    <strong>[<?=$sku?>] <?=$i["stock_name"]?></strong><br>
    <small><?=$i["stock_desc"]?></small><br>
    <small><?=$i["stock_qty"]?> <?=$i["stock_unit"]?></small>
  </div>
  <div>
    <button class="btn btn-danger btn-sm" onclick="inv.del('<?=$sku?>')">
      <span class="mi">delete</span>
    </button>
    <button class="btn btn-primary btn-sm" onclick="inv.addEdit('<?=$sku?>')">
      <span class="mi">edit</span>
    </button>
    <button class="btn btn-primary btn-sm" onclick="inv.qrcode('<?=$sku?>')">
      <span class="mi">print</span>
    </button>
    <button class="btn btn-primary btn-sm" onclick="check.load('<?=$sku?>')">
      <span class="mi">history</span>
    </button>
  </div>
</div>
<?php }} else { ?>
<div class="d-flex align-items-center p-2">No items found.</div>
<?php }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw($items["page"], "inv.goToPage");
