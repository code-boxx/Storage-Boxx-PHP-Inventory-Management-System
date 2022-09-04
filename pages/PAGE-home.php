<?php
// (A) GET "WATCHED ITEMS"
$_CORE->load("Inventory");
$items = $_CORE->Inventory->getMonitor();

// (B) DASHBOARD
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<ul class="list-group">
<?php if (is_array($items)) { foreach ($items as $i) {
$low = $i["stock_qty"] <= $i["stock_low"]; ?>
<li class="list-group-item d-flex justify-content-between align-items-start">
  <div class="ms-2 me-auto">
    <div class="fw-bold<?=$low?" text-danger":""?>">[<?=$i["stock_sku"]?>] <?=$i["stock_name"]?></div>
    <div class="text-<?=$low?"danger":"secondary"?>">Min : <?=$i["stock_low"]?> <?=$i["stock_unit"]?></div>
    <div class="text-<?=$low?"danger":"secondary"?>">Now : <?=$i["stock_qty"]?> <?=$i["stock_unit"]?></div>
  </div>
  <?php if ($low) { ?>
  <span class="badge bg-danger">LOW</span>
  <?php } else { ?>
  <span class="badge bg-primary">OK</span>
  <?php } ?>
</li>
<?php }} else { echo "<li class='list-group-item'>No items on the watch list.</li>"; } ?>
</ul>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>