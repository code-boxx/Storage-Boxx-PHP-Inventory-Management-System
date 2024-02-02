<?php
// (A) PAGE META
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-home.js", "defer"]
]];

// (B) HTML PAGE
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (B1) PUSH NOTIFICATIONS -->
<script>var cbvapid="<?=PUSH_PUBLIC?>";</script>
<div class="fw-bold text-danger mb-2">PUSH NOTIFICATIONS</div>
<div class="bg-white border p-3 mb-4" id="push-stat">Initializing...</div>

<!-- (B2) WATCH LIST -->
<div class="fw-bold text-danger mb-2">ITEMS WATCH LIST</div>
<div class="mb-4"><?php
  $_CORE->load("Report");
  $items = $_CORE->Report->getMonitor();
  if (is_array($items)) { foreach ($items as $i) {
  $low = $i["item_qty"] <= $i["item_low"]; ?>
  <div class="d-flex align-items-center bg-white border px-3 py-1 mb-1<?=$low?" text-danger":""?>" onclick="inv.sup('<?=$i["item_sku"]?>')">
    <div class="pe-4">
      <i class="ico icon-<?=$low?"cross":"checkmark"?>"></i>
    </div>
    <div class="flex-grow-1">
      <div class="fw-bold">[<?=$i["item_sku"]?>] <?=$i["item_name"]?></div>
      <div>Min : <?=$i["item_low"]?> <?=$i["item_unit"]?></div>
    </div>
    <div class="text-center">
      <div class="display-6"><?=$i["item_qty"]?></div>
      <div><?=$i["item_unit"]?></div>
    </div>
  </div>
  <?php }} else { echo '<div class="bg-white border p-3">No items on the watch list.</div>'; } 
?></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>