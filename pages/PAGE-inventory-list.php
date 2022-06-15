<?php
// (A) GET ITEMS
$items = $_CORE->autoCall("Inventory", "getAll");

// (B) DRAW ITEMS LIST
if (is_array($items["data"])) { foreach ($items["data"] as $sku=>$i) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong>[<?=$sku?>] <?=$i["stock_name"]?></strong><br>
    <small><?=$i["stock_desc"]?></small><br>
    <small><?=$i["stock_qty"]?> <?=$i["stock_unit"]?></small>
  </div>
  <div>
    <button title="Delete" class="btn btn-danger btn-sm mi" onclick="inv.del('<?=$sku?>')">
      delete
    </button>
    <button title="Edit" class="btn btn-primary btn-sm mi" onclick="inv.addEdit('<?=$sku?>')">
      edit
    </button>
    <button title="Print" class="btn btn-primary btn-sm mi" onclick="inv.qrcode('<?=$sku?>')">
      print
    </button>
    <button title="History" class="btn btn-warning btn-sm mi" onclick="check.load('<?=$sku?>')">
      history
    </button>
  </div>
</div>
<?php }} else { ?>
<div class="d-flex align-items-center border p-2">No items found.</div>
<?php }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw($items["page"], "inv.goToPage");
