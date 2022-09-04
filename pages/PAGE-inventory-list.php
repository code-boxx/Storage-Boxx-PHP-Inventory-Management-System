<?php
// (A) GET ITEMS
$items = $_CORE->autoCall("Inventory", "getAll");

// (B) DRAW ITEMS LIST
if (is_array($items)) { foreach ($items as $sku=>$i) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong>[<?=$sku?>] <?=$i["stock_name"]?></strong><br>
    <small><?=$i["stock_desc"]?></small><br>
    <small><?=$i["stock_qty"]?> <?=$i["stock_unit"]?></small>
  </div>
  <div>
    <button class="btn btn-danger btn-sm mi" onclick="inv.del('<?=$sku?>')">
      delete
    </button>
    <button class="btn btn-primary btn-sm mi" onclick="inv.addEdit('<?=$sku?>')">
      edit
    </button>
    <button class="btn btn-primary btn-sm mi" onclick="inv.qrcode('<?=$sku?>', '<?=$i["stock_name"]?>')">
      print
    </button>
    <button class="btn btn-primary btn-sm mi" onclick="check.load('<?=$sku?>')">
      history
    </button>
  </div>
</div>
<?php }} else { echo "No items found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("inv.goToPage");