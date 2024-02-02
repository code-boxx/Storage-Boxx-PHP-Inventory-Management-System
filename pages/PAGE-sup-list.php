<?php
// (A) GET SUPPLIERS
$sup = $_CORE->autoCall("Suppliers", "getAll");

// (B) DRAW SUPPLIERS LIST
if (is_array($sup)) { foreach ($sup as $id=>$s) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$s["sup_name"]?></strong><br>
    <small>
      <span class="badge bg-secondary">address</span> <?=$s["sup_address"]?>
      <br>
      <span class="badge bg-secondary">tel</span> <a href="tel:<?=$s["sup_tel"]?>"><?=$s["sup_tel"]?></a>
      <span class="badge bg-secondary">email</span> <a href="mailto:<?=$s["sup_email"]?>"><?=$s["sup_email"]?></a>
    </small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="sup.addEdit(<?=$id?>)">
        <i class="text-secondary ico-sm icon-pencil"></i> Edit
      </li>
      <li class="dropdown-item" onclick="items.init(<?=$id?>)">
        <i class="text-secondary ico-sm icon-price-tag"></i> Items
      </li>
      <li class="dropdown-item" onclick="sup.csv(<?=$id?>)">
        <i class="text-secondary ico-sm icon-folder-download"></i> Items CSV
      </li>
      <li class="dropdown-item text-warning" onclick="sup.del('<?=$id?>')">
        <i class="ico-sm icon-bin2"></i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No suppliers found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("sup.goToPage");