<?php
// (A) GET "WATCHED ITEMS"
$_CORE->load("Inventory");
$items = $_CORE->Inventory->getMonitor();

// (B) DASHBOARD
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-home.js", "defer"],
  ["s", HOST_ASSETS."PAGE-home-inv.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (B1) PUSH NOTIFICATIONS -->
<div id="push-stat" class="alert alert-danger d-none">Test</div>
<h3 class="mb-1">ITEMS WATCH LIST</h3>
<div class="mb-3 text-secondary">* click on an item to check the suppliers</div>

<!-- (B2) WATCH LIST -->
<div class="zebra my-4">
<?php if (is_array($items)) { foreach ($items as $i) {
$low = $i["stock_qty"] <= $i["stock_low"]; ?>
<div class="d-flex align-items-center border p-2<?=$low?" text-danger":""?>" onclick="inv.sup('<?=$i["stock_sku"]?>')">
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
</div>
<?php }} else { echo "No items on the watch list."; } ?>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>