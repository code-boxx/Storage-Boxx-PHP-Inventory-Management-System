<?php
// (A) GET SUPPLIERS
$sup = $_CORE->autoCall("Suppliers", "getBySKU");

// (B) SUPPLIERS LIST
if (is_array($sup)) { foreach ($sup as $id=>$s) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$s["sup_name"]?></strong><br>
    <strong>SSKU: <?=$s["sup_sku"]?></strong><br>
    <small>
      T: <a href="tel:<?=$s["sup_tel"]?>"><?=$s["sup_tel"]?></a> |
      E: <a href="mailto:<?=$s["sup_email"]?>"><?=$s["sup_email"]?></a>
    </small><br>
    <small><?=$s["sup_address"]?></small>
  </div>
  <div class="text-secondary">
    $<?=$s["unit_price"]?> / <?=$s["item_unit"]?>
  </div>
</div>
<?php }} else { echo "No suppliers for this item"; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("inv.suppage");