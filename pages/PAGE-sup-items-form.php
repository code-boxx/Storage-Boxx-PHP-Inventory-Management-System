<?php
// (A) GET ITEM
$edit = isset($_POST["sku"]) && $_POST["sku"]!="";
if ($edit) { $item = $_CORE->autoCall("Suppliers", "getItem"); }

// (B) ITEM FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> ITEM</h3>
<form onsubmit="return items.save()">
  <div class="bg-white border p-4 mb-3">
    <div class="form-floating mb-4">
      <input type="hidden" id="item-osku" value="<?=$edit?$item["item_sku"]:""?>">
      <input type="text" class="form-control" id="item-sku" required value="<?=$edit?$item["item_sku"]:""?>">
      <label>Item SKU</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" id="item-ssku" class="form-control" value="<?=$edit?$item["sup_sku"]:""?>">
      <label>Supplier SKU (if different)</label>
    </div>

    <div class="form-floating">
      <input type="number" step="0.01" id="item-price" class="form-control" required value="<?=$edit?$item["unit_price"]:""?>">
      <label>Unit Price</label>
    </div>
  </div>

  <button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="cb.page(2)">
    <i class="ico-sm icon-undo2"></i> Back
  </button>
  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-checkmark"></i> Save
  </button>
</form>