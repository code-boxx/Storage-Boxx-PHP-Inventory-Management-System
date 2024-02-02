<?php
// (A) GET SUPPLIERS
$sup = $_CORE->autoCall("Suppliers", "getBySKU");

// (B) SUPPLIERS LIST
if (is_array($sup)) { foreach ($sup as $id=>$s) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong>[<?=$s["sup_sku"]?>] <?=$s["sup_name"]?></strong><br>
    <small>
      <span class="badge bg-secondary">tel</span> <a href="tel:<?=$s["sup_tel"]?>"><?=$s["sup_tel"]?></a>
      <span class="badge bg-secondary">email</span> <a href="mailto:<?=$s["sup_email"]?>"><?=$s["sup_email"]?></a>
      <br>
      <span class="badge bg-secondary">price</span> <?=$s["unit_price"]?> / <?=$s["item_unit"]?>
    </small>
  </div>
</div>
<?php }} else { echo "No suppliers for this item"; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("inv.suppage");