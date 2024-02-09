<?php
// (A) GET DELIVERIES
$_CORE->Settings->defineN("DELIVER_STAT", true);
$dlv = $_CORE->autoCall("Delivery", "getAll");
$colors = ["secondary", "primary", "danger"];

// (B) DRAW DELIVERIES LIST
if (is_array($dlv)) { foreach ($dlv as $id=>$d) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong><?=$d["cus_name"]?></strong><br>
    <small>
      <span class="badge bg-secondary">#</span> <?=$d["d_id"]?>
      <br>
      <span class="badge bg-secondary">date</span> <?=$d["d_date"]?>
      <br>
      <span class="badge bg-<?=$colors[$d["d_status"]]?>">status</span> <?=DELIVER_STAT[$d["d_status"]]?>
    </small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <?php if ($d["d_status"]==0) { ?>
      <li class="dropdown-item" onclick="dlv.addEdit(<?=$id?>)">
        <i class="text-secondary ico-sm icon-pencil"></i> Edit
      </li>
      <?php } ?>
      <li class="dropdown-item" onclick="dlv.print(<?=$id?>)">
        <i class="text-secondary ico-sm icon-printer"></i> Print
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No deliveries found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("dlv.goToPage");