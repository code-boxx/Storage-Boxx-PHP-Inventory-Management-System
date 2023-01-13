<?php
// (A) GET ITEM
$edit = isset($_POST["sku"]) && $_POST["sku"]!="";
if ($edit) { $item = $_CORE->autoCall("Inventory", "get"); }

// (B) ITEM FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> ITEM</h3>
<form onsubmit="return inv.save()">
  <div class="bg-white border p-4 mb-3">
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text mi">qr_code</span>
      </div>
      <input type="hidden" id="inv-osku" value="<?=$edit?$item["stock_sku"]:""?>">
      <input type="text" class="form-control" id="inv-sku" required value="<?=$edit?$item["stock_sku"]:""?>" placeholder="SKU">
    </div>
    <div class="p-1 mb-3" onclick="inv.randomSKU()">[Random SKU]</div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">inventory_2</span>
      </div>
      <input type="text" id="inv-name" class="form-control" required value="<?=$edit?$item["stock_name"]:""?>" placeholder="Item Name">
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">description</span>
      </div>
      <input type="text" id="inv-desc" class="form-control" value="<?=$edit?$item["stock_desc"]:""?>" placeholder="Description">
    </div>

    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">straighten</span>
      </div>
      <input type="text" class="form-control" id="inv-unit" list="inv-units" required value="<?=$edit?$item["stock_unit"]:""?>" placeholder="Unit of Measurement">
      <datalist id="inv-units">
        <option value="BAG"> <option value="BIN"> <option value="BOX">
        <option value="CAN"> <option value="CAS"> <option value="CNT">
        <option value="CRT"> <option value="CSK"> <option value="CTN">
        <option value="PCS"> <option value="PKG"> <option value="ROL">
      </datalist>
    </div>

    <?php if (!$edit) { ?>
      <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text mi">published_with_changes</span>
      </div>
      <input type="number" step="0.01" class="form-control" id="inv-stock" required placeholder="Current stock level">
    </div>
    <?php } ?>

    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text mi">notifications</span>
      </div>
      <input type="number" step="0.01" class="form-control" id="inv-low" required value="<?=$edit?$item["stock_low"]:""?>" placeholder="Stock level watch">
    </div>
    <div class="mt-2 text-secondary">
      * Enter "0" if you don't want to monitor this item.
      Enter any quantity more than 0 to monitor on the dashboard.
    </div>
  </div>

  <input type="button" class="col btn btn-danger" value="Back" onclick="cb.page(0)">
  <input type="submit" class="col btn btn-primary" value="Save">
</form>