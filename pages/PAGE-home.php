<?php
// (A) GET "WATCHED ITEMS"
$_CORE->load("Inventory");
$items = $_CORE->Inventory->getMonitor();

// (B) DASHBOARD
$_PMETA = ["load" => [["s", HOST_ASSETS."PAGE-home.js", "defer"]]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (B1) PUSH NOTIFICATIONS -->
<h5>PUSH NOTIFICATIONS</h5>
<div id="push-stat"></div>

<!-- (B2) WATCH LIST -->
<h5>ITEMS WATCH LIST</h5>
<ul class="list-group">
<?php if (is_array($items)) { foreach ($items as $i) {
$low = $i["stock_qty"] <= $i["stock_low"]; ?>
<li class="list-group-item d-flex align-items-center text-<?=$low?"danger":"secondary"?>">
  <div class="flex-grow-1">
    <div class="fw-bold">[<?=$i["stock_sku"]?>] <?=$i["stock_name"]?></div>
    <div>
      <?php if ($low) { ?>
      <span class="badge bg-danger">LOW</span>
      <?php } else { ?>
      <span class="badge bg-primary">OK</span>
      <?php } ?>
      Min : <?=$i["stock_low"]?> <?=$i["stock_unit"]?>
    </div>
  </div>
  <div class="ms-1 text-center">
    <div class="display-6"><?=$i["stock_qty"]?></div>
    <div><?=$i["stock_unit"]?></div>
  </div>
</li>
<?php }} else { echo "<li class='list-group-item'>No items on the watch list.</li>"; } ?>
</ul>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>