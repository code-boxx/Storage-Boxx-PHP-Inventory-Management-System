<?php
// (A) GET BATCHES
$batches = $_CORE->autoCall("Move", "getAllB");

// (B) DRAW BATCHES LIST
if (is_array($batches)) { foreach ($batches as $b) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="flex-grow-1">
    <strong>[<?=$b["item_sku"]?>] <?=$b["item_name"]?></strong><br>
    <small><?=$b["batch_name"]?> - <?=$b["batch_qty"]?> <?=$b["item_unit"]?></small><br>
    <small>EXPIRE: <?=$b["ex"]?></small>
  </div>
  <div class="dropdown">
    <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li class="dropdown-item" onclick="batch.addEdit('<?=$b["item_sku"]?>', '<?=$b["batch_name"]?>')">
        <i class="text-secondary ico-sm icon-pencil"></i> Edit
      </li>
      <li class="dropdown-item" onclick="check.go('<?=$b["item_sku"]?>', '<?=$b["batch_name"]?>')">
        <i class="text-secondary ico-sm icon-history"></i> History
      </li>
      <li class="dropdown-item" onclick="batch.qr('<?=$b["item_sku"]?>', '<?=$b["batch_name"]?>')">
        <i class="text-secondary ico-sm icon-qrcode"></i> QR Code
      </li>
      <li class="dropdown-item" onclick="batch.nfcShow('<?=$b["item_sku"]?>', '<?=$b["batch_name"]?>')">
        <i class="text-secondary ico-sm icon-feed"></i> NFC Tag
      </li>
      <li class="dropdown-item text-warning" onclick="batch.del('<?=$b["item_sku"]?>', '<?=$b["batch_name"]?>')">
        <i class="ico-sm icon-bin2"></i> Delete
      </li>
    </ul>
  </div>
</div>
<?php }} else { echo "No batches found."; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("batch.goToPage");