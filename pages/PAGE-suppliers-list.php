<?php
// (A) GET SUPPLIERS
$sup = $_CORE->autoCall("Suppliers", "getAll");

// (B) DRAW SUPPLIERS LIST
if (is_array($sup)) { foreach ($sup as $id=>$s) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$s["sup_name"]?></strong><br>
    <small><?=$s["sup_tel"]?> | <a href="mailto:<?=$s["sup_email"]?>"><?=$s["sup_email"]?></a></small><br>
    <small><?=$s["sup_address"]?></small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary btn-sm mi" type="button" data-bs-toggle="dropdown">
      more_vert
    </button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="sup.addEdit(<?=$id?>)">
        <i class="mi mi-smol">edit</i> Edit
      </li>
      <li class="dropdown-item" onclick="items.init(<?=$id?>)">
        <i class="mi mi-smol">inventory</i> Items
      </li>
      <li class="dropdown-item" onclick="sup.csv(<?=$id?>)">
        <i class="mi mi-smol">download</i> CSV List
      </li>
      <li class="dropdown-item text-warning" onclick="sup.del(<?=$id?>)">
        <i class="mi mi-smol">delete</i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No suppliers found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("sup.goToPage");