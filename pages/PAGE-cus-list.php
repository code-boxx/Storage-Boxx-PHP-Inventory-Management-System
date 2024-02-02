<?php
// (A) GET CUSTOMERS
$cus = $_CORE->autoCall("Customers", "getAll");

// (B) DRAW CUSTOMERS LIST
if (is_array($cus)) { foreach ($cus as $id=>$c) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$c["cus_name"]?></strong><br>
    <small>
      <span class="badge bg-secondary">address</span> <?=$c["cus_address"]?>
      <br>
      <span class="badge bg-secondary">tel</span> <a href="tel:<?=$c["cus_tel"]?>"><?=$c["cus_tel"]?></a>
      <span class="badge bg-secondary">email</span> <a href="mailto:<?=$c["cus_email"]?>"><?=$c["cus_email"]?></a>
    </small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="cus.addEdit(<?=$id?>)">
        <i class="text-secondary ico-sm icon-pencil"></i> Edit
      </li>
      <li class="dropdown-item text-warning" onclick="cus.del('<?=$id?>')">
        <i class="ico-sm icon-bin2"></i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No customers found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("cus.goToPage");