<?php
// (A) GET ITEM
$edit = isset($_POST["sku"]) && $_POST["sku"]!="";
if ($edit) { $item = $_CORE->autoCall("Inventory", "get"); }

// (B) ITEM FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> ITEM</h3>
<form onsubmit="return inv.save()">
  <div class="bg-white border p-4 mb-3">
    <div class="form-floating mb-1">
      <input type="hidden" id="inv-osku" value="<?=$edit?$item["stock_sku"]:""?>">
      <input type="text" class="form-control" id="inv-sku" required value="<?=$edit?$item["stock_sku"]:""?>">
      <label>SKU</label>
    </div>
    <span class="text-secondary" onclick="inv.randomSKU()">[Random SKU]</span>

    <div class="form-floating my-4">
      <input type="text" id="inv-name" class="form-control" required value="<?=$edit?$item["stock_name"]:""?>">
      <label>Item Name</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" id="inv-desc" class="form-control" value="<?=$edit?$item["stock_desc"]:""?>">
      <label>Item Description</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" class="form-control" id="inv-unit" list="inv-units" required value="<?=$edit?$item["stock_unit"]:""?>">
      <label>Unit of Measurement</label>
      <datalist id="inv-units">
        <option value="BAG"> <option value="BIN"> <option value="BOX">
        <option value="CAN"> <option value="CAS"> <option value="CNT">
        <option value="CRT"> <option value="CSK"> <option value="CTN">
        <option value="PCS"> <option value="PKG"> <option value="ROL">
      </datalist>
    </div>

    <?php if (!$edit) { ?>
    <div class="form-floating mb-4">
      <input type="number" step="0.01" class="form-control" id="inv-stock" required>
      <label>Current Stock Level</label>
    </div>
    <?php } ?>

    <div class="form-floating mb-1">
      <input type="number" step="0.01" class="form-control" id="inv-low" required value="<?=$edit?$item["stock_low"]:""?>">
      <label>Stock Level Watch</label>
    </div>
    <div class="text-secondary">
      * Enter "0" if you don't want to monitor this item.
      Enter any quantity more than 0 to monitor on the dashboard.
    </div>
  </div>

  <input type="button" class="col btn btn-danger" value="Back" onclick="cb.page(1)">
  <input type="submit" class="col btn btn-primary" value="Save">
</form>