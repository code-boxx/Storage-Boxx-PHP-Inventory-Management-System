<?php
// (A) GET ITEM
$edit = isset($_POST["sku"]) && $_POST["sku"]!="";
if ($edit) {
  $item = $_CORE->autoCall("Inventory", "get");
}

// (B) ITEM FORM ?>
<form class="col-md-6 offset-md-3 bg-light border p-4" onsubmit="return inv.save()">
  <h4><?=$edit?"EDIT":"ADD"?> ITEM</h4>

  <div class="mb-4">
    <label for="inv-sku" class="form-label">SKU</label>
    <input type="hidden" id="inv-osku" value="<?=$edit?$item["stock_sku"]:""?>"/>
    <input type="text" class="form-control" id="inv-sku" required value="<?=$edit?$item["stock_sku"]:""?>"/>
    <div class="p-1" onclick="inv.randomSKU()">[Random SKU]</div>
  </div>

  <div class="mb-4">
    <label for="inv-name" class="form-label">Name</label>
    <input type="text" id="inv-name" class="form-control" required value="<?=$edit?$item["stock_name"]:""?>"/>
  </div>

  <div class="mb-4">
    <label for="inv-desc" class="form-label">Description</label>
    <input type="text" id="inv-desc" class="form-control" value="<?=$edit?$item["stock_desc"]:""?>"/>
  </div>

  <div class="mb-4">
    <label for="inv-unit" class="form-label">Unit of Measurement</label>
    <input type="text" class="form-control" id="inv-unit" required value="<?=$edit?$item["stock_unit"]:""?>"/>
    <div class="p-1">
      <span onclick="inv.unit('PC')">[PC]</span>
      <span onclick="inv.unit('EA')">[EA]</span>
      <span onclick="inv.unit('BX')">[BX]</span>
      <span onclick="inv.unit('CS')">[CS]</span>
      <span onclick="inv.unit('PL')">[PL]</span>
    </div>
  </div>

  <input type="button" class="col btn btn-danger btn-lg" value="Back" onclick="sb.page(1)"/>
  <input type="submit" class="col btn btn-primary btn-lg" value="Save"/>
</form>
