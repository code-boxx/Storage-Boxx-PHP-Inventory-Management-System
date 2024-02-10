<?php
// (A) GET PURCHASES
$_CORE->Settings->defineN("PURCHASE_STAT", true);
$pur = $_CORE->autoCall("Purchase", "getAll");
$colors = ["secondary", "primary", "danger"];

// (B) DRAW PURCHASES LIST
if (is_array($pur)) { foreach ($pur as $id=>$p) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$p["sup_name"]?></strong><br>
    <small>
      <span class="badge bg-secondary">#</span> <?=$p["p_id"]?>
      <br>
      <span class="badge bg-secondary">date</span> <?=$p["p_date"]?>
      <br>
      <span class="badge bg-<?=$colors[$p["p_status"]]?>">status</span> <?=PURCHASE_STAT[$p["p_status"]]?>
    </small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <?php if ($p["p_status"]==0) { ?>
      <li class="dropdown-item" onclick="pur.addEdit(<?=$id?>)">
        <i class="text-secondary ico-sm icon-pencil"></i> Edit
      </li>
      <?php } ?>
      <li class="dropdown-item" onclick="pur.print(<?=$id?>)">
        <i class="text-secondary ico-sm icon-printer"></i> Print
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No purchases found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("pur.goToPage");