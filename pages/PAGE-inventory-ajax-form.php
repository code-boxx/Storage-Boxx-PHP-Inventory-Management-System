<?php
// (A) GET ITEM
$edit = isset($_POST['sku']) && $_POST['sku']!="";
if ($edit) {
  $_CORE->load("Inventory");
  $item = $_CORE->Inventory->get($_POST['sku']);
}

// (B) ITEM FORM ?>
<h1><?=$edit?"EDIT":"ADD"?> ITEM</h1>
<form class="standard" onsubmit="return inv.save();">
  <label for="inv-sku">SKU</label>
  <input type="text" id="inv-sku" required value="<?=$edit?$item['stock_sku']:""?>"/>
  <input type="hidden" id="inv-osku" value="<?=$edit?$item['stock_sku']:""?>"/>
  <div class="bar" onclick="inv.randomSKU()">[Random SKU]</div>
  <label for="inv-name">Name</label>
  <input type="text" id="inv-name"  required value="<?=$edit?$item['stock_name']:""?>"/>
  <label for="inv-desc">Description</label>
  <input type="text" id="inv-desc" value="<?=$edit?$item['stock_desc']:""?>"/>
  <label for="inv-unit">Unit of Measurement</label>
  <input type="text" id="inv-unit" required value="<?=$edit?$item['stock_unit']:""?>"/>
  <div class="bar">
    <span onclick="inv.unit('PC')">[PC]</span>
    <span onclick="inv.unit('EA')">[EA]</span>
    <span onclick="inv.unit('BX')">[BX]</span>
    <span onclick="inv.unit('CS')">[CS]</span>
    <span onclick="inv.unit('PL')">[PL]</span>
  </div>
  <input type="submit" value="Save"/>
  <input type="button" onclick="common.page('A')" value="Back"/>
</form>