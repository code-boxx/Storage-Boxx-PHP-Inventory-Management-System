<?php
// (A) GET ITEMS
$items = $_CORE->autoCall("Suppliers", "getItems");

// (B) DRAW ITEMS LIST
if (is_array($items)) { foreach ($items as $sku=>$i) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$i["stock_name"]?> (<?=$i["stock_unit"]?>)</strong><br>
    <strong>SKU: <?=$sku?> | Supplier SKU: <?=$i["sup_sku"]?$i["sup_sku"]:$sku?></strong><br>
    <small><?=$i["stock_desc"]?></small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary btn-sm mi" type="button" data-bs-toggle="dropdown">
      more_vert
    </button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="items.addEdit('<?=$sku?>')">
        <i class="mi mi-smol">edit</i> Edit
      </li>
      <li class="dropdown-item text-warning" onclick="items.del('<?=$sku?>')">
        <i class="mi mi-smol">delete</i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No items found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("items.goToPage");