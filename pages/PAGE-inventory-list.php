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
  <div class="dropdown">
    <button class="btn btn-primary btn-sm mi dropdown-toggle" type="button" data-bs-toggle="dropdown">
      more_vert
    </button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="inv.addEdit('<?=$sku?>')">
        <i class="mi mi-smol">edit</i> Edit
      </li>
      <li class="dropdown-item" onclick="check.load('<?=$sku?>')">
        <i class="mi mi-smol">history</i> History
      </li>
      <li class="dropdown-item" onclick="inv.qrcode('<?=$sku?>', '<?=$i["stock_name"]?>')">
        <i class="mi mi-smol">qr_code</i> QR Code
      </li>
      <li class="dropdown-item text-warning" onclick="inv.del('<?=$sku?>')">
        <i class="mi mi-smol">delete</i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No items found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("inv.goToPage");