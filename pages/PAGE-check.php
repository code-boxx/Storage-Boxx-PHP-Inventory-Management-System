<?php require PATH_PAGES . "TEMPLATE-top.php"; ?>
<!-- (A) SCRIPTS -->
<link rel="stylesheet" href="<?=URL_PUBLIC?>autocomplete.css"/>
<script src="<?=URL_PUBLIC?>autocomplete.js"></script>
<script src="<?=URL_PUBLIC?>admin-check.js"></script>

<!-- (B) CHECK FORM -->
<h1>ITEM CHECK</h1>
<form class="standard" autocomplete="off" onsubmit="return check.show()">
  <label for="mvt-sku">SKU (Enter or Scan)</label>
  <input type="text" id="mvt-sku" required autofocus/>
  <input type="submit" value="Check"/>
</form>

<!-- (C) ITEM + HISTORY-->
<div id="mvt-item"></div>
<div id="mvt-history"></div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>