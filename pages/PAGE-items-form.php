<?php
// (A) GET ITEM
$edit = isset($_POST["sku"]) && $_POST["sku"]!="";
if ($edit) { $item = $_CORE->autoCall("Items", "get"); }

// (B) ITEM FORM ?>
<h3 class="m-0"><?=$edit?"EDIT":"ADD"?> ITEM</h3>
<div class="text-danger fw-bold mb-3">
  <?php if ($edit) { ?>
  * If you change the SKU/name/unit - 
  All movement history, supplier items, and orders will also be updated.
  This can potentially mess things up, do so with extra care.
  <?php } ?>
</div>

<form onsubmit="return item.save()">
  <div class="bg-white border p-4 mb-3">
    <div class="form-floating mb-1">
      <input type="hidden" id="item-osku" value="<?=$edit?$item["item_sku"]:""?>">
      <input type="text" class="form-control" id="item-sku" required value="<?=$edit?$item["item_sku"]:""?>">
      <label>SKU</label>
    </div>
    <span class="text-secondary" onclick="item.randomSKU()">[Random SKU]</span>

    <div class="form-floating my-4">
      <input type="text" id="item-name" class="form-control" required value="<?=$edit?$item["item_name"]:""?>">
      <label>Item Name</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" id="item-desc" class="form-control" value="<?=$edit?$item["item_desc"]:""?>">
      <label>Item Description</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" class="form-control" id="item-unit" list="item-units" required value="<?=$edit?$item["item_unit"]:""?>">
      <label>Unit of Measurement</label>
      <datalist id="item-units">
        <option value="BAG"> <option value="BIN"> <option value="BOX">
        <option value="CAN"> <option value="CAS"> <option value="CNT">
        <option value="CRT"> <option value="CSK"> <option value="CTN">
        <option value="PCS"> <option value="PKG"> <option value="ROL">
      </datalist>
    </div>

    <div class="form-floating mb-4">
      <input type="number" step="0.01" class="form-control" id="item-price" required value="<?=$edit?$item["item_price"]:""?>">
      <label>Unit Price</label>
    </div>

    <div class="form-floating mb-1">
      <input type="number" step="0.01" class="form-control" id="item-low" required value="<?=$edit?$item["item_low"]:""?>">
      <label>Stock Level Watch</label>
    </div>
    <div class="text-secondary">
      * Enter "0" if you don't want to monitor this item.
      Enter any quantity more than 0 to monitor on the dashboard.
    </div>
  </div>

  <button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="cb.page(1)">
    <i class="ico-sm icon-undo2"></i> Back
  </button>
  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-checkmark"></i> Save
  </button>
</form>