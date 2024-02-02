<?php
// (A) GET ITEMS
$items = $_CORE->autoCall("Suppliers", "getItems");

// (B) DRAW ITEMS LIST
if (is_array($items)) { foreach ($items as $sku=>$i) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong>[<?=$sku?>] <?=$i["item_name"]?></strong><br>
    <small>
      <span class="badge bg-secondary">ssku</span> <?=$i["sup_sku"]?$i["sup_sku"]:$sku?><br>
      <span class="badge bg-secondary">price</span> <?=$i["unit_price"]?> / <?=$i["item_unit"]?>
    </small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="items.addEdit('<?=$sku?>')">
        <i class="text-secondary ico-sm icon-pencil"></i> Edit
      </li>
      <li class="dropdown-item text-warning" onclick="items.del('<?=$sku?>')">
        <i class="ico-sm icon-bin2"></i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No items found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("items.goToPage");