<?php require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) SCRIPTS -->
<link rel="stylesheet" href="<?=URL_PUBLIC?>autocomplete.css"/>
<script src="<?=URL_PUBLIC?>autocomplete.js"></script>
<script src="<?=URL_PUBLIC?>admin-movement.js"></script>

<!-- (B) INVENTORY MOVEMENT -->
<h1>STOCK MOVEMENT</h1>
<form class="standard" autocomplete="off" onsubmit="return move.save()">
  <label for="mvt-direction">Direction</label>
  <select id="mvt-direction">
    <option value="I">Stock In (Receive)</option>
    <option value="O">Stock Out (Release)</option>
    <option value="T">Stock Take (Audit)</option>
  </select>
  <label for="mvt-qty">Quantity</label>
  <input type='number' id="mvt-qty" step='0.01' value='1.00' required/>
  <label for="mvt-notes">Notes (If Any)</label>
  <textarea id="mvt-notes"></textarea>
  <label for="mvt-sku">SKU (Enter or Scan)</label>
  <input type="text" id="mvt-sku" required autofocus/>
  <input type="submit" class="blue" value="Save"/>
</form>

<!-- (C) UPDATE RESULT -->
<div id="mvt-result" ></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>